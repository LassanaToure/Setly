(() => {
  'use strict';

  const debounce = (fn, delay) => {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), delay);
    };
  };

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.reservation');
    if (!form) return;

    const studioId = form.getAttribute('data-studio');
    const recap = document.getElementById('recap-prix');
    const champKm = document.getElementById('champ-km');

    const setRecap = (key, value) => {
      const el = recap.querySelector(`[data-recap="${key}"]`);
      if (el) el.textContent = value;
    };

    const update = async () => {
      const duree = form.querySelector('[name="duree_heures"]').value || '0';
      const modeEl = form.querySelector('[name="mode_retrait"]:checked');
      const mode = modeEl ? modeEl.value : 'sur_place';
      const kmField = form.querySelector('[name="livraison_km"]');
      const km = kmField ? kmField.value : '0';

      if (champKm) champKm.hidden = (mode !== 'livraison');

      const params = new URLSearchParams();
      params.set('studio', studioId);
      params.set('duree', duree);
      params.set('mode', mode);
      params.set('km', km);

      form.querySelectorAll('.materiel-qty').forEach((input) => {
        const qty = parseInt(input.value, 10) || 0;
        if (qty > 0) {
          const id = input.name.replace(/[^0-9]/g, '');
          params.set(`materiel[${id}]`, qty);
        }
      });

      try {
        const response = await fetch(`/api/tarif?${params.toString()}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await response.json();
        if (data.error) return;
        setRecap('prix_studio', data.prix_studio);
        setRecap('prix_materiel', data.prix_materiel);
        setRecap('livraison', data.livraison);
        setRecap('total', data.total);
        const redLine = recap.querySelector('[data-recap-reduction]');
        if (redLine) {
          redLine.hidden = !data.a_reduction;
          setRecap('reduction', `−${data.reduction}`);
        }
      } catch (e) {
        // reseau indisponible : on conserve le dernier recapitulatif affiche
      }
    };

    const debounced = debounce(update, 250);
    form.addEventListener('input', debounced);
    form.addEventListener('change', update);
    update();
  });
})();
