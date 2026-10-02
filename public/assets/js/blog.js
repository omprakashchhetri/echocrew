/* EchoCrew blog: reading progress, copy-link, contents highlight. No dependencies. */
(function () {
  'use strict';

  var bar = document.querySelector('.eb-progress');
  var article = document.querySelector('[data-eb-article]');

  if (bar && article) {
    var ticking = false;
    var update = function () {
      var r = article.getBoundingClientRect();
      var total = r.height - window.innerHeight * 0.6;
      var done = Math.min(1, Math.max(0, (-r.top + window.innerHeight * 0.3) / Math.max(total, 1)));
      bar.style.transform = 'scaleX(' + done.toFixed(3) + ')';
      ticking = false;
    };
    window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(update); } }, { passive: true });
    update();
  }

  var copy = document.querySelector('[data-eb-copy]');
  if (copy) {
    copy.addEventListener('click', function () {
      var url = copy.getAttribute('data-eb-copy');
      var done = function () { var old = copy.textContent; copy.textContent = 'Done'; setTimeout(function () { copy.textContent = old; }, 1600); };
      if (navigator.clipboard) { navigator.clipboard.writeText(url).then(done); }
      else { var t = document.createElement('input'); t.value = url; document.body.appendChild(t); t.select(); document.execCommand('copy'); t.remove(); done(); }
    });
  }

  var links = [].slice.call(document.querySelectorAll('.eb-toc a'));
  if (links.length && 'IntersectionObserver' in window) {
    var map = {};
    links.forEach(function (a) { map[a.getAttribute('href').slice(1)] = a; });
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          links.forEach(function (a) { a.classList.remove('is-active'); });
          var a = map[e.target.id]; if (a) a.classList.add('is-active');
        }
      });
    }, { rootMargin: '-20% 0px -70% 0px' });
    Object.keys(map).forEach(function (id) { var el = document.getElementById(id); if (el) io.observe(el); });
  }
})();
