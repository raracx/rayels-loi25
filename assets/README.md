# WordPress.org Plugin Assets

These images are used on the WordPress.org plugin directory page.
They are NOT included in the plugin ZIP — they are uploaded separately via SVN.

## Required Files

### Icons (shown in search results & plugin cards)
- `icon-128x128.png` — Standard icon
- `icon-256x256.png` — Retina icon

**Design suggestion:** Blue/purple gradient background (#1d4ed8 → #7c3aed) with a white cookie shield icon. Simple, bold, recognizable at small sizes.

### Banners (shown at the top of the plugin page)
- `banner-772x250.png` — Standard banner
- `banner-1544x500.png` — Retina banner (2x)

**Design suggestion:** Same gradient, with "Loi 25 Cookie Consent" in bold white text, subtitle "100% Free • Quebec Compliant", and a subtle shield/cookie icon.

### Screenshots (referenced in readme.txt as `screenshot-1.png`, etc.)
1. `screenshot-1.png` — Bar style banner (light theme)
2. `screenshot-2.png` — Popup style banner (dark theme + glassmorphism)
3. `screenshot-3.png` — Corner widget style
4. `screenshot-4.png` — Admin settings page (General tab)
5. `screenshot-5.png` — Script Vault tab
6. `screenshot-6.png` — Consent Statistics dashboard widget
7. `screenshot-7.png` — Custom Text settings (FR + EN)

## How to Create

### Quick option: Use Canva (free)
1. Go to canva.com
2. Create custom size for each asset
3. Use brand colors: #1d4ed8 (blue) → #7c3aed (purple)
4. Export as PNG

### Screenshots
1. Install the plugin on your test WordPress site
2. Take screenshots of each feature
3. Crop to clean dimensions (e.g., 1200x800)
4. Add subtle borders or drop shadows for polish

## Where These Go

These files are uploaded to the WordPress.org SVN `assets/` directory, NOT included in the plugin ZIP file.

```
https://plugins.svn.wordpress.org/rayels-loi25/assets/
├── icon-128x128.png
├── icon-256x256.png
├── banner-772x250.png
├── banner-1544x500.png
├── screenshot-1.png
├── screenshot-2.png
└── ...
```

## SVN Upload Command
```bash
svn co https://plugins.svn.wordpress.org/rayels-loi25/
cd rayels-loi25/assets/
# Copy your images here
svn add *.png
svn ci -m "Add plugin assets"
```
