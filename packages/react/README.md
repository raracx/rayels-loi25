# @rayels/loi25

> **FR** | [English](#english)

Composant React / Next.js pour le consentement aux cookies **Loi 25 du Québec** (Projet de loi 64). Léger, bilingue, conforme.

**Par [Rayels Consulting](https://rayelsconsulting.com)** — Agence numérique à Montréal

## Installation

```bash
npm install @rayels/loi25
```

## Utilisation

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

### Avec Next.js App Router

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

## Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `lang` | `'fr' \| 'en'` | `'fr'` | Langue |
| `position` | `'bottom' \| 'top'` | `'bottom'` | Position |
| `theme` | `'light' \| 'dark'` | `'light'` | Thème |
| `privacyPolicyUrl` | `string` | `'/politique-de-confidentialite'` | Lien confidentialité |
| `poweredByLink` | `boolean` | `true` | Afficher "Propulsé par Rayels" |
| `onAcceptAll` | `function` | — | Callback accepter tout |
| `onAcceptNecessary` | `function` | — | Callback nécessaire seulement |

## Vérifier le consentement

```js
// Partout dans votre code
const consent = localStorage.getItem('loi25-consent');
// 'all' | 'necessary' | null

if (consent === 'all') {
  // Charger les traceurs
}
```

## Aussi disponible

- [`@rayels/loi25-core`](https://www.npmjs.com/package/@rayels/loi25-core) — Vanilla JS (sans React)
- [Plugin WordPress](https://github.com/raracx/rayels-loi25/tree/master/packages/wordpress)
- [Extension Chrome](https://github.com/raracx/rayels-loi25/tree/master/packages/chrome-extension)
- [Snippets VS Code](https://marketplace.visualstudio.com/items?itemName=rayels-consulting.rayels-loi25-snippets)

## Licence

MIT — [Rayels Consulting](https://rayelsconsulting.com)

---

<a id="english"></a>

# English

React / Next.js component for **Quebec Law 25** (Bill 64) cookie consent. Lightweight, bilingual, compliant.

**By [Rayels Consulting](https://rayelsconsulting.com)** — Montreal Digital Agency

## Install

```bash
npm install @rayels/loi25
```

## Usage

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

### With Next.js App Router

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

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `lang` | `'fr' \| 'en'` | `'fr'` | Language |
| `position` | `'bottom' \| 'top'` | `'bottom'` | Position |
| `theme` | `'light' \| 'dark'` | `'light'` | Theme |
| `privacyPolicyUrl` | `string` | `'/politique-de-confidentialite'` | Privacy policy link |
| `poweredByLink` | `boolean` | `true` | Show "Powered by Rayels" |
| `onAcceptAll` | `function` | — | Callback on accept all |
| `onAcceptNecessary` | `function` | — | Callback on necessary only |

## Check consent

```js
// Anywhere in your code
const consent = localStorage.getItem('loi25-consent');
// 'all' | 'necessary' | null

if (consent === 'all') {
  // Load trackers
}
```

## Also available

- [`@rayels/loi25-core`](https://www.npmjs.com/package/@rayels/loi25-core) — Vanilla JS (no React)
- [WordPress Plugin](https://github.com/raracx/rayels-loi25/tree/master/packages/wordpress)
- [Chrome Extension](https://github.com/raracx/rayels-loi25/tree/master/packages/chrome-extension)
- [VS Code Snippets](https://marketplace.visualstudio.com/items?itemName=rayels-consulting.rayels-loi25-snippets)

## License

MIT — [Rayels Consulting](https://rayelsconsulting.com)
