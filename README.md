# Loi 25 Quebec — Cookie Consent / Consentement aux cookies

> **FR** | [English](#english)

> Bannière de consentement aux cookies légère et bilingue (FR/EN) pour la Loi 25 du Québec (Projet de loi 64).
> Zéro dépendance. Prêt à l'emploi. Fonctionne partout.

**Par [Rayels Consulting](https://rayelsconsulting.com)** — Agence numérique à Montréal

---

## Qu'est-ce que la Loi 25 ?

La **Loi 25** du Québec (anciennement Projet de loi 64) exige que tous les sites web qui collectent des renseignements personnels auprès de résidents du Québec doivent :
- Informer les utilisateurs de la collecte de données
- Obtenir un consentement explicite avant d'utiliser des cookies/traceurs
- Fournir une politique de confidentialité
- Permettre aux utilisateurs de retirer leur consentement

Ce toolkit simplifie la conformité sur toutes les plateformes.

---

## Packages / Paquets

| Paquet | Plateforme | Installation |
|--------|------------|--------------|
| [`@rayels/loi25-core`](./packages/core) | Vanilla JS | `npm i @rayels/loi25-core` |
| [`@rayels/loi25`](./packages/react) | React / Next.js | `npm i @rayels/loi25` |
| [`rayels-loi25`](./packages/wordpress) | WordPress | Téléverser le plugin |
| [Extension Chrome](./packages/chrome-extension) | Chrome | Chrome Web Store |
| [Snippets VS Code](./packages/vscode-extension) | VS Code | Marketplace |

---

## Démarrage rapide

### JavaScript vanille

```bash
npm install @rayels/loi25-core
```

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

#### Vérifier le consentement avant de charger les traceurs

```js
if (Loi25.isAnalyticsAllowed()) {
  // Charger Google Analytics, Meta Pixel, etc.
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

#### Avec Next.js App Router

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

1. Téléchargez le plugin depuis [`packages/wordpress/`](./packages/wordpress)
2. Téléversez dans `wp-content/plugins/`
3. Activez dans le menu **Extensions**
4. Configurez dans **Réglages → Loi 25 Cookies**

Paramètres inclus :
- Langue (FR/EN)
- Position (haut/bas)
- Thème (clair/sombre)
- URL de politique de confidentialité
- Lien « Propulsé par Rayels »

---

### Extension Chrome — Vérificateur de conformité Loi 25

Analysez n'importe quel site web pour vérifier sa conformité à la Loi 25 :

- Détecte les bannières de consentement aux cookies
- Trouve les liens vers les politiques de confidentialité
- Signale Google Analytics et Meta Pixel
- Affiche un score de conformité

Installez depuis le [Chrome Web Store](#) ou chargez depuis [`packages/chrome-extension/`](./packages/chrome-extension).

---

### Snippets VS Code

Extraits de code pour ajouter rapidement le consentement Loi 25 à tout projet.

| Préfixe | Description | Langages |
|---------|-------------|----------|
| `loi25-banner` | Bannière de consentement complète (JS vanille) | JS |
| `loi25-check` | Vérifier le consentement avant les traceurs | JS |
| `loi25-revoke` | Révoquer le consentement et recharger | JS |
| `loi25-react` | Composant React de consentement complet | JSX/TSX |
| `loi25-hook` | Hook React pour le statut du consentement | JSX/TSX |
| `loi25-html` | Bannière via balise script HTML | HTML |
| `loi25-wp` | Extrait pour functions.php WordPress | PHP |

Installez depuis le [VS Code Marketplace](https://marketplace.visualstudio.com/items?itemName=rayels-consulting.rayels-loi25-snippets) ou depuis [`packages/vscode-extension/`](./packages/vscode-extension).

---

## Référence API

### Core (`@rayels/loi25-core`)

```js
Loi25.init(config)          // Afficher la bannière (si aucun consentement enregistré)
Loi25.getConsent()          // Retourne 'all' | 'necessary' | null
Loi25.setConsent(level)     // Définir le consentement manuellement
Loi25.revokeConsent()       // Supprimer le consentement (la bannière réapparaît)
Loi25.isAnalyticsAllowed()  // true si consentement === 'all'
```

### Options de configuration

| Option | Type | Défaut | Description |
|--------|------|--------|-------------|
| `lang` | `'fr' \| 'en'` | `'fr'` | Langue de la bannière |
| `position` | `'bottom' \| 'top'` | `'bottom'` | Position de la bannière |
| `theme` | `'light' \| 'dark'` | `'light'` | Thème de couleurs |
| `privacyPolicyUrl` | `string` | `'/politique-de-confidentialite'` | Lien vers la politique de confidentialité |
| `poweredByLink` | `boolean` | `true` | Afficher « Propulsé par Rayels » |
| `onAcceptAll` | `function` | — | Callback quand l'utilisateur accepte tout |
| `onAcceptNecessary` | `function` | — | Callback quand l'utilisateur accepte le nécessaire seulement |

---

## Comment ça fonctionne

1. La bannière apparaît à la première visite (vérifie `localStorage`)
2. L'utilisateur clique sur « Tout accepter » ou « Nécessaires seulement »
3. Le choix est sauvegardé dans `localStorage` avec la clé `loi25-consent`
4. La bannière ne réapparaît pas lors des visites suivantes
5. Votre code vérifie `Loi25.isAnalyticsAllowed()` avant de charger les traceurs

---

## Licence

MIT — [Rayels Consulting](https://rayelsconsulting.com)

---

## Besoin d'aide avec la conformité Loi 25 ?

Rayels Consulting offre des audits de conformité Loi 25 complets et de l'accompagnement pour les entreprises québécoises.

- Web : [rayelsconsulting.com](https://rayelsconsulting.com)
- Services : Développement web, Automatisation IA, Marketing numérique, SEO
- Localisation : Montréal, Québec, Canada

---
---

<a id="english"></a>

# English

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

Or via CDN:

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

1. Download the plugin from [`packages/wordpress/`](./packages/wordpress)
2. Upload to `wp-content/plugins/`
3. Activate in **Plugins** menu
4. Configure at **Settings > Loi 25 Cookies**

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

| Prefix | Description | Languages |
|--------|-------------|-----------|
| `loi25-banner` | Full cookie consent banner (vanilla JS) | JS |
| `loi25-check` | Check consent before loading trackers | JS |
| `loi25-revoke` | Revoke consent and reload | JS |
| `loi25-react` | Full React consent component | JSX/TSX |
| `loi25-hook` | React hook for consent status | JSX/TSX |
| `loi25-html` | HTML script tag banner | HTML |
| `loi25-wp` | WordPress functions.php snippet | PHP |

Install from the [VS Code Marketplace](https://marketplace.visualstudio.com/items?itemName=rayels-consulting.rayels-loi25-snippets) or from [`packages/vscode-extension/`](./packages/vscode-extension).

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
