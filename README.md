# Loi 25 Quebec — Cookie Consent

> Lightweight, bilingual (FR/EN) cookie consent banner for Quebec's Law 25 (Bill 64).
> Zero dependencies. Plug & play. Works everywhere.

**By [Rayels Consulting](https://rayelsconsulting.com)** — Montreal Digital Agency

---

## What is Loi 25?

Quebec's **Law 25** (formerly Bill 64) requires all websites collecting personal information from Quebec residents to:
- Inform users about data collection
- Obtain explicit consent before using cookies/tracking
- Provide a privacy policy
- Allow users to withdraw consent

This toolkit makes compliance simple across any platform.

---

## Packages

| Package | Platform | Install |
|---------|----------|---------|
| [`@rayels/loi25-core`](./packages/core) | Vanilla JS | `npm i @rayels/loi25-core` |
| [`@rayels/loi25`](./packages/react) | React / Next.js | `npm i @rayels/loi25` |
| [`rayels-loi25`](./packages/wordpress) | WordPress | Plugin upload |
| [Chrome Extension](./packages/chrome-extension) | Chrome | Chrome Web Store |
| [VS Code Snippets](./packages/vscode-extension) | VS Code | Marketplace |

---

## Quick Start

### Vanilla JavaScript

```bash
npm install @rayels/loi25-core
```

```js
const Loi25 = require('@rayels/loi25-core');

Loi25.init({
  lang: 'fr',           // 'fr' or 'en'
  position: 'bottom',   // 'bottom' or 'top'
  theme: 'light',       // 'light' or 'dark'
  privacyPolicyUrl: '/politique-de-confidentialite'
});
```

Or via CDN — just add the script tag and call `Loi25.init()`:

```html
<script src="https://unpkg.com/@rayels/loi25-core"></script>
<script>
  Loi25.init({ lang: 'fr' });
</script>
```

#### Check consent before loading trackers

```js
if (Loi25.isAnalyticsAllowed()) {
  // Load Google Analytics, Meta Pixel, etc.
}
```

---

### React / Next.js

```bash
npm install @rayels/loi25
```

```jsx
import { Loi25Banner } from '@rayels/loi25';

function App() {
  return (
    <>
      <YourApp />
      <Loi25Banner
        lang="fr"
        position="bottom"
        theme="light"
        privacyPolicyUrl="/politique-de-confidentialite"
      />
    </>
  );
}
```

#### With Next.js App Router

```jsx
// app/layout.tsx
import { Loi25Banner } from '@rayels/loi25';

export default function RootLayout({ children }) {
  return (
    <html>
      <body>
        {children}
        <Loi25Banner lang="fr" />
      </body>
    </html>
  );
}
```

---

### WordPress

1. Download `rayels-loi25.php` from [`packages/wordpress/`](./packages/wordpress)
2. Upload to `wp-content/plugins/`
3. Activate in **Plugins** menu
4. Configure at **Settings → Loi 25 Cookies**

Settings include:
- Language (FR/EN)
- Position (top/bottom)
- Theme (light/dark)
- Privacy policy URL
- "Powered by Rayels" link toggle

---

### Chrome Extension — Loi 25 Compliance Checker

Scan any website to check if it's compliant with Quebec's Law 25:

- Detects cookie consent banners
- Finds privacy policy links
- Flags Google Analytics & Meta Pixel
- Shows a compliance score

Install from the [Chrome Web Store](#) or load unpacked from [`packages/chrome-extension/`](./packages/chrome-extension).

---

### VS Code Snippets

Code snippets to quickly add Loi 25 consent to any project.

**Available snippets:**

| Prefix | Description | Languages |
|--------|-------------|-----------|
| `loi25-banner` | Full cookie consent banner (vanilla JS) | JS |
| `loi25-check` | Check consent before loading trackers | JS |
| `loi25-revoke` | Revoke consent and reload | JS |
| `loi25-react` | Full React consent component | JSX/TSX |
| `loi25-hook` | React hook for consent status | JSX/TSX |
| `loi25-html` | HTML script tag banner | HTML |
| `loi25-wp` | WordPress functions.php snippet | PHP |

Install from the [VS Code Marketplace](#) or from [`packages/vscode-extension/`](./packages/vscode-extension).

---

## API Reference

### Core (`@rayels/loi25-core`)

```js
Loi25.init(config)          // Show banner (if no consent stored)
Loi25.getConsent()          // Returns 'all' | 'necessary' | null
Loi25.setConsent(level)     // Manually set consent
Loi25.revokeConsent()       // Remove consent (banner reappears)
Loi25.isAnalyticsAllowed()  // true if consent === 'all'
```

### Config Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `lang` | `'fr' \| 'en'` | `'fr'` | Banner language |
| `position` | `'bottom' \| 'top'` | `'bottom'` | Banner position |
| `theme` | `'light' \| 'dark'` | `'light'` | Color theme |
| `privacyPolicyUrl` | `string` | `'/politique-de-confidentialite'` | Privacy policy link |
| `poweredByLink` | `boolean` | `true` | Show "Powered by Rayels" |
| `onAcceptAll` | `function` | — | Callback when user accepts all |
| `onAcceptNecessary` | `function` | — | Callback when user accepts necessary only |

---

## How It Works

1. Banner appears on first visit (checks `localStorage`)
2. User clicks "Accept All" or "Necessary Only"
3. Choice saved to `localStorage` with key `loi25-consent`
4. Banner doesn't reappear on subsequent visits
5. Your code checks `Loi25.isAnalyticsAllowed()` before loading trackers

---

## License

MIT — [Rayels Consulting](https://rayelsconsulting.com)

---

## Need Help with Loi 25 Compliance?

Rayels Consulting offers full Loi 25 compliance audits and implementation for Quebec businesses.

- Web: [rayelsconsulting.com](https://rayelsconsulting.com)
- Services: Web Development, AI Automation, Digital Marketing, SEO
- Location: Montreal, Quebec, Canada
