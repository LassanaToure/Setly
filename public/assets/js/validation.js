(function () {
  'use strict';

  var PASSWORD_RE = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{10,}$/;
  var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  var PHONE_RE = /^[0-9 +().-]{6,20}$/;

  function messageFor(rule, param, form) {
    switch (rule) {
      case 'required': return 'Ce champ est obligatoire.';
      case 'email': return "L'adresse email n'est pas valide.";
      case 'password': return 'Au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.';
      case 'phone': return 'Numéro de téléphone invalide.';
      case 'min': return 'La valeur doit être supérieure ou égale à ' + param + '.';
      case 'max': return 'La valeur doit être inférieure ou égale à ' + param + '.';
      case 'match': return 'Les deux valeurs ne correspondent pas.';
      default: return 'Valeur invalide.';
    }
  }

  function checkRule(value, rule, param, form) {
    switch (rule) {
      case 'required': return value.trim() !== '';
      case 'email': return value === '' || EMAIL_RE.test(value);
      case 'password': return value === '' || PASSWORD_RE.test(value);
      case 'phone': return value === '' || PHONE_RE.test(value);
      case 'min': return value === '' || parseFloat(value) >= parseFloat(param);
      case 'max': return value === '' || parseFloat(value) <= parseFloat(param);
      case 'match':
        var other = form.querySelector('[name="' + param + '"]');
        return other ? value === other.value : true;
      default: return true;
    }
  }

  function fieldWrapper(input) {
    return input.closest('.field') || input.parentElement;
  }

  function errorNode(input, form) {
    var name = input.getAttribute('name');
    return form.querySelector('[data-error-for="' + name + '"]') ||
           fieldWrapper(input).querySelector('.field__error');
  }

  function validateField(input, form) {
    var rulesAttr = input.getAttribute('data-rule');
    if (!rulesAttr) return true;

    var value = input.value;
    if (input.type === 'radio') {
      var checked = form.querySelector('[name="' + input.name + '"]:checked');
      value = checked ? checked.value : '';
    }

    var rules = rulesAttr.split('|');
    var firstError = null;

    for (var i = 0; i < rules.length; i++) {
      var parts = rules[i].split(':');
      var rule = parts[0];
      var param = parts[1];
      if (!checkRule(value, rule, param, form)) {
        firstError = messageFor(rule, param, form);
        break;
      }
    }

    var wrapper = fieldWrapper(input);
    var errNode = errorNode(input, form);

    if (firstError) {
      wrapper.classList.add('has-error');
      input.setAttribute('aria-invalid', 'true');
      if (errNode) errNode.textContent = firstError;
      return false;
    }

    wrapper.classList.remove('has-error');
    input.removeAttribute('aria-invalid');
    if (errNode) errNode.textContent = '';
    return true;
  }

  function initForm(form) {
    var fields = form.querySelectorAll('[data-rule]');

    fields.forEach(function (input) {
      var evt = (input.tagName === 'SELECT' || input.type === 'radio') ? 'change' : 'blur';
      input.addEventListener(evt, function () { validateField(input, form); });

      input.addEventListener('input', function () {
        if (fieldWrapper(input).classList.contains('has-error')) {
          validateField(input, form);
        }
      });
    });

    form.addEventListener('submit', function (e) {
      var valid = true;
      var firstInvalid = null;
      fields.forEach(function (input) {
        if (!validateField(input, form)) {
          valid = false;
          if (!firstInvalid) firstInvalid = input;
        }
      });
      if (!valid) {
        e.preventDefault();
        if (firstInvalid) firstInvalid.focus();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-validate]').forEach(initForm);

    var toggle = document.querySelector('.nav-toggle');
    var nav = document.getElementById('nav-principal');
    if (toggle && nav) {
      toggle.addEventListener('click', function () {
        var open = nav.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    }

    document.querySelectorAll('[data-toggle]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var target = document.querySelector(btn.getAttribute('data-toggle'));
        if (target) target.hidden = !target.hidden;
      });
    });

    var mainPhoto = document.getElementById('photo-principale');
    document.querySelectorAll('.thumb').forEach(function (thumb) {
      thumb.addEventListener('click', function () {
        if (!mainPhoto) return;
        mainPhoto.src = thumb.getAttribute('data-src');
        document.querySelectorAll('.thumb').forEach(function (t) { t.classList.remove('is-active'); });
        thumb.classList.add('is-active');
      });
    });

    var statutSelect = document.querySelector('[data-statut-select]');
    var contactBox = document.querySelector('[data-contact-box]');
    if (statutSelect && contactBox) {
      var sync = function () { contactBox.hidden = statutSelect.value !== 'annulee'; };
      statutSelect.addEventListener('change', sync);
      sync();
    }
  });
})();
