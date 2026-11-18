(() => {
  'use strict';

  const debounce = (fn, delay) => {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), delay);
    };
  };

  const loadInto = async (url, target) => {
    target.setAttribute('aria-busy', 'true');
    try {
      const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      target.innerHTML = await response.text();
    } finally {
      target.removeAttribute('aria-busy');
    }
  };

  document.addEventListener('DOMContentLoaded', () => {

    const catForm = document.getElementById('filtres-studios');
    const catTarget = document.getElementById('studios-resultats');
    if (catForm && catTarget) {
      const runCat = () => {
        const params = new URLSearchParams(new FormData(catForm)).toString();
        loadInto(`/studios/filtrer?${params}`, catTarget);
      };
      catForm.addEventListener('input', debounce(runCat, 250));
      catForm.addEventListener('change', runCat);
      catForm.addEventListener('submit', (e) => { e.preventDefault(); runCat(); });
      const resetBtn = document.getElementById('reset-filtres');
      if (resetBtn) resetBtn.addEventListener('click', () => setTimeout(runCat, 0));
    }

    const tabWrap = document.getElementById('filtre-reservations');
    const resaTarget = document.getElementById('reservations-liste');
    if (tabWrap && resaTarget) {
      tabWrap.querySelectorAll('.tab').forEach((tab) => {
        tab.addEventListener('click', () => {
          tabWrap.querySelectorAll('.tab').forEach((t) => t.classList.remove('is-active'));
          tab.classList.add('is-active');
          const filtre = tab.getAttribute('data-filtre');
          loadInto(`/mes-reservations/filtrer?filtre=${encodeURIComponent(filtre)}`, resaTarget);
        });
      });
    }

    const boForm = document.getElementById('filtres-bo-reservations');
    const boTarget = document.getElementById('bo-reservations-table');
    if (boForm && boTarget) {
      const runBo = () => {
        const params = new URLSearchParams(new FormData(boForm)).toString();
        loadInto(`/back-office/reservations/filtrer?${params}`, boTarget);
      };
      boForm.addEventListener('input', debounce(runBo, 300));
      boForm.addEventListener('change', runBo);
      boForm.addEventListener('submit', (e) => { e.preventDefault(); runBo(); });
    }
  });
})();
