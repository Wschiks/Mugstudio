/* Support request form: character counter, "Sending…" state, focus on errors / confirmation. */
(function () {
  'use strict';

  // After sending, or after a mistake, move focus there so keyboard and screen-reader users land in the right place.
  // On a mistake that is the first field with an error; its message is read out through aria-describedby.
  var firstError = document.querySelector('.field.has-error');
  var focusTarget = document.getElementById('done') ||
    (firstError && firstError.querySelector('input:not([type="hidden"]), select, textarea'));
  if (focusTarget) {
    focusTarget.focus({ preventScroll: true });
    (firstError || focusTarget).scrollIntoView({ block: 'center', behavior: 'instant' });
  }

  var form = document.getElementById('support-form');
  if (!form) return;

  // Character counter under the message.
  var message = document.getElementById('f-message');
  var counter = document.getElementById('c-message');
  if (message && counter) {
    var min = parseInt(counter.dataset.min, 10);
    var max = parseInt(counter.dataset.max, 10);
    var update = function () {
      var len = message.value.trim().length;
      if (len === 0) {
        counter.textContent = 'Minimum ' + min + ' characters.';
        counter.className = 'counter';
      } else if (len < min) {
        counter.textContent = len + ' / ' + max + ' characters. ' + (min - len) + ' more needed.';
        counter.className = 'counter counter--low';
      } else {
        counter.textContent = len + ' / ' + max + ' characters.';
        counter.className = 'counter counter--ok';
      }
    };
    message.addEventListener('input', update);
    update();
  }

  // Clear the red state of a field as soon as the user changes it.
  form.addEventListener('input', function (e) {
    var field = e.target.closest('.field.has-error');
    if (field) {
      field.classList.remove('has-error');
      var err = field.querySelector('.field-error');
      if (err) err.remove();
      field.querySelectorAll('[aria-invalid]').forEach(function (el) { el.removeAttribute('aria-invalid'); });
    }
  });

  // Show that something is happening, and stop double sending.
  form.addEventListener('submit', function (e) {
    var button = form.querySelector('button[type="submit"]');
    if (form.dataset.busy) {
      e.preventDefault();
      return;
    }
    form.dataset.busy = '1';
    button.setAttribute('aria-disabled', 'true');
    button.classList.add('is-busy');
    button.dataset.label = button.textContent;
    button.innerHTML = '<span class="spinner" aria-hidden="true"></span>' + button.dataset.sending;
  });

  // Coming back with the browser's Back button: make the button usable again.
  window.addEventListener('pageshow', function (e) {
    var button = form.querySelector('button[type="submit"]');
    if (e.persisted && form.dataset.busy) {
      delete form.dataset.busy;
      button.removeAttribute('aria-disabled');
      button.classList.remove('is-busy');
      button.textContent = button.dataset.label;
    }
  });
})();
