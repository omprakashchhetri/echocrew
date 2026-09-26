<?php
/**
 * Structured data. One @graph, one script tag — cleaner for Google than
 * several competing blocks.
 *
 * Expects (all optional):
 *   $schemaService  array  service data from Config\Seo::$services
 *   $schemaFaq      array  [[question, answer], ...]
 *   $breadcrumbs    array  ['Label' => 'https://...']
 */
$seo   = config('Seo');
$base  = rtrim(base_url(), '/') . '/';
$orgId = $base . '#organization';

$graph = [];

/* ---------- Organization / LocalBusiness ---------- */
$org = [
    '@type'       => $seo->nap['locality'] !== '' ? ['Organization', 'ProfessionalService'] : 'Organization',
    '@id'         => $orgId,
    'name'        => $seo->brand,
    'legalName'   => $seo->nap['legalName'],
    'url'         => $base,
    'logo'        => [
        '@type' => 'ImageObject',
        'url'   => base_url('assets/img/ec/echocrew-logo.png'),
    ],
    'image'       => base_url('assets/img/ec/echocrew-logo.png'),
    'description' => 'EchoCrew builds custom software, websites, CRM systems, management software, e-commerce platforms, API integrations, workflow automation and AI-powered solutions for businesses.',
    'knowsAbout'  => [
        'Custom software development', 'Web application development', 'CRM development',
        'School management software', 'E-commerce development', 'API integration',
        'Workflow automation', 'Artificial intelligence integration',
        'Legacy system modernisation', 'Technical SEO',
    ],
];

$address = array_filter([
    '@type'           => 'PostalAddress',
    'streetAddress'   => $seo->nap['street'],
    'addressLocality' => $seo->nap['locality'],
    'addressRegion'   => $seo->nap['region'],
    'postalCode'      => $seo->nap['postalCode'],
    'addressCountry'  => $seo->nap['country'],
]);
if (count($address) > 2) {
    $org['address'] = $address;
}

if ($seo->nap['telephone'] !== '') {
    $org['telephone'] = $seo->nap['telephone'];
}
if ($seo->nap['email'] !== '') {
    $org['email'] = $seo->nap['email'];
}
if ($seo->nap['latitude'] !== '' && $seo->nap['longitude'] !== '') {
    $org['geo'] = [
        '@type'     => 'GeoCoordinates',
        'latitude'  => $seo->nap['latitude'],
        'longitude' => $seo->nap['longitude'],
    ];
}
if ($seo->nap['googleMapUrl'] !== '') {
    $org['hasMap'] = $seo->nap['googleMapUrl'];
}
if ($seo->profiles !== []) {
    $org['sameAs'] = array_values($seo->profiles);
}
$org['areaServed'] = array_map(
    static fn (string $area): array => ['@type' => 'AdministrativeArea', 'name' => $area],
    $seo->areasServed
);
if ($seo->nap['telephone'] !== '') {
    $org['openingHoursSpecification'] = [[
        '@type'     => 'OpeningHoursSpecification',
        'dayOfWeek' => $seo->openingHours['days'],
        'opens'     => $seo->openingHours['opens'],
        'closes'    => $seo->openingHours['closes'],
    ]];
}

/* Everything EchoCrew offers — helps Google map queries to the right page. */
$org['hasOfferCatalog'] = [
    '@type'          => 'OfferCatalog',
    'name'           => 'Software and digital services',
    'itemListElement' => array_values(array_map(
        static fn (string $slug, array $svc): array => [
            '@type'       => 'Offer',
            'itemOffered' => [
                '@type'       => 'Service',
                'name'        => $svc['name'],
                'url'         => base_url('services/' . $slug),
                'description' => $svc['desc'],
            ],
        ],
        array_keys($seo->services),
        $seo->services
    )),
];
$graph[] = $org;

/* ---------- WebSite ---------- */
$graph[] = [
    '@type'     => 'WebSite',
    '@id'       => $base . '#website',
    'url'       => $base,
    'name'      => $seo->brand,
    'publisher' => ['@id' => $orgId],
    'inLanguage' => 'en-IN',
];

/* ---------- WebPage ---------- */
$graph[] = array_filter([
    '@type'      => 'WebPage',
    '@id'        => current_url() . '#webpage',
    'url'        => current_url(),
    'name'       => $title ?? ($seo->brand . ' | ' . $seo->tagline),
    'description' => $description ?? null,
    'isPartOf'   => ['@id' => $base . '#website'],
    'about'      => ['@id' => $orgId],
    'inLanguage' => 'en-IN',
]);

/* ---------- Service (service landing pages only) ---------- */
if (! empty($schemaService)) {
    $graph[] = array_filter([
        '@type'       => 'Service',
        '@id'         => current_url() . '#service',
        'name'        => $schemaService['name'],
        'description' => $schemaService['desc'],
        'url'         => current_url(),
        'provider'    => ['@id' => $orgId],
        'serviceType' => $schemaService['name'],
        'areaServed'  => array_map(
            static fn (string $area): array => ['@type' => 'AdministrativeArea', 'name' => $area],
            $seo->areasServed
        ),
    ]);
}

/* ---------- FAQPage ---------- */
if (! empty($schemaFaq)) {
    $graph[] = [
        '@type'      => 'FAQPage',
        '@id'        => current_url() . '#faq',
        'mainEntity' => array_map(static fn (array $qa): array => [
            '@type'          => 'Question',
            'name'           => $qa[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa[1]],
        ], $schemaFaq),
    ];
}

/* ---------- Breadcrumbs ---------- */
if (! empty($breadcrumbs)) {
    $i = 0;
    $graph[] = [
        '@type'           => 'BreadcrumbList',
        'itemListElement' => array_map(static function (string $url, string $label) use (&$i): array {
            return ['@type' => 'ListItem', 'position' => ++$i, 'name' => $label, 'item' => $url];
        }, array_values($breadcrumbs), array_keys($breadcrumbs)),
    ];
}
?>
<script type="application/ld+json"><?= json_encode(
    ['@context' => 'https://schema.org', '@graph' => $graph],
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
) ?></script>
