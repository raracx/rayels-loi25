# @rayels/loi25-core

> **FR** | [English](#english)

Bannière de consentement aux cookies légère et bilingue pour la **Loi 25 du Québec** (Projet de loi 64). Zéro dépendance. Vanilla JS. Fonctionne partout.

**Par [Rayels Consulting](https://rayelsconsulting.com)** — Agence numérique à Montréal

## Installation

```bash
npm install @rayels/loi25-core
```

## Utilisation

```js
const Loi25 = require('@rayels/loi25-core');

Loi25.init({
  lang: 'fr',           // 'fr' ou 'en'
  position: 'bottom',   // 'bottom' ou 'top'
  theme: 'light',       // 'light' ou 'dark'
  privacyPolicyUrl: '/politique-de-confidentialite'
});
```

Ou via CDN :

```html
<script src="https://unpkg.com/@rayels/loi25-core"></script>
<script>
  Loi25.init({ lang: 'fr' });
</script>
```

## Vérifier le consentement

```js
if (Loi25.isAnalyticsAllowed()) {
  // Charger Google Analytics, Meta Pixel, etc.
}
```

## API

```js
Loi25.init(config)          // Afficher la bannière
Loi25.getConsent()          // Retourne 'all' | 'necessary' | null
Loi25.setConsent(level)     // Définir le consentement
Loi25.revokeConsent()       // Révoquer (la bannière réapparaît)
Loi25.isAnalyticsAllowed()  // true si consentement === 'all'
```

## Options

| Option | Type | Défaut | Description |
|--------|------|--------|-------------|
| `lang` | `'fr' \| 'en'` | `'fr'` | Langue |
| `position` | `'bottom' \| 'top'` | `'bottom'` | Position |
| `theme` | `'light' \| 'dark'` | `'light'` | Thème |
| `privacyPolicyUrl` | `string` | `'/politique-de-confidentialite'` | Lien confidentialité |
| `poweredByLink` | `boolean` | `true` | Afficher "Propulsé par Rayels" |
| `onAcceptAll` | `function` | — | Callback accepter tout |
| `onAcceptNecessary` | `function` | — | Callback nécessaire seulement |

## Aussi disponible

- [`@rayels/loi25`](https://www.npmjs.com/package/@rayels/loi25) — Composant React / Next.js
- [Plugin WordPress](https://github.com/raracx/rayels-loi25/tree/master/packages/wordpress)
- [Extension Chrome](https://github.com/raracx/rayels-loi25/tree/master/packages/chrome-extension)
- [Snippets VS Code](https://marketplace.visualstudio.com/items?itemName=rayels-consulting.rayels-loi25-snippets)

## Licence

MIT — [Rayels Consulting](https://rayelsconsulting.com)

---

<a id="english"></a>

# English

Lightweight, bilingual cookie consent banner for Quebec's **Law 25** (Bill 64). Zero dependencies. Vanilla JS. Works everywhere.

**By [Rayels Consulting](https://rayelsconsulting.com)** — Montreal Digital Agency

## Install

```bash
npm install @rayels/loi25-core
```

## Usage

```js
const Loi25 = require('@rayels/loi25-core');

Loi25.init({
  lang: 'fr',           // 'fr' or 'en'
  position: 'bottom',   // 'bottom' or 'top'
  theme: 'light',       // 'light' or 'dark'
  privacyPolicyUrl: '/politique-de-confidentialite'
});
```

Or via CDN:

```html
<script src="https://unpkg.com/@rayels/loi25-core"></script>
<script>
  Loi25.init({ lang: 'fr' });
</script>
```

## Check consent

```js
if (Loi25.isAnalyticsAllowed()) {
  // Load Google Analytics, Meta Pixel, etc.
}
```

## API

```js
Loi25.init(config)          // Show banner
Loi25.getConsent()          // Returns 'all' | 'necessary' | null
Loi25.setConsent(level)     // Manually set consent
Loi25.revokeConsent()       // Revoke (banner reappears)
Loi25.isAnalyticsAllowed()  // true if consent === 'all'
```

## Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `lang` | `'fr' \| 'en'` | `'fr'` | Language |
| `position` | `'bottom' \| 'top'` | `'bottom'` | Position |
| `theme` | `'light' \| 'dark'` | `'light'` | Theme |
| `privacyPolicyUrl` | `string` | `'/politique-de-confidentialite'` | Privacy policy link |
| `poweredByLink` | `boolean` | `true` | Show "Powered by Rayels" |
| `onAcceptAll` | `function` | — | Callback on accept all |
| `onAcceptNecessary` | `function` | — | Callback on necessary only |

## Also available

- [`@rayels/loi25`](https://www.npmjs.com/package/@rayels/loi25) — React / Next.js component
- [WordPress Plugin](https://github.com/raracx/rayels-loi25/tree/master/packages/wordpress)
- [Chrome Extension](https://github.com/raracx/rayels-loi25/tree/master/packages/chrome-extension)
- [VS Code Snippets](https://marketplace.visualstudio.com/items?itemName=rayels-consulting.rayels-loi25-snippets)

## License

MIT — [Rayels Consulting](https://rayelsconsulting.com)
