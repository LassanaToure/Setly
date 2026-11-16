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

  document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.reservation');
    if (!form) return;

    var studioId = form.getAttribute('data-studio');
    var recap = document.getElementById('recap-prix');
    var champKm = document.getElementById('champ-km');

    function setRecap(key, value) {
      var el = recap.querySelector('[data-recap="' + key + '"]');
      if (el) el.textContent = value;
    }

    function update() {
      var duree = form.querySelector('[name="duree_heures"]').value || '0';
      var modeEl = form.querySelector('[name="mode_retrait"]:checked');
      var mode = modeEl ? modeEl.value : 'sur_place';
      var km = form.querySelector('[name="livraison_km"]');
      km = km ? km.value : '0';

      if (champKm) champKm.hidden = (mode !== 'livraison');

      var params = new URLSearchParams();
      params.set('studio', studioId);
      params.set('duree', duree);
      params.set('mode', mode);
      params.set('km', km);

      form.querySelectorAll('.materiel-qty').forEach(function (input) {
        var qty = parseInt(input.value, 10) || 0;
        if (qty > 0) {
          var id = input.name.replace(/[^0-9]/g, '');
          params.set('materiel[' + id + ']', qty);
        }
      });

      fetch('/api/tarif?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.error) return;
          setRecap('prix_studio', data.prix_studio);
          setRecap('prix_materiel', data.prix_materiel);
          setRecap('livraison', data.livraison);
          setRecap('total', data.total);
          var redLine = recap.querySelector('[data-recap-reduction]');
          if (redLine) {
            redLine.hidden = !data.a_reduction;
            setRecap('reduction', '−' + data.reduction);
          }
        })
        .catch(function () {  });
    }

    var debounced = debounce(update, 250);
    form.addEventListener('input', debounced);
    form.addEventListener('change', update);
    update();
  });
})();
