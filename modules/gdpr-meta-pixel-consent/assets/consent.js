(function () {
  'use strict';

  var cfg = window.GMPPConsentConfig || {};
  var COOKIE_NAME = cfg.consentCookie || 'gmpp_meta_marketing_consent';
  var LEGACY_COOKIE = cfg.legacyCookie || 'gmpp_consent';
  var PYS_COOKIE = cfg.pysConsentCookie || 'pys_consent';
  var META_COOKIES = ['_fbp', '_fbc', 'pys_fb_event_id', 'fbp', 'fbc'];
  var state = { banner: null, lastFocused: null };

  function escHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function safeMessage(html) {
    var wrapper = document.createElement('div');
    wrapper.innerHTML = String(html || '');
    wrapper.querySelectorAll('script, iframe, object, embed, form, input, button, style').forEach(function (node) { node.remove(); });
    wrapper.querySelectorAll('*').forEach(function (node) {
      Array.prototype.slice.call(node.attributes).forEach(function (attr) {
        var value = String(attr.value || '').trim().toLowerCase();
        if (/^on/i.test(attr.name) || value.indexOf('javascript:') === 0) {
          node.removeAttribute(attr.name);
        }
      });
    });
    return wrapper.innerHTML;
  }

  function maxAge() {
    var days = parseInt(cfg.retentionDays, 10);
    if (!days || days < 1) days = 180;
    return days * 24 * 60 * 60;
  }

  function cookieSecure() {
    return location.protocol === 'https:' ? '; Secure' : '';
  }

  function getCookie(name) {
    var escaped = name.replace(/[.$?*|{}()[\]\\/+^]/g, '\\$&');
    var match = document.cookie.match(new RegExp('(?:^|; )' + escaped + '=([^;]*)'));
    return match ? decodeURIComponent(match[1]) : null;
  }

  function setCookie(name, value, seconds, domain) {
    var cookie = name + '=' + encodeURIComponent(value) + '; Max-Age=' + seconds + '; Path=/; SameSite=Lax' + cookieSecure();
    if (domain) cookie = name + '=' + encodeURIComponent(value) + '; Max-Age=' + seconds + '; Path=/; Domain=' + domain + '; SameSite=Lax' + cookieSecure();
    document.cookie = cookie;
  }

  function expireCookie(name, domain) {
    document.cookie = name + '=; Max-Age=0; Path=/; SameSite=Lax' + cookieSecure();
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/' + cookieSecure();
    if (domain) {
      document.cookie = name + '=; Max-Age=0; Path=/; Domain=' + domain + '; SameSite=Lax' + cookieSecure();
      document.cookie = name + '=; Max-Age=0; Path=/; Domain=.' + domain + '; SameSite=Lax' + cookieSecure();
      document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/; Domain=' + domain + cookieSecure();
      document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/; Domain=.' + domain + cookieSecure();
    }
  }

  function domainCandidates() {
    var host = location.hostname;
    var parts = host.split('.');
    var domains = [host];
    if (parts.length >= 2) domains.push(parts.slice(-2).join('.'));
    if (parts.length >= 3) domains.push(parts.slice(-3).join('.'));
    return domains.filter(function (value, index, arr) { return value && arr.indexOf(value) === index; });
  }

  function encodeBase64Utf8(value) {
    try { return btoa(unescape(encodeURIComponent(value))); } catch (e) { return btoa(value); }
  }

  function pysConsentPayload(marketing) {
    return {
      facebook: !!marketing,
      ga: true,
      google_ads: true,
      tiktok: true,
      bing: true,
      pinterest: true,
      gtm: true,
      reddit: true
    };
  }

  function writePysConsent(marketing) {
    var payload = encodeBase64Utf8(JSON.stringify(pysConsentPayload(marketing)));
    setCookie(PYS_COOKIE, payload, maxAge());
  }

  function readLegacyConsent() {
    var raw = null;
    try { raw = window.localStorage.getItem(LEGACY_COOKIE); } catch (e) {}
    if (!raw) raw = getCookie(LEGACY_COOKIE);
    if (!raw) return null;
    try {
      var data = JSON.parse(raw);
      if (typeof data.marketing === 'boolean') return data.marketing ? 'yes' : 'no';
    } catch (e) {}
    return null;
  }

  function readConsent() {
    var value = getCookie(COOKIE_NAME);
    if (value === 'yes' || value === 'no') return value;
    return readLegacyConsent();
  }

  function hasChoice() {
    var value = readConsent();
    return value === 'yes' || value === 'no';
  }

  function hasMarketingConsent() {
    return readConsent() === 'yes';
  }

  function removeMetaCookies() {
    var domains = domainCandidates();
    META_COOKIES.forEach(function (name) {
      expireCookie(name);
      domains.forEach(function (domain) { expireCookie(name, domain); });
    });
  }

  function revokeRuntimePixel() {
    if (typeof window.fbq === 'function') {
      try { window.fbq('consent', 'revoke'); } catch (e) {}
    }
    removeMetaCookies();
  }

  function writeConsent(marketing) {
    var value = marketing ? 'yes' : 'no';
    setCookie(COOKIE_NAME, value, maxAge());
    writePysConsent(marketing);

    var detail = {
      necessary: true,
      marketing: !!marketing,
      source: 'gdpr-meta-pixel-consent-pixelyoursite',
      version: cfg.version || '2.0.0',
      ts: new Date().toISOString()
    };

    try { window.localStorage.setItem(COOKIE_NAME, JSON.stringify(detail)); } catch (e) {}
    document.dispatchEvent(new CustomEvent('gmppConsentChanged', { detail: detail }));

    if (!marketing) revokeRuntimePixel();
    return detail;
  }

  function reloadIfNeeded() {
    if (cfg.reloadOnChange !== false) {
      window.location.reload();
    }
  }

  function focusableElements(root) {
    return Array.prototype.slice.call(root.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])'));
  }

  function trapFocus(e) {
    if (!state.banner || e.key !== 'Tab') return;
    var els = focusableElements(state.banner);
    if (!els.length) return;
    var first = els[0];
    var last = els[els.length - 1];
    if (e.shiftKey && document.activeElement === first) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault();
      first.focus();
    }
  }

  function closeBanner() {
    if (!state.banner) return;
    document.removeEventListener('keydown', trapFocus);
    state.banner.remove();
    state.banner = null;
    if (state.lastFocused && typeof state.lastFocused.focus === 'function') {
      try { state.lastFocused.focus(); } catch (e) {}
    }
  }

  function makeToggle(marketingChecked) {
    return '' +
      '<div class="gmpp-choice gmpp-choice-locked">' +
        '<div><strong>' + escHtml(cfg.necessaryText || 'Strictly necessary') + '</strong><p>' + escHtml(cfg.necessaryDescription || 'Required for the website to work.') + '</p></div>' +
        '<label class="gmpp-switch" aria-label="Strictly necessary cookies always active"><input type="checkbox" checked disabled><span></span></label>' +
      '</div>' +
      '<div class="gmpp-choice">' +
        '<div><strong>' + escHtml(cfg.marketingText || 'Marketing / Meta Pixel') + '</strong><p>' + escHtml(cfg.marketingDescription || 'Allows PixelYourSite to load Meta/Facebook Pixel for ads measurement.') + '</p></div>' +
        '<label class="gmpp-switch" aria-label="Marketing cookies"><input id="gmpp-marketing-toggle" type="checkbox" ' + (marketingChecked ? 'checked' : '') + '><span></span></label>' +
      '</div>';
  }

  function renderBanner(mode) {
    if (state.banner) closeBanner();
    state.lastFocused = document.activeElement;

    var marketingChecked = hasMarketingConsent();
    var position = cfg.position === 'center' ? 'gmpp-center' : 'gmpp-bottom';
    var privacy = cfg.privacyUrl ? '<a class="gmpp-policy" href="' + escHtml(cfg.privacyUrl) + '" target="_blank" rel="noopener noreferrer">' + escHtml(cfg.privacyText || 'Privacy policy') + '</a>' : '';

    var root = document.createElement('div');
    root.className = 'gmpp-root ' + position;
    root.style.setProperty('--gmpp-accent', cfg.accentColor || '#2563eb');
    root.innerHTML = '' +
      '<div class="gmpp-backdrop" aria-hidden="true"></div>' +
      '<section class="gmpp-card" role="dialog" aria-modal="true" aria-labelledby="gmpp-title" aria-describedby="gmpp-desc">' +
        '<div class="gmpp-header">' +
          '<div><p class="gmpp-eyebrow">Consent</p><h2 id="gmpp-title">' + escHtml(cfg.title || 'Privacy preferences') + '</h2></div>' +
          '<button type="button" class="gmpp-icon" data-gmpp-action="reject" aria-label="Reject non-essential cookies">×</button>' +
        '</div>' +
        '<div id="gmpp-desc" class="gmpp-message">' + safeMessage(cfg.message || '') + '</div>' +
        privacy +
        '<div class="gmpp-panel" ' + (mode === 'manage' ? '' : 'hidden') + '>' + makeToggle(marketingChecked) + '</div>' +
        '<div class="gmpp-actions gmpp-primary-actions" ' + (mode === 'manage' ? 'hidden' : '') + '>' +
          '<button type="button" class="gmpp-btn btn btn-secondary gmpp-btn-secondary" data-gmpp-action="reject">' + escHtml(cfg.rejectAllText || 'Reject all') + '</button>' +
          '<button type="button" class="gmpp-btn btn gmpp-btn-ghost" data-gmpp-action="manage">' + escHtml(cfg.manageText || 'Manage options') + '</button>' +
          '<button type="button" class="gmpp-btn btn btn-primary gmpp-btn-primary" data-gmpp-action="accept">' + escHtml(cfg.acceptAllText || 'Accept all') + '</button>' +
        '</div>' +
        '<div class="gmpp-actions gmpp-manage-actions" ' + (mode === 'manage' ? '' : 'hidden') + '>' +
          '<button type="button" class="gmpp-btn btn btn-secondary gmpp-btn-secondary" data-gmpp-action="reject">' + escHtml(cfg.rejectAllText || 'Reject all') + '</button>' +
          '<button type="button" class="gmpp-btn btn btn-primary gmpp-btn-primary" data-gmpp-action="save">' + escHtml(cfg.saveText || 'Save choices') + '</button>' +
        '</div>' +
      '</section>';

    root.addEventListener('click', function (e) {
      var actionNode = e.target.closest('[data-gmpp-action]');
      if (!actionNode) return;
      var action = actionNode.getAttribute('data-gmpp-action');

      if (action === 'accept') {
        writeConsent(true);
        closeBanner();
        reloadIfNeeded();
      }
      if (action === 'reject') {
        writeConsent(false);
        closeBanner();
        reloadIfNeeded();
      }
      if (action === 'manage') {
        root.querySelector('.gmpp-panel').hidden = false;
        root.querySelector('.gmpp-primary-actions').hidden = true;
        root.querySelector('.gmpp-manage-actions').hidden = false;
        var toggle = root.querySelector('#gmpp-marketing-toggle');
        if (toggle) toggle.focus();
      }
      if (action === 'save') {
        var marketing = !!(root.querySelector('#gmpp-marketing-toggle') && root.querySelector('#gmpp-marketing-toggle').checked);
        writeConsent(marketing);
        closeBanner();
        reloadIfNeeded();
      }
    });

    document.body.appendChild(root);
    state.banner = root;
    document.addEventListener('keydown', trapFocus);
    var firstButton = root.querySelector('button');
    if (firstButton) firstButton.focus({ preventScroll: true });
  }

  function showSettingsButton() {
    return;
  }

  function bindInlineButtons() {
    document.addEventListener('click', function (e) {
      var opener = e.target.closest('[data-gmpp-open-settings]');
      if (!opener) return;
      e.preventDefault();
      renderBanner('manage');
    });
  }

  function init() {
    // Migrate old v1 consent to the lightweight yes/no cookie and PixelYourSite's consent cookie.
    var existing = readConsent();
    if (existing === 'yes' || existing === 'no') {
      setCookie(COOKIE_NAME, existing, maxAge());
      writePysConsent(existing === 'yes');
    }

    bindInlineButtons();

    if (!hasChoice()) {
      renderBanner('default');
    } else if (!hasMarketingConsent()) {
      revokeRuntimePixel();
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
