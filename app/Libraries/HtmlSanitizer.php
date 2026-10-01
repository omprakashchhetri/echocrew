<?php

namespace App\Libraries;

use DOMComment;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * Allow-list HTML sanitizer for blog post bodies.
 *
 * Unknown tags are unwrapped (children kept), dangerous tags are removed with
 * their contents, and attributes/URL schemes are restricted.
 */
class HtmlSanitizer
{
    /** @var array<string, list<string>> allowed tag => allowed attributes */
    private const ALLOWED = [
        'p' => [], 'br' => [], 'hr' => [], 'h2' => [], 'h3' => [], 'h4' => [],
        'ul' => [], 'ol' => [], 'li' => [], 'blockquote' => [], 'pre' => [], 'code' => [],
        'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [],
        'figure' => [], 'figcaption' => [],
        'table' => [], 'thead' => [], 'tbody' => [], 'tr' => [], 'th' => [], 'td' => [],
        'a'   => ['href', 'title'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
    ];

    private const DROP_WITH_CONTENT = [
        'script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'textarea',
        'select', 'svg', 'math', 'noscript', 'template', 'head', 'title', 'meta', 'link', 'base',
    ];

    private const HEADING_MAP = ['h1' => 'h2', 'h5' => 'h4', 'h6' => 'h4'];

    private const URL_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    public function clean(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        $doc  = new DOMDocument('1.0', 'UTF-8');
        $prev = libxml_use_internal_errors(true);
        $doc->loadHTML(
            '<?xml encoding="utf-8" ?><div>' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $root = $doc->getElementsByTagName('div')->item(0);
        if (! $root) {
            return '';
        }

        $this->walk($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return trim($out);
    }

    private function walk(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMText) {
                continue;
            }
            if (! $child instanceof DOMElement) {
                $node->removeChild($child); // comments, processing instructions, CDATA

                continue;
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                $node->removeChild($child);

                continue;
            }

            if (isset(self::HEADING_MAP[$tag])) {
                $child = $this->rename($child, self::HEADING_MAP[$tag]);
                $tag   = strtolower($child->tagName);
            }

            $this->walk($child);

            if (! isset(self::ALLOWED[$tag])) {
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);

                continue;
            }

            $this->cleanAttributes($child, $tag);
        }
    }

    private function rename(DOMElement $el, string $newTag): DOMElement
    {
        $new = $el->ownerDocument->createElement($newTag);
        while ($el->firstChild) {
            $new->appendChild($el->firstChild);
        }
        $el->parentNode->replaceChild($new, $el);

        return $new;
    }

    private function cleanAttributes(DOMElement $el, string $tag): void
    {
        $allowed = self::ALLOWED[$tag];

        foreach (iterator_to_array($el->attributes) as $attr) {
            $name = strtolower($attr->name);

            if (! in_array($name, $allowed, true)) {
                $el->removeAttribute($attr->name);

                continue;
            }

            if (($name === 'href' || $name === 'src') && ! $this->safeUrl($attr->value)) {
                $el->removeAttribute($attr->name);
            }

            if (($name === 'width' || $name === 'height') && ! ctype_digit($attr->value)) {
                $el->removeAttribute($attr->name);
            }
        }

        if ($tag === 'a' && $el->hasAttribute('href') && preg_match('#^https?:#i', $el->getAttribute('href'))) {
            $el->setAttribute('rel', 'noopener');
        }
    }

    private function safeUrl(string $url): bool
    {
        $url = preg_replace('/[\x00-\x20\x7f]+/', '', $url) ?? '';

        if ($url === '') {
            return false;
        }

        if (preg_match('/^([a-z][a-z0-9+.\-]*):/i', $url, $m)) {
            return in_array(strtolower($m[1]), self::URL_SCHEMES, true);
        }

        return true; // relative, fragment or protocol-relative
    }
}
