(function () {
  'use strict';

  function debounce(fn, delay) {
    var t;
    return function () {
      var ctx = this, args = arguments;
      clearTimeout(t);
      t = setTimeout(function () { fn.apply(ctx, args); }, delay);
    };
  }

  function loadInto(url, target) {
    target.setAttribute('aria-busy', 'true');
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.text(); })
      .then(function (html) {
        target.innerHTML = html;
        target.removeAttribute('aria-busy');
      })
      .catch(function () {
        target.removeAttribute('aria-busy');
      });
  }

  document.addEventListener('DOMContentLoaded', function () {

    var catForm = document.getElementById('filtres-studios');
    var catTarget = document.getElementById('studios-resultats');
    if (catForm && catTarget) {
      var runCat = function () {
        var params = new URLSearchParams(new FormData(catForm)).toString();
        loadInto('/studios/filtrer?' + params, catTarget);
      };
      catForm.addEventListener('input', debounce(runCat, 250));
      catForm.addEventListener('change', runCat);
      catForm.addEventListener('submit', function (e) { e.preventDefault(); runCat(); });
      var resetBtn = document.getElementById('reset-filtres');
      if (resetBtn) resetBtn.addEventListener('click', function () { setTimeout(runCat, 0); });
    }

    var tabWrap = document.getElementById('filtre-reservations');
    var resaTarget = document.getElementById('reservations-liste');
    if (tabWrap && resaTarget) {
      tabWrap.querySelectorAll('.tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
          tabWrap.querySelectorAll('.tab').forEach(function (t) { t.classList.remove('is-active'); });
          tab.classList.add('is-active');
          var filtre = tab.getAttribute('data-filtre');
          loadInto('/mes-reservations/filtrer?filtre=' + encodeURIComponent(filtre), resaTarget);
        });
      });
    }

    var boForm = document.getElementById('filtres-bo-reservations');
    var boTarget = document.getElementById('bo-reservations-table');
    if (boForm && boTarget) {
      var runBo = function () {
        var params = new URLSearchParams(new FormData(boForm)).toString();
        loadInto('/back-office/reservations/filtrer?' + params, boTarget);
      };
      boForm.addEventListener('input', debounce(runBo, 300));
      boForm.addEventListener('change', runBo);
      boForm.addEventListener('submit', function (e) { e.preventDefault(); runBo(); });
    }
  });
})();
