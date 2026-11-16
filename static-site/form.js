/*
 * Support request form for the plain-HTML site.
 * Does the same as the WordPress version (same checks, same messages, same
 * confirmation screen), but sends the request through Web3Forms, which emails it to you.
 *
 * SETUP: get a free access key at https://web3forms.com and paste it below. Sign up with
 * your own inbox, not support@mugstudio.nl (that address forwards through Cloudflare Email
 * Routing; if the forwarding breaks, form mail would get lost too). Requests are sent to the
 * inbox you sign up with; "Reply" goes to the person who filled in the form.
 * The key is not a secret: Web3Forms made it to sit in a public web page.
 */
(function () {
  'use strict';

  var ACCESS_KEY = 'ac854a3c-956a-4f3f-8328-3c91275e794a';
  var ENDPOINT = 'https://api.web3forms.com/submit';
  var SUPPORT_EMAIL = 'support@mugstudio.nl';
  var MIN = 20;
  var MAX = 1500;

  var wrap = document.getElementById('contact');
  var form = document.getElementById('support-form');
  if (!wrap || !form) return;

  var configured = ACCESS_KEY.indexOf('PASTE-') !== 0;
  // On your own computer (python3 -m http.server) the form pretends to send, so you can test the flow.
  var isLocal = /^(localhost|127\.0\.0\.1|\[::1\]|)$/.test(location.hostname);

  function $(sel, root) { return (root || document).querySelector(sel); }
  function esc(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
  function isEmail(s) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(s); }

  // ---------------------------------------------------------- read the form --
  function values() {
    var game = $('input[name="game"]:checked', form);
    var topic = $('#f-topic');
    var device = $('#f-device');
    return {
      game: game ? game.value : '',
      gameLabel: game ? game.parentNode.textContent.trim() : '',
      topic: topic.value,
      topicLabel: topic.value ? topic.options[topic.selectedIndex].text : '',
      device: device.value,
      deviceLabel: device.value ? device.options[device.selectedIndex].text : '',
      message: $('#f-message').value.trim(),
      name: $('#f-name').value.trim(),
      email: $('#f-email').value.trim(),
      consent: $('#f-consent').checked,
      trap: $('#f-website') ? $('#f-website').value : ''
    };
  }

  // Same checks and the same words as the WordPress version.
  function validate(v) {
    var e = {};
    if (!v.game) e.game = 'Choose the game your question is about.';
    if (!v.topic) e.topic = 'Choose what your question is about.';
    if (!v.device) e.device = 'Choose the device you play on, or "Does not matter for my question".';
    var len = v.message.length;
    if (len === 0) e.message = 'Describe your question or problem.';
    else if (len < MIN) e.message = 'Write at least ' + MIN + ' characters so we understand the problem (you have ' + len + '). For example: what did you do, and what happened?';
    else if (len > MAX) e.message = 'Keep it under ' + MAX + ' characters (you have ' + len + ').';
    if (v.name.length > 80) e.your_name = 'Keep your name under 80 characters.';
    if (!v.email) e.email = 'Fill in your email address so we can reply, for example name@mail.nl.';
    else if (!isEmail(v.email)) e.email = 'This email address is not complete. Use the form name@mail.nl.';
    if (!v.consent) e.consent = 'Tick the box so we are allowed to use your email address to reply.';
    return e;
  }

  // ------------------------------------------------------------ errors --
  // Where each message goes: the element it is placed in front of.
  var anchors = {
    game: function () { return $('.choices', form); },
    topic: function () { return $('#f-topic'); },
    device: function () { return $('#f-device'); },
    message: function () { return $('#f-message'); },
    your_name: function () { return $('#f-name'); },
    email: function () { return $('#f-email'); },
    consent: function () { return $('label.check', form); }
  };
  var controls = {
    game: function () { return form.querySelectorAll('input[name="game"]'); },
    topic: function () { return [$('#f-topic')]; },
    device: function () { return [$('#f-device')]; },
    message: function () { return [$('#f-message')]; },
    your_name: function () { return [$('#f-name')]; },
    email: function () { return [$('#f-email')]; },
    consent: function () { return [$('#f-consent')]; }
  };

  function errorEl(key, msg) {
    var p = document.createElement('p');
    p.className = 'field-error';
    p.id = 'e-' + key;
    p.innerHTML = '<span aria-hidden="true">!</span> ' + esc(msg);
    return p;
  }

  function clearErrors() {
    form.querySelectorAll('.field-error').forEach(function (el) { el.remove(); });
    form.querySelectorAll('.has-error').forEach(function (el) { el.classList.remove('has-error'); });
    form.querySelectorAll('[aria-invalid]').forEach(function (el) { el.removeAttribute('aria-invalid'); });
    form.querySelectorAll('[data-describedby]').forEach(function (el) {
      el.setAttribute('aria-describedby', el.getAttribute('data-describedby'));
      el.removeAttribute('data-describedby');
    });
    form.querySelectorAll('[aria-describedby^="e-"]').forEach(function (el) {
      if (!el.hasAttribute('data-describedby')) el.removeAttribute('aria-describedby');
    });
  }

  function showErrors(errors) {
    clearErrors();
    Object.keys(errors).forEach(function (key) {
      if (key === 'form') {
        form.insertBefore(errorEl('form', errors.form), form.firstChild);
        return;
      }
      var anchor = anchors[key]();
      var field = anchor.closest('.field');
      field.classList.add('has-error');
      anchor.parentNode.insertBefore(errorEl(key, errors[key]), anchor);
      var targets = key === 'game' ? [field] : controls[key]();
      Array.prototype.forEach.call(targets, function (el) {
        if (key !== 'game') el.setAttribute('aria-invalid', 'true');
        var before = el.getAttribute('aria-describedby');
        if (before) el.setAttribute('data-describedby', before);
        el.setAttribute('aria-describedby', (before ? before + ' ' : '') + 'e-' + key);
      });
    });
    // Jump to the first mistake and put the cursor there.
    var first = $('.field.has-error', form);
    if (first) {
      var input = first.querySelector('input:not([type="hidden"]), select, textarea');
      if (input) input.focus({ preventScroll: true });
      first.scrollIntoView({ block: 'center', behavior: 'instant' });
    } else {
      var msg = $('#e-form', form);
      msg.setAttribute('role', 'alert');
      msg.scrollIntoView({ block: 'center', behavior: 'instant' });
    }
  }

  // Remove a field's red state as soon as the user changes it.
  form.addEventListener('input', function (e) {
    var field = e.target.closest('.field.has-error');
    if (!field) return;
    field.classList.remove('has-error');
    var err = field.querySelector('.field-error');
    if (err) err.remove();
    field.querySelectorAll('[aria-invalid]').forEach(function (el) { el.removeAttribute('aria-invalid'); });
  });

  // ---------------------------------------------------- character counter --
  var message = $('#f-message');
  var counter = $('#c-message');
  function updateCounter() {
    var len = message.value.trim().length;
    if (len === 0) {
      counter.textContent = 'Minimum ' + MIN + ' characters.';
      counter.className = 'counter';
    } else if (len < MIN) {
      counter.textContent = len + ' / ' + MAX + ' characters. ' + (MIN - len) + ' more needed.';
      counter.className = 'counter counter--low';
    } else {
      counter.textContent = len + ' / ' + MAX + ' characters.';
      counter.className = 'counter counter--ok';
    }
  }
  if (message && counter) {
    message.addEventListener('input', updateCounter);
    updateCounter();
  }

  // ------------------------------------------------------------- sending --
  var button = $('button[type="submit"]', form);
  var label = button.textContent;

  function busy(on) {
    if (on) {
      form.dataset.busy = '1';
      button.setAttribute('aria-disabled', 'true');
      button.classList.add('is-busy');
      button.innerHTML = '<span class="spinner" aria-hidden="true"></span>' + esc(button.dataset.sending || 'Sending…');
    } else {
      delete form.dataset.busy;
      button.removeAttribute('aria-disabled');
      button.classList.remove('is-busy');
      button.textContent = label;
    }
  }

  function reference() {
    return 'MS-' + String(Math.floor(1000 + Math.random() * 9000));
  }

  function send(v, ref) {
    if (!configured) {
      if (isLocal) {
        console.info('[form] Test mode: nothing was sent. Paste a Web3Forms key in form.js to send for real.', v);
        return new Promise(function (resolve) { setTimeout(resolve, 700); });
      }
      return Promise.reject(new Error('not configured'));
    }
    return fetch(ENDPOINT, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({
        access_key: ACCESS_KEY,
        subject: '[' + ref + '] ' + v.gameLabel + ': ' + v.topicLabel,
        from_name: 'MugStudio website',
        replyto: v.email,
        Reference: ref,
        Game: v.gameLabel,
        Topic: v.topicLabel,
        Device: v.deviceLabel,
        Name: v.name || '-',
        Email: v.email,
        Message: v.message
      })
    }).then(function (r) {
      return r.json().then(function (data) {
        if (!r.ok || !data.success) throw new Error(data.message || 'Send failed');
      });
    });
  }

  // Same confirmation screen as the WordPress version.
  function showDone(v, ref) {
    wrap.innerHTML =
      '<div class="card done" tabindex="-1" id="done" role="status">' +
        '<div class="done__icon" aria-hidden="true">&#10003;</div>' +
        '<h2 id="contact-title">Thanks, we got your request!</h2>' +
        '<p>Your reference number is <strong class="ref">' + esc(ref) + '</strong>. ' +
        'We reply within 3 working days to <strong>' + esc(v.email) + '</strong>. ' +
        'Check your spam folder if you hear nothing.</p>' +
        '<dl class="summary">' +
          '<div><dt>Game</dt><dd>' + esc(v.gameLabel) + '</dd></div>' +
          '<div><dt>Topic</dt><dd>' + esc(v.topicLabel) + '</dd></div>' +
          '<div><dt>Device</dt><dd>' + esc(v.deviceLabel) + '</dd></div>' +
          '<div class="summary__wide"><dt>Your message</dt><dd>' + esc(v.message).replace(/\n/g, '<br>') + '</dd></div>' +
        '</dl>' +
        '<div class="btn-row">' +
          '<a class="btn" href="/#games">Back to the games</a>' +
          '<a class="btn btn--ghost" href="/support/#contact" id="send-another">Send another request</a>' +
        '</div>' +
      '</div>';
    $('#send-another').addEventListener('click', function (e) {
      e.preventDefault();
      location.hash = 'contact';
      location.reload();
    });
    var done = $('#done');
    done.focus({ preventScroll: true });
    done.scrollIntoView({ block: 'center', behavior: 'instant' });
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (form.dataset.busy) return;

    var v = values();
    var ref = reference();
    if (v.trap) { showDone(v, ref); return; } // spam bot: pretend it worked

    var errors = validate(v);
    if (Object.keys(errors).length) { showErrors(errors); return; }

    clearErrors();
    busy(true);
    send(v, ref).then(function () {
      showDone(v, ref);
    }).catch(function (err) {
      busy(false);
      showErrors({
        form: err.message === 'not configured'
          ? 'The form is not set up yet. Please email us at ' + SUPPORT_EMAIL + '.'
          : 'Sending did not work. Check your internet connection and press "Send request" again, or email us at ' + SUPPORT_EMAIL + '. Your text is still here.'
      });
    });
  });

  // Coming back with the browser's Back button: make the button usable again.
  window.addEventListener('pageshow', function (e) {
    if (e.persisted && form.dataset.busy) busy(false);
  });
})();
