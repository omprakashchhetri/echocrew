/* EchoCrew site behaviour.
 * Interaction (menu, tabs, form) works without GSAP.
 * Motion needs GSAP + ScrollTrigger from plugin.js and is skipped entirely
 * under prefers-reduced-motion.
 */
(function () {
  'use strict';

  var doc = document;
  var root = doc.documentElement;
  var $ = function (sel, ctx) { return (ctx || doc).querySelector(sel); };
  var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || doc).querySelectorAll(sel)); };

  /* ---------- Mobile menu ---------- */
  var burger = $('.ec-nav__burger');
  var mobileNav = $('#ec-mobilenav');

  function setMenu(open) {
    root.classList.toggle('ec-menu-open', open);
    if (burger) {
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
      burger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    }
    if (mobileNav) {
      if (open) { mobileNav.removeAttribute('inert'); } else { mobileNav.setAttribute('inert', ''); }
    }
  }

  if (burger && mobileNav) {
    setMenu(false);
    burger.addEventListener('click', function () { setMenu(!root.classList.contains('ec-menu-open')); });
    $$('a', mobileNav).forEach(function (a) { a.addEventListener('click', function () { setMenu(false); }); });
    doc.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && root.classList.contains('ec-menu-open')) { setMenu(false); burger.focus(); }
    });
    window.matchMedia('(min-width: 1101px)').addEventListener('change', function (e) { if (e.matches) setMenu(false); });
  }

  /* ---------- Problem board (tabs) ---------- */
  $$('[data-tabs]').forEach(function (group) {
    var tabs = $$('[role="tab"]', group);
    var panels = tabs.map(function (t) { return doc.getElementById(t.getAttribute('aria-controls')); });

    function select(i, focus) {
      tabs.forEach(function (t, j) {
        var on = i === j;
        t.setAttribute('aria-selected', on ? 'true' : 'false');
        t.tabIndex = on ? 0 : -1;
        if (panels[j]) {
          panels[j].hidden = !on;
          panels[j].classList.toggle('is-in', on);
        }
      });
      if (focus) tabs[i].focus();
    }

    tabs.forEach(function (t, i) {
      t.addEventListener('click', function () {
        select(i, false);
        // Stacked layout: the answer sits below the notes, so bring it into view.
        if (panels[i] && window.matchMedia('(max-width: 900px)').matches) panels[i].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
      });
      t.addEventListener('keydown', function (e) {
        var next = null;
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') next = (i + 1) % tabs.length;
        if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') next = (i - 1 + tabs.length) % tabs.length;
        if (e.key === 'Home') next = 0;
        if (e.key === 'End') next = tabs.length - 1;
        if (next !== null) { e.preventDefault(); select(next, true); }
      });
    });
  });

  /* ---------- Forms ---------- */
  $$('form[data-enquiry]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var firstBad = null;
      $$('[required]', form).forEach(function (field) {
        var bad = !field.value.trim() || (field.type === 'email' && !/^\S+@\S+\.\S+$/.test(field.value.trim()));
        field.setAttribute('aria-invalid', bad ? 'true' : 'false');
        if (bad && !firstBad) firstBad = field;
      });
      if (firstBad) { e.preventDefault(); firstBad.focus(); return; }
      var btn = $('button[type="submit"]', form);
      if (btn) { btn.disabled = true; btn.firstChild.nodeValue = 'Sending '; }
    });
  });

  var flash = $('[data-scroll-to]');
  if (flash) {
    window.addEventListener('load', function () { flash.scrollIntoView({ block: 'center' }); });
  }

  /* ---------- Nav state: fallback without GSAP ---------- */
  var nav = $('.ec-nav');
  var hasGsap = !!(window.gsap && window.ScrollTrigger);

  if (!hasGsap) {
    root.classList.remove('ec-motion');
    if (nav && 'IntersectionObserver' in window) {
      var sentinel = doc.createElement('div');
      sentinel.style.cssText = 'position:absolute;top:0;height:12px;width:1px';
      doc.body.prepend(sentinel);
      new IntersectionObserver(function (entries) {
        nav.classList.toggle('is-scrolled', !entries[0].isIntersecting);
      }).observe(sentinel);
    }
    return;
  }

  var gsap = window.gsap;
  var ScrollTrigger = window.ScrollTrigger;
  gsap.registerPlugin(ScrollTrigger);
  if (window.SplitText) gsap.registerPlugin(window.SplitText);

  /* Nav: solid after 12px, hides going down, returns going up. */
  if (nav) {
    ScrollTrigger.create({
      start: 0,
      end: 'max',
      onUpdate: function (self) {
        var y = self.scroll();
        nav.classList.toggle('is-scrolled', y > 12);
        if (root.classList.contains('ec-menu-open')) return;
        nav.classList.toggle('is-hidden', self.direction === 1 && y > 480);
      }
    });
  }

  /* Highlight the in-page section in the nav (home page only). */
  $$('.ec-nav__links a[href*="#"]').forEach(function (link) {
    var id = link.getAttribute('href').split('#')[1];
    var target = id && doc.getElementById(id);
    if (!target) return;
    ScrollTrigger.create({
      trigger: target,
      start: 'top 45%',
      end: 'bottom 45%',
      onToggle: function (self) { link.classList.toggle('is-active', self.isActive); }
    });
  });

  var mm = gsap.matchMedia();

  mm.add('(prefers-reduced-motion: no-preference)', function () {
    root.classList.add('ec-motion', 'ec-motion-ready');

    var isDesktop = window.matchMedia('(min-width: 1024px)').matches;

    /* Process: pinned horizontal pan. Created first so every trigger below
       measures the page with the pin spacer already in place. */
    var process = $('[data-pan]');
    var track = process && $('.ec-process__track', process);
    if (process && track && isDesktop) {
      var distance = function () { return Math.max(0, track.scrollWidth - window.innerWidth); };
      gsap.to(track, {
        x: function () { return -distance(); },
        ease: 'none',
        scrollTrigger: {
          trigger: process,
          start: 'top top',
          end: function () { return '+=' + distance(); },
          pin: true,
          scrub: 0.8,
          invalidateOnRefresh: true
        }
      });
    }

    /* Hero entrance: words rise in, then the underline gets drawn. */
    var heroWords = $$('.ec-hero__title .ec-w');
    if (heroWords.length) {
      gsap.to(heroWords, { opacity: 1, y: 0, rotate: 0, duration: 1.1, ease: 'expo.out', stagger: 0.055, delay: 0.1 });
    }

    /* Hand-drawn strokes. */
    $$('.ec-scribble path, .ec-ring path').forEach(function (path) {
      var len = Math.ceil(path.getTotalLength());
      path.style.setProperty('--len', len);
      var inHero = !!path.closest('.ec-hero');
      gsap.to(path, {
        strokeDashoffset: 0,
        duration: inHero ? 0.9 : 1.2,
        ease: 'power2.inOut',
        delay: inHero ? 0.85 : 0,
        scrollTrigger: inHero ? null : { trigger: path.closest('svg'), start: 'top 80%', once: true }
      });
    });

    /* Section headings: word-by-word rise. */
    if (window.SplitText) {
      $$('[data-split]').forEach(function (el) {
        var split = new window.SplitText(el, { type: 'words', wordsClass: 'ec-sw' });
        gsap.from(split.words, {
          yPercent: 70, opacity: 0, rotate: 1.5, duration: 1, ease: 'expo.out', stagger: 0.035,
          scrollTrigger: { trigger: el, start: 'top 86%', once: true }
        });
      });

      /* Manifesto: words fill in as you read down. */
      $$('[data-fill]').forEach(function (el) {
        var split = new window.SplitText(el, { type: 'words', wordsClass: 'ec-sw' });
        gsap.fromTo(split.words, { opacity: 0.14 }, {
          opacity: 1, ease: 'none', stagger: 0.1,
          scrollTrigger: { trigger: el, start: 'top 78%', end: 'bottom 42%', scrub: true }
        });
      });
    }

    /* Generic reveal. */
    ScrollTrigger.batch('[data-reveal]', {
      start: 'top 88%',
      once: true,
      onEnter: function (batch) {
        gsap.to(batch, { opacity: 1, y: 0, duration: 1, ease: 'expo.out', stagger: 0.08, overwrite: true });
      }
    });

    /* Photo frames open up as they arrive. */
    $$('[data-unveil]').forEach(function (el) {
      gsap.to(el, {
        clipPath: 'inset(0% 0% 0% 0% round 18px)', duration: 1.4, ease: 'expo.out',
        scrollTrigger: { trigger: el, start: 'top 85%', once: true }
      });
    });

    /* Layer parallax: data-parallax="N" moves the element from -N to +N px
       while its section crosses the viewport. Negative N reverses. */
    $$('[data-parallax]').forEach(function (el) {
      var amt = parseFloat(el.getAttribute('data-parallax')) || 0;
      if (!isDesktop) amt = amt * 0.5;
      gsap.fromTo(el, { y: -amt }, {
        y: amt, ease: 'none',
        scrollTrigger: { trigger: el.closest('section, [data-scope]') || el, start: 'top bottom', end: 'bottom top', scrub: true }
      });
    });

    /* Image-inside-frame parallax. */
    $$('.ec-media img').forEach(function (img) {
      gsap.fromTo(img, { yPercent: -7 }, {
        yPercent: 7, ease: 'none',
        scrollTrigger: { trigger: img.parentElement, start: 'top bottom', end: 'bottom top', scrub: true }
      });
    });

    /* Ticker rows travel in opposite directions with the scroll. */
    $$('[data-ticker]').forEach(function (row) {
      var dir = parseFloat(row.getAttribute('data-ticker')) || 1;
      gsap.fromTo(row, { xPercent: dir > 0 ? -22 : 0 }, {
        xPercent: dir > 0 ? 0 : -22, ease: 'none',
        scrollTrigger: { trigger: row.parentElement, start: 'top bottom', end: 'bottom top', scrub: 0.6 }
      });
    });

    /* Horizontal drift for the Build / Launch / Grow words. */
    $$('[data-drift]').forEach(function (el) {
      var amt = parseFloat(el.getAttribute('data-drift')) || 0;
      if (!isDesktop) amt = amt * 0.4;
      gsap.fromTo(el, { x: -amt }, {
        x: amt, ease: 'none',
        scrollTrigger: { trigger: el.parentElement, start: 'top bottom', end: 'bottom top', scrub: true }
      });
    });

    /* Echo layers on the work covers separate as the card scrolls. */
    $$('.ec-echo').forEach(function (echo) {
      var layers = $$('span', echo);
      layers.forEach(function (layer, i) {
        var depth = (layers.length - 1 - i) * 0.09;
        if (!depth) return;
        gsap.fromTo(layer, { yPercent: depth * 110, xPercent: -depth * 14 }, {
          yPercent: -depth * 110, xPercent: depth * 14, ease: 'none',
          scrollTrigger: { trigger: echo.closest('.ec-work__cover'), start: 'top bottom', end: 'bottom top', scrub: true }
        });
      });
    });

    /* Legacy strata: new layers settle onto the kept system. */
    $$('[data-strata]').forEach(function (list) {
      var plates = $$('li', list);
      var tl = gsap.timeline({
        scrollTrigger: { trigger: list, start: 'top 80%', end: 'center 45%', scrub: 0.6 }
      });
      plates.forEach(function (plate, i) {
        if (i === 0) return;
        tl.from(plate, { y: -60 - i * 14, opacity: 0, ease: 'power2.out', duration: 1 }, (i - 1) * 0.55);
      });
    });

    /* Manifesto flow: each step underline fills in turn. */
    $$('[data-flow]').forEach(function (list) {
      var items = $$('li', list);
      ScrollTrigger.create({
        trigger: list, start: 'top 75%', end: 'bottom 50%', scrub: true,
        onUpdate: function (self) {
          var p = self.progress * items.length;
          items.forEach(function (li, i) { li.style.setProperty('--fill', Math.min(1, Math.max(0, p - i)).toFixed(3)); });
        }
      });
    });

    /* Automation chain: the line runs and nodes switch on. */
    $$('[data-chain]').forEach(function (chain) {
      var nodes = $$('li', chain);
      ScrollTrigger.create({
        trigger: chain, start: 'top 78%', end: 'bottom 40%', scrub: true,
        onUpdate: function (self) {
          var p = self.progress;
          chain.style.setProperty('--p', p.toFixed(3));
          nodes.forEach(function (n, i) { n.classList.toggle('is-on', p >= i / (nodes.length - 1) - 0.001); });
        }
      });
    });

    /* Keycaps drop in like someone typing. */
    $$('[data-keys]').forEach(function (list) {
      gsap.from($$('.ec-key', list), {
        y: -18, opacity: 0, duration: 0.5, ease: 'back.out(2.2)',
        stagger: { each: 0.035, from: 'random' },
        scrollTrigger: { trigger: list, start: 'top 85%', once: true }
      });
    });

    return function () { root.classList.remove('ec-motion'); };
  });

  /* No motion allowed: make sure nothing stays hidden. */
  mm.add('(prefers-reduced-motion: reduce)', function () {
    root.classList.remove('ec-motion');
    $$('.ec-chain').forEach(function (c) { c.style.setProperty('--p', 1); $$('li', c).forEach(function (n) { n.classList.add('is-on'); }); });
    $$('[data-flow] li').forEach(function (li) { li.style.setProperty('--fill', 1); });
  });

  /* Fonts and lazy images change heights; re-measure once they settle. */
  if (doc.fonts && doc.fonts.ready) doc.fonts.ready.then(function () { ScrollTrigger.refresh(); });
  window.addEventListener('load', function () { ScrollTrigger.refresh(); });
})();
