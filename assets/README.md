# Ressources du plugin WordPress.org / WordPress.org Plugin Assets

Ces images sont utilisées sur la page du répertoire de plugins WordPress.org.
Elles ne sont PAS incluses dans le ZIP du plugin — elles sont téléversées séparément via SVN.

These images are used on the WordPress.org plugin directory page.
They are NOT included in the plugin ZIP — they are uploaded separately via SVN.

## Fichiers requis / Required Files

### Icônes / Icons (résultats de recherche et cartes de plugins / search results & plugin cards)
- `icon-128x128.png` — Icône standard / Standard icon
- `icon-256x256.png` — Icône Retina / Retina icon

**Suggestion de design :** Arrière-plan dégradé bleu/violet (#1d4ed8 vers #7c3aed) avec une icône de bouclier-cookie blanche. Simple, audacieux, reconnaissable en petit format.

**Design suggestion:** Blue/purple gradient background (#1d4ed8 to #7c3aed) with a white cookie shield icon. Simple, bold, recognizable at small sizes.

### Bannières / Banners (haut de la page du plugin / top of plugin page)
- `banner-772x250.png` — Bannière standard / Standard banner
- `banner-1544x500.png` — Bannière Retina (2x) / Retina banner (2x)

**Suggestion de design :** Même dégradé, avec « Loi 25 Cookie Consent » en texte blanc gras, sous-titre « 100% Gratuit / Conforme Québec », et une icône subtile de bouclier/cookie.

**Design suggestion:** Same gradient, with "Loi 25 Cookie Consent" in bold white text, subtitle "100% Free / Quebec Compliant", and a subtle shield/cookie icon.

### Captures d'écran / Screenshots (référencées dans readme.txt comme `screenshot-1.png`, etc.)
1. `screenshot-1.png` — Bannière style Barre (thème clair) / Bar style banner (light theme)
2. `screenshot-2.png` — Bannière style Popup (thème sombre + glassmorphisme) / Popup style (dark + glassmorphism)
3. `screenshot-3.png` — Style Widget de coin / Corner widget style
4. `screenshot-4.png` — Page des réglages admin (onglet Général) / Admin settings page (General tab)
5. `screenshot-5.png` — Onglet Script Vault / Script Vault tab
6. `screenshot-6.png` — Widget de statistiques de consentement / Consent Statistics dashboard widget
7. `screenshot-7.png` — Réglages de texte personnalisé (FR + EN) / Custom Text settings (FR + EN)

## Comment créer / How to Create

### Option rapide : Canva (gratuit) / Quick option: Canva (free)
1. Allez sur canva.com / Go to canva.com
2. Créez une taille personnalisée pour chaque ressource / Create custom size for each asset
3. Utilisez les couleurs de marque / Use brand colors: #1d4ed8 (bleu/blue) vers/to #7c3aed (violet/purple)
4. Exportez en PNG / Export as PNG

### Captures d'écran / Screenshots
1. Installez le plugin sur votre site WordPress de test / Install the plugin on your test WordPress site
2. Prenez des captures d'écran de chaque fonctionnalité / Take screenshots of each feature
3. Recadrez aux dimensions propres (ex. 1200x800) / Crop to clean dimensions (e.g., 1200x800)
4. Ajoutez des bordures ou ombres subtiles / Add subtle borders or drop shadows for polish

## Destination / Where These Go

Ces fichiers sont téléversés dans le répertoire SVN `assets/` de WordPress.org, PAS inclus dans le ZIP du plugin.

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

## Commande SVN / SVN Upload Command
```bash
svn co https://plugins.svn.wordpress.org/rayels-loi25/
cd rayels-loi25/assets/
# Copiez vos images ici / Copy your images here
svn add *.png
svn ci -m "Add plugin assets"
```
