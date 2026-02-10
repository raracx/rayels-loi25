/**
 * @rayels/loi25-core
 * Loi 25 Quebec Cookie Consent Manager
 * https://rayelsconsulting.com
 *
 * Lightweight cookie consent solution compliant with Quebec's Loi 25 (Bill 64).
 * Bilingual (FR/EN), zero dependencies, ~2KB.
 */

const STORAGE_KEY = 'loi25-consent';
const STORAGE_TIMESTAMP_KEY = 'loi25-consent-date';

const CONSENT_ALL = 'all';
const CONSENT_NECESSARY = 'necessary';

const DEFAULT_CONFIG = {
  lang: 'fr',
  position: 'bottom', // 'bottom' | 'top'
  theme: 'light', // 'light' | 'dark'
  privacyPolicyUrl: '/politique-de-confidentialite',
  poweredByLink: true,
  expiryDays: 365,
  onAcceptAll: null,
  onAcceptNecessary: null,
  onRevoke: null,
  texts: null, // override default texts
};

const TEXTS = {
  fr: {
    title: 'Respect de votre vie privee',
    message: 'Ce site utilise des cookies pour ameliorer votre experience. Conformement a la Loi 25 du Quebec, nous demandons votre consentement avant de collecter vos donnees.',
    acceptAll: 'Tout accepter',
    acceptNecessary: 'Necessaires seulement',
    manage: 'Gerer mes preferences',
    privacyPolicy: 'Politique de confidentialite',
    poweredBy: 'Propulse par',
    categories: {
      necessary: { title: 'Necessaires', description: 'Essentiels au fonctionnement du site. Ne peuvent pas etre desactives.' },
      analytics: { title: 'Analytiques', description: 'Nous aident a comprendre comment vous utilisez le site.' },
      marketing: { title: 'Marketing', description: 'Utilises pour vous montrer des publicites pertinentes.' },
    },
  },
  en: {
    title: 'Your Privacy Matters',
    message: 'This website uses cookies to improve your experience. In compliance with Quebec\'s Law 25, we ask for your consent before collecting your data.',
    acceptAll: 'Accept All',
    acceptNecessary: 'Necessary Only',
    manage: 'Manage Preferences',
    privacyPolicy: 'Privacy Policy',
    poweredBy: 'Powered by',
    categories: {
      necessary: { title: 'Necessary', description: 'Essential for the website to function. Cannot be disabled.' },
      analytics: { title: 'Analytics', description: 'Help us understand how you use the website.' },
      marketing: { title: 'Marketing', description: 'Used to show you relevant advertisements.' },
    },
  },
};

/**
 * Get the current consent status
 * @returns {'all' | 'necessary' | null}
 */
function getConsent() {
  try {
    var consent = localStorage.getItem(STORAGE_KEY);
    if (!consent) return null;

    var timestamp = localStorage.getItem(STORAGE_TIMESTAMP_KEY);
    if (timestamp) {
      var expiryMs = 365 * 24 * 60 * 60 * 1000;
      if (Date.now() - parseInt(timestamp, 10) > expiryMs) {
        localStorage.removeItem(STORAGE_KEY);
        localStorage.removeItem(STORAGE_TIMESTAMP_KEY);
        return null;
      }
    }
    return consent;
  } catch (e) {
    return null;
  }
}

/**
 * Set consent
 * @param {'all' | 'necessary'} level
 */
function setConsent(level) {
  try {
    localStorage.setItem(STORAGE_KEY, level);
    localStorage.setItem(STORAGE_TIMESTAMP_KEY, String(Date.now()));
  } catch (e) {
    // localStorage not available
  }
}

/**
 * Revoke consent
 */
function revokeConsent() {
  try {
    localStorage.removeItem(STORAGE_KEY);
    localStorage.removeItem(STORAGE_TIMESTAMP_KEY);
  } catch (e) {
    // localStorage not available
  }
}

/**
 * Check if analytics/marketing cookies are allowed
 * @returns {boolean}
 */
function isAnalyticsAllowed() {
  return getConsent() === CONSENT_ALL;
}

/**
 * Create and inject the consent banner into the DOM
 * @param {object} userConfig
 */
