# CLAUDE.md - Loi 25 Quebec Cookie Consent Toolkit

## Project Overview
Multi-platform cookie consent toolkit for Quebec's **Law 25 (Bill 64)**. Built by **Rayels Consulting** as both a genuine open-source tool AND an SEO/backlinks strategy to build domain authority for [rayelsconsulting.com](https://rayelsconsulting.com).

## Why This Exists — SEO Strategy
The primary goal is **DA 90+ backlinks** from high-authority platforms. Every package links back to rayelsconsulting.com:
- **npm** (DA 97) — `@rayels/loi25-core` + `@rayels/loi25` packages
- **GitHub** (DA 95) — `raracx/rayels-loi25` repo
- **WordPress.org** (DA 96) — Plugin directory listing
- **Chrome Web Store** (DA 97) — Extension listing
- **VS Code Marketplace** (DA 92) — Snippet extension listing

Each platform = 1 dofollow or high-value backlink to rayelsconsulting.com. Total: **5+ backlinks from DA 90+ sites**.

Secondary goal: organic adoption. Loi 25 is **legally mandatory** for Quebec businesses — people will search for it, install it, and some will click through to hire Rayels Consulting.

## Project Structure
```
rayels-loi25/
├── CLAUDE.md              # This file
├── README.md              # Main repo README (GitHub)
├── LICENSE                # MIT
├── package.json           # Monorepo root
├── .gitignore
├── demo.html              # Interactive platform showcase page
├── banner-preview.html    # Live banner preview (runs actual Loi25.init())
└── packages/
    ├── core/              # @rayels/loi25-core — Vanilla JS consent manager
    │   ├── index.js       # Main library (~209 lines)
    │   ├── index.d.ts     # TypeScript types
    │   └── package.json
    ├── react/             # @rayels/loi25 — React/Next.js component
    │   ├── index.js       # <Loi25Banner> component (renamed from .jsx)
    │   └── package.json
    ├── wordpress/         # WordPress plugin
    │   └── rayels-loi25.php  # Full plugin with settings page
    ├── chrome-extension/  # Loi 25 compliance checker
    │   ├── manifest.json  # Manifest V3
    │   ├── popup.html     # Extension popup UI
    │   ├── popup.js       # Scanning logic
    │   ├── icon48.png     # Extension icon (48x48)
    │   ├── icon128.png    # Extension icon (128x128)
    │   ├── screenshot-1.png    # Chrome Web Store screenshot (1280x800)
    │   ├── promo-small.png     # Chrome Web Store small promo (440x280)
    │   ├── promo-marquee.png   # Chrome Web Store marquee promo (1400x560)
    │   └── loi25-chrome-extension.zip  # Ready-to-upload zip
    └── vscode-extension/  # VS Code snippet extension
        ├── package.json   # Extension config
        ├── icon.png       # Marketplace icon (128x128)
        ├── README.md      # Marketplace README
        └── snippets/      # Snippet files (js, jsx, html, php)
```

## Brand & Naming
- **npm scope:** `@rayels` (org created on npmjs.com)
- **npm account:** `rayelcx`
- **GitHub repo:** `raracx/rayels-loi25`
- **Author field:** "Rayels Consulting" (full name in all metadata)
- **Homepage links:** Always `https://rayelsconsulting.com`
- **WordPress plugin name:** "Loi 25 Quebec Cookie Consent — by Rayels Consulting"
- **Chrome Web Store publisher:** "Rayels"

## Tech Details
- **Zero dependencies** — everything is vanilla JS or uses React as a peer dep
- **Bilingual** — French (default) and English
- **localStorage** — consent stored with key `loi25-consent` (values: `all` | `necessary`)
- **Consent date** — stored with key `loi25-consent-date` (unix timestamp)
- **Config options:** lang, position (top/bottom), theme (light/dark), privacyPolicyUrl, poweredByLink, callbacks

## Publishing Status

### Published
- [x] `npm publish --access public` for `packages/core` — **@rayels/loi25-core@1.0.0** (https://www.npmjs.com/package/@rayels/loi25-core)
- [x] `npm publish --access public` for `packages/react` — **@rayels/loi25@1.0.0** (https://www.npmjs.com/package/@rayels/loi25)
- [x] Chrome extension icons created (icon48.png, icon128.png) — blue rounded square with shield + "L25"
- [x] VS Code extension icon created (icon.png) — same design
- [x] Chrome Web Store listing images generated (screenshot, small promo, marquee promo)
- [x] Chrome extension submitted to Chrome Web Store — **pending review** (1-3 business days)
- [x] React package entry point renamed from index.jsx to index.js for better compatibility
- [x] All repository URLs corrected from `rayels-loi25-quebec` to `rayels-loi25`

### Still TODO
- [ ] Package and publish VS Code extension via `vsce` (needs Azure DevOps Personal Access Token)
- [ ] Submit WordPress plugin to wordpress.org/plugins
- [ ] Create GitHub repo `raracx/rayels-loi25` and push (if not done yet)
- [ ] Add links to all published listings in the main README (replace # placeholders)
- [ ] Submit GitHub repo URL to Google Search Console for indexing
- [ ] Create a /tools/loi25 or /open-source page on rayelsconsulting.com linking to all packages
- [ ] Publish npm v1.0.1 patch to fix repository URL in published packages

### Publishing Notes
- **npm auth:** Use `--auth-type=web` flag to authenticate with passkey in browser
- **Chrome Web Store:** Developer account under maylice09@gmail.com, $5 fee paid, non-professional account
- **Chrome Web Store listing:** Bilingual description (FR primary, EN secondary), category: Developer Tools

## Quality Rules
- Keep all packages lightweight — no bloat, no build steps for core/react
- Every package.json, manifest, and plugin header MUST link to rayelsconsulting.com
- "Powered by Rayels" link should be ON by default (configurable to hide)
- Bilingual FR/EN support in everything
- MIT license on everything

## Parent Project
This project supports the SEO strategy for the main Rayels Consulting website at:
`../rayels-next-site/`

The backlinks strategy doc is at:
`../rayels-next-site/Backlinks-Authority-Strategy.md`

## Owner
- **Name:** Rayel
- **Business:** Rayels Consulting
- **Location:** Montreal, Quebec, Canada
- **Main site:** https://rayelsconsulting.com
