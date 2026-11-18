(() => {
  'use strict';

  const PASSWORD_RE = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{10,}$/;
  const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const PHONE_RE = /^[0-9 +().-]{6,20}$/;

  const messageFor = (rule, param) => {
    switch (rule) {
      case 'required': return 'Ce champ est obligatoire.';
      case 'email': return "L'adresse email n'est pas valide.";
      case 'password': return 'Au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.';
      case 'phone': return 'Numéro de téléphone invalide.';
      case 'min': return `La valeur doit être supérieure ou égale à ${param}.`;
      case 'max': return `La valeur doit être inférieure ou égale à ${param}.`;
      case 'match': return 'Les deux valeurs ne correspondent pas.';
      default: return 'Valeur invalide.';
    }
  };

  const checkRule = (value, rule, param, form) => {
    switch (rule) {
      case 'required': return value.trim() !== '';
      case 'email': return value === '' || EMAIL_RE.test(value);
      case 'password': return value === '' || PASSWORD_RE.test(value);
      case 'phone': return value === '' || PHONE_RE.test(value);
      case 'min': return value === '' || parseFloat(value) >= parseFloat(param);
      case 'max': return value === '' || parseFloat(value) <= parseFloat(param);
      case 'match': {
        const other = form.querySelector(`[name="${param}"]`);
        return other ? value === other.value : true;
      }
      default: return true;
    }
  };

  const fieldWrapper = (input) => input.closest('.field') || input.parentElement;

  const errorNode = (input, form) => {
    const name = input.getAttribute('name');
    return form.querySelector(`[data-error-for="${name}"]`) ||
           fieldWrapper(input).querySelector('.field__error');
  };

  const validateField = (input, form) => {
    const rulesAttr = input.getAttribute('data-rule');
    if (!rulesAttr) return true;

    let value = input.value;
    if (input.type === 'radio') {
      const checked = form.querySelector(`[name="${input.name}"]:checked`);
      value = checked ? checked.value : '';
    }

    let firstError = null;
    for (const token of rulesAttr.split('|')) {
      const [rule, param] = token.split(':');
      if (!checkRule(value, rule, param, form)) {
        firstError = messageFor(rule, param);
        break;
      }
    }

    const wrapper = fieldWrapper(input);
    const errNode = errorNode(input, form);

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
  };

  const initForm = (form) => {
    const fields = form.querySelectorAll('[data-rule]');

    fields.forEach((input) => {
      const evt = (input.tagName === 'SELECT' || input.type === 'radio') ? 'change' : 'blur';
      input.addEventListener(evt, () => validateField(input, form));

      input.addEventListener('input', () => {
        if (fieldWrapper(input).classList.contains('has-error')) {
          validateField(input, form);
        }
      });
    });

    form.addEventListener('submit', (e) => {
      let valid = true;
      let firstInvalid = null;
      fields.forEach((input) => {
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
  };

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-validate]').forEach(initForm);

    const toggle = document.querySelector('.nav-toggle');
    const nav = document.getElementById('nav-principal');
    if (toggle && nav) {
      toggle.addEventListener('click', () => {
        const open = nav.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    }

    document.querySelectorAll('[data-toggle]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const target = document.querySelector(btn.getAttribute('data-toggle'));
        if (target) target.hidden = !target.hidden;
      });
    });

    const mainPhoto = document.getElementById('photo-principale');
    document.querySelectorAll('.thumb').forEach((thumb) => {
      thumb.addEventListener('click', () => {
        if (!mainPhoto) return;
        mainPhoto.src = thumb.getAttribute('data-src');
        document.querySelectorAll('.thumb').forEach((t) => t.classList.remove('is-active'));
        thumb.classList.add('is-active');
      });
    });

    const statutSelect = document.querySelector('[data-statut-select]');
    const contactBox = document.querySelector('[data-contact-box]');
    if (statutSelect && contactBox) {
      const sync = () => { contactBox.hidden = statutSelect.value !== 'annulee'; };
      statutSelect.addEventListener('change', sync);
      sync();
    }
  });
})();
