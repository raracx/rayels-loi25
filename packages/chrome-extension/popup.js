document.getElementById('check-btn').addEventListener('click', async () => {
  const resultsDiv = document.getElementById('results');
  resultsDiv.innerHTML = '<p style="color:#64748b;font-size:13px">Scanning...</p>';

  try {
    const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });

    const results = await chrome.scripting.executeScript({
      target: { tabId: tab.id },
      func: () => {
        const html = document.documentElement.innerHTML.toLowerCase();
        const checks = [];

        // Check for cookie banner keywords
        const bannerKeywords = ['cookie', 'consent', 'loi 25', 'law 25', 'vie privee', 'privacy', 'consentement'];
        const hasBanner = bannerKeywords.some(kw => html.includes(kw));
        checks.push({ label: 'Cookie consent banner detected', pass: hasBanner });

        // Check for privacy policy link
        const links = Array.from(document.querySelectorAll('a'));
        const hasPrivacyLink = links.some(a => {
          const href = (a.href || '').toLowerCase();
          const text = (a.textContent || '').toLowerCase();
          return href.includes('privacy') || href.includes('confidentialite') || href.includes('politique')
            || text.includes('privacy') || text.includes('confidentialite');
        });
        checks.push({ label: 'Privacy policy link found', pass: hasPrivacyLink });

        // Check for Google Analytics
        const hasGA = html.includes('google-analytics') || html.includes('gtag') || html.includes('ga(');
        checks.push({ label: 'Google Analytics detected', pass: null, warn: hasGA });

        // Check for Meta Pixel
        const hasFBPixel = html.includes('fbq(') || html.includes('facebook.com/tr') || html.includes('connect.facebook');
        checks.push({ label: 'Meta/Facebook Pixel detected', pass: null, warn: hasFBPixel });

        // Check for consent before tracking
        const hasConsentCheck = html.includes('loi25') || html.includes('cookie-consent') || html.includes('cookieconsent') || html.includes('consent');
        checks.push({ label: 'Consent management code found', pass: hasConsentCheck });

        return checks;
      }
    });

    const checks = results[0].result;
    let html = '';

    checks.forEach(check => {
      let icon;
      if (check.warn !== undefined) {
        icon = check.warn ? '<span class="icon-warn">!</span>' : '<span class="icon-pass">-</span>';
        const label = check.warn ? check.label + ' (needs consent)' : check.label + ' (not found)';
        html += `<div class="check-item">${icon} ${label}</div>`;
      } else {
        icon = check.pass ? '<span class="icon-pass">&#10003;</span>' : '<span class="icon-fail">&#10007;</span>';
        html += `<div class="check-item">${icon} ${check.label}</div>`;
      }
    });

    const passCount = checks.filter(c => c.pass === true).length;
    const total = checks.filter(c => c.pass !== null && c.pass !== undefined).length;
    const score = total > 0 ? Math.round((passCount / total) * 100) : 0;

    html = `<div style="text-align:center;margin-bottom:12px;padding:12px;background:${score >= 66 ? '#f0fdf4' : score >= 33 ? '#fefce8' : '#fef2f2'};border-radius:8px">
      <div style="font-size:24px;font-weight:700;color:${score >= 66 ? '#16a34a' : score >= 33 ? '#f59e0b' : '#dc2626'}">${score}%</div>
      <div style="font-size:12px;color:#64748b">Compliance Score</div>
    </div>` + html;

    resultsDiv.innerHTML = html;
  } catch (e) {
    resultsDiv.innerHTML = '<p style="color:#dc2626;font-size:13px">Unable to scan this page.</p>';
  }
});
