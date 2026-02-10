/**
 * @rayels/loi25
 * React Cookie Consent Banner — Loi 25 Quebec Compliant
 * https://rayelsconsulting.com
 */

import React, { useState, useEffect } from 'react';

const STORAGE_KEY = 'loi25-consent';
const STORAGE_TIMESTAMP_KEY = 'loi25-consent-date';

const TEXTS = {
  fr: {
    title: 'Respect de votre vie privee',
    message: 'Ce site utilise des cookies pour ameliorer votre experience. Conformement a la Loi 25 du Quebec, nous demandons votre consentement.',
    acceptAll: 'Tout accepter',
    acceptNecessary: 'Necessaires seulement',
    privacyPolicy: 'Politique de confidentialite',
    poweredBy: 'Propulse par',
  },
  en: {
    title: 'Your Privacy Matters',
    message: 'This website uses cookies to improve your experience. In compliance with Quebec\'s Law 25, we ask for your consent.',
    acceptAll: 'Accept All',
    acceptNecessary: 'Necessary Only',
    privacyPolicy: 'Privacy Policy',
    poweredBy: 'Powered by',
  },
};

export function getConsent() {
  try {
    return localStorage.getItem(STORAGE_KEY);
  } catch {
    return null;
  }
}

export function isAnalyticsAllowed() {
  return getConsent() === 'all';
}

export function revokeConsent() {
  try {
    localStorage.removeItem(STORAGE_KEY);
    localStorage.removeItem(STORAGE_TIMESTAMP_KEY);
  } catch {}
}

export function Loi25Banner({
  lang = 'fr',
  position = 'bottom',
  theme = 'light',
  privacyPolicyUrl = '/politique-de-confidentialite',
  poweredByLink = true,
  onAcceptAll,
  onAcceptNecessary,
}) {
  const [visible, setVisible] = useState(false);
  const texts = TEXTS[lang] || TEXTS.fr;
  const isDark = theme === 'dark';

  useEffect(() => {
    if (!getConsent()) setVisible(true);
  }, []);

  const handleAcceptAll = () => {
    try {
      localStorage.setItem(STORAGE_KEY, 'all');
      localStorage.setItem(STORAGE_TIMESTAMP_KEY, String(Date.now()));
    } catch {}
    setVisible(false);
    if (onAcceptAll) onAcceptAll();
  };

  const handleAcceptNecessary = () => {
    try {
      localStorage.setItem(STORAGE_KEY, 'necessary');
      localStorage.setItem(STORAGE_TIMESTAMP_KEY, String(Date.now()));
    } catch {}
    setVisible(false);
    if (onAcceptNecessary) onAcceptNecessary();
  };

  if (!visible) return null;

  return (
    <div
      style={{
        position: 'fixed',
        left: 0,
        right: 0,
        [position]: 0,
        zIndex: 999999,
        background: isDark ? '#18181b' : '#ffffff',
        borderTop: position === 'bottom' ? `1px solid ${isDark ? '#27272a' : '#e2e8f0'}` : 'none',
        borderBottom: position === 'top' ? `1px solid ${isDark ? '#27272a' : '#e2e8f0'}` : 'none',
        padding: '20px 24px',
        fontFamily: '-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif',
        fontSize: '14px',
        color: isDark ? '#e4e4e7' : '#1e293b',
        boxShadow: '0 -2px 20px rgba(0,0,0,0.08)',
      }}
    >
      <div style={{ maxWidth: 960, margin: '0 auto' }}>
        <div style={{ fontWeight: 700, fontSize: 16, marginBottom: 8 }}>
          {texts.title}
        </div>
        <p style={{ margin: '0 0 16px', color: isDark ? '#a1a1aa' : '#64748b', lineHeight: 1.5 }}>
          {texts.message}
        </p>
        <div style={{ display: 'flex', flexWrap: 'wrap', gap: 8, alignItems: 'center' }}>
          <button
            onClick={handleAcceptAll}
            style={{
              background: '#1d4ed8',
              color: '#fff',
              border: 'none',
              padding: '10px 20px',
              borderRadius: 8,
              fontWeight: 600,
              fontSize: 14,
              cursor: 'pointer',
            }}
          >
            {texts.acceptAll}
          </button>
          <button
            onClick={handleAcceptNecessary}
            style={{
              background: isDark ? '#27272a' : '#f1f5f9',
              color: isDark ? '#e4e4e7' : '#334155',
              border: `1px solid ${isDark ? '#27272a' : '#e2e8f0'}`,
              padding: '10px 20px',
              borderRadius: 8,
              fontWeight: 600,
              fontSize: 14,
              cursor: 'pointer',
            }}
          >
            {texts.acceptNecessary}
          </button>
          <a
            href={privacyPolicyUrl}
            style={{
              color: isDark ? '#a1a1aa' : '#64748b',
              fontSize: 12,
              textDecoration: 'underline',
              marginLeft: 8,
            }}
          >
            {texts.privacyPolicy}
          </a>
          {poweredByLink && (
            <a
              href="https://rayelsconsulting.com"
              target="_blank"
              rel="noopener noreferrer"
              style={{
                color: isDark ? '#a1a1aa' : '#64748b',
                fontSize: 11,
                marginLeft: 'auto',
                textDecoration: 'none',
                opacity: 0.7,
              }}
            >
              {texts.poweredBy} Rayels
            </a>
          )}
        </div>
      </div>
    </div>
  );
}

export default Loi25Banner;