function init(userConfig) {
  var config = Object.assign({}, DEFAULT_CONFIG, userConfig);
  var texts = config.texts || TEXTS[config.lang] || TEXTS.fr;

  // Already consented? Don't show banner
  if (getConsent()) return;

  // Build banner HTML
  var isDark = config.theme === 'dark';
  var bgColor = isDark ? '#18181b' : '#ffffff';
  var textColor = isDark ? '#e4e4e7' : '#1e293b';
  var mutedColor = isDark ? '#a1a1aa' : '#64748b';
  var borderColor = isDark ? '#27272a' : '#e2e8f0';
  var btnPrimaryBg = '#1d4ed8';
  var btnSecondaryBg = isDark ? '#27272a' : '#f1f5f9';
  var btnSecondaryText = isDark ? '#e4e4e7' : '#334155';

  var positionStyle = config.position === 'top'
    ? 'top:0;'
    : 'bottom:0;';

  var html = ''
    + '<div id="loi25-banner" style="'
    + 'position:fixed;left:0;right:0;' + positionStyle
    + 'z-index:999999;'
    + 'background:' + bgColor + ';'
    + 'border-' + (config.position === 'top' ? 'bottom' : 'top') + ':1px solid ' + borderColor + ';'
    + 'padding:20px 24px;'
    + 'font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;'
    + 'font-size:14px;'
    + 'color:' + textColor + ';'
    + 'box-shadow:0 -2px 20px rgba(0,0,0,0.08);'
    + '">'
    + '<div style="max-width:960px;margin:0 auto;">'
    + '<div style="font-weight:700;font-size:16px;margin-bottom:8px;">' + texts.title + '</div>'
    + '<p style="margin:0 0 16px;color:' + mutedColor + ';line-height:1.5;">' + texts.message + '</p>'
    + '<div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">'
    + '<button id="loi25-accept-all" style="'
    + 'background:' + btnPrimaryBg + ';color:#fff;border:none;padding:10px 20px;border-radius:8px;'
    + 'font-weight:600;font-size:14px;cursor:pointer;">'
    + texts.acceptAll + '</button>'
    + '<button id="loi25-accept-necessary" style="'
    + 'background:' + btnSecondaryBg + ';color:' + btnSecondaryText + ';border:1px solid ' + borderColor + ';'
    + 'padding:10px 20px;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;">'
    + texts.acceptNecessary + '</button>'
    + '<a href="' + config.privacyPolicyUrl + '" style="'
    + 'color:' + mutedColor + ';font-size:12px;text-decoration:underline;margin-left:8px;">'
    + texts.privacyPolicy + '</a>'
    + (config.poweredByLink
      ? '<a href="https://rayelsconsulting.com" target="_blank" rel="noopener" style="'
        + 'color:' + mutedColor + ';font-size:11px;margin-left:auto;text-decoration:none;opacity:0.7;">'
        + texts.poweredBy + ' Rayels</a>'
      : '')
    + '</div>'
    + '</div>'
    + '</div>';

  // Inject
  var wrapper = document.createElement('div');
  wrapper.innerHTML = html;
  document.body.appendChild(wrapper.firstChild);

  // Event listeners
  document.getElementById('loi25-accept-all').addEventListener('click', function () {
    setConsent(CONSENT_ALL);
    removeBanner();
    if (typeof config.onAcceptAll === 'function') config.onAcceptAll();
  });

  document.getElementById('loi25-accept-necessary').addEventListener('click', function () {
    setConsent(CONSENT_NECESSARY);
    removeBanner();
    if (typeof config.onAcceptNecessary === 'function') config.onAcceptNecessary();
  });
}

function removeBanner() {
  var banner = document.getElementById('loi25-banner');
  if (banner) banner.remove();
}

// Export for different module systems
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { init: init, getConsent: getConsent, setConsent: setConsent, revokeConsent: revokeConsent, isAnalyticsAllowed: isAnalyticsAllowed, TEXTS: TEXTS };
}
if (typeof window !== 'undefined') {
  window.Loi25 = { init: init, getConsent: getConsent, setConsent: setConsent, revokeConsent: revokeConsent, isAnalyticsAllowed: isAnalyticsAllowed, TEXTS: TEXTS };
}
