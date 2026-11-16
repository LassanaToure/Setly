(function () {
  'use strict';

  var COLORS = ['#e0a33d', '#1f7a8c', '#5b4fb0', '#2e7d52', '#c0392b', '#2763b3'];

  function drawBarChart(canvas, labels, values, unit) {
    if (!canvas || !canvas.getContext) return;
    var ctx = canvas.getContext('2d');
    var dpr = window.devicePixelRatio || 1;
    var cssW = canvas.clientWidth || 400;
    var cssH = canvas.getAttribute('height') ? parseInt(canvas.getAttribute('height'), 10) : 220;
    canvas.width = cssW * dpr;
    canvas.height = cssH * dpr;
    ctx.scale(dpr, dpr);
    ctx.clearRect(0, 0, cssW, cssH);

    if (!values.length) {
      ctx.fillStyle = '#6b6b76';
      ctx.font = '14px Segoe UI, sans-serif';
      ctx.fillText('Aucune donnée', 10, 24);
      return;
    }

    var pad = { top: 16, right: 12, bottom: 44, left: 44 };
    var chartW = cssW - pad.left - pad.right;
    var chartH = cssH - pad.top - pad.bottom;
    var max = Math.max.apply(null, values) || 1;
    var niceMax = Math.ceil(max * 1.1);
    var barW = chartW / values.length * 0.6;
    var gap = chartW / values.length;

    ctx.strokeStyle = '#e4e0d6';
    ctx.fillStyle = '#6b6b76';
    ctx.font = '11px Segoe UI, sans-serif';
    ctx.lineWidth = 1;
    for (var g = 0; g <= 4; g++) {
      var y = pad.top + chartH - (chartH * g / 4);
      ctx.beginPath();
      ctx.moveTo(pad.left, y);
      ctx.lineTo(pad.left + chartW, y);
      ctx.stroke();
      ctx.fillText(Math.round(niceMax * g / 4), 6, y + 4);
    }

    values.forEach(function (v, i) {
      var h = chartH * (v / niceMax);
      var x = pad.left + gap * i + (gap - barW) / 2;
      var y = pad.top + chartH - h;
      ctx.fillStyle = COLORS[i % COLORS.length];
      ctx.beginPath();
      ctx.rect(x, y, barW, h);
      ctx.fill();

      ctx.fillStyle = '#16161a';
      ctx.font = 'bold 12px Segoe UI, sans-serif';
      ctx.textAlign = 'center';
      ctx.fillText(unit === '€' ? v.toLocaleString('fr-FR') : v, x + barW / 2, y - 6);

      ctx.fillStyle = '#3a3a42';
      ctx.font = '11px Segoe UI, sans-serif';
      var label = labels[i].length > 14 ? labels[i].slice(0, 13) + '…' : labels[i];
      ctx.fillText(label, x + barW / 2, pad.top + chartH + 16);
      ctx.textAlign = 'left';
    });
  }

  function render(data) {
    drawBarChart(document.getElementById('chart-reservations'), data.labels, data.reservations, '');
    drawBarChart(document.getElementById('chart-ca'), data.labels, data.ca, '€');

    if (data.indicateurs) {
      var total = document.querySelector('[data-kpi="total"]');
      var top = document.querySelector('[data-kpi="top"]');
      if (total) total.textContent = data.indicateurs.total_reservations;
      if (top) top.textContent = data.indicateurs.studio_top;
    }
  }

  function fetchAndRender(params) {
    fetch('/admin/statistiques/data?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.json(); })
      .then(render)
      .catch(function () {});
  }

  document.addEventListener('DOMContentLoaded', function () {
    var wrap = document.querySelector('[data-stats]');
    if (!wrap) return;

    var form = document.getElementById('filtres-stats');
    var current = new URLSearchParams({
      du: wrap.getAttribute('data-du') || '',
      au: wrap.getAttribute('data-au') || '',
      studio: wrap.getAttribute('data-studio') || ''
    });
    fetchAndRender(current.toString());

    if (form) {
      form.addEventListener('change', function () {
        var params = new URLSearchParams(new FormData(form)).toString();
        fetchAndRender(params);

        history.replaceState(null, '', '/admin/statistiques?' + params);
      });
    }

    window.addEventListener('resize', function () {
      fetchAndRender(current.toString());
    });
  });
})();
