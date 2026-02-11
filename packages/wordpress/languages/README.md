# Traduction / Translation

Ce plugin supporte les installations WordPress en français et en anglais.
This plugin supports both English and French WordPress installations.

## Comment ça fonctionne / How It Works

Le plugin détecte automatiquement la langue de votre installation WordPress et affiche l'interface d'administration dans la langue appropriée.

The plugin automatically detects your WordPress language setting and displays the admin interface in the appropriate language.

### Langues supportées / Supported Languages

- **Français** (par défaut / default)
- **English**

## Pour les traducteurs / For Translators

Les fichiers de traduction se trouvent dans le répertoire `languages/` :
Translation files are located in the `languages/` directory:

- `rayels-loi25-fr_FR.po` — Source de traduction française / French translation source
- `rayels-loi25-fr_FR.mo` — Traduction compilée / Compiled translation (binary)

### Ajouter/Modifier des traductions / Adding/Editing Translations

1. Modifiez le fichier `.po` avec un éditeur de texte ou utilisez [Poedit](https://poedit.net/)
   Edit the `.po` file with a text editor or use [Poedit](https://poedit.net/)
2. Compilez en format `.mo` / Compile to `.mo` format:
   ```bash
   php compile-translations.php
   ```

### Ajouter une nouvelle langue / Adding a New Language

1. Copiez `rayels-loi25-fr_FR.po` vers `rayels-loi25-{locale}.po` (ex. `rayels-loi25-es_ES.po` pour l'espagnol)
   Copy `rayels-loi25-fr_FR.po` to `rayels-loi25-{locale}.po` (e.g., `rayels-loi25-es_ES.po` for Spanish)
2. Traduisez les valeurs `msgstr` / Translate the `msgstr` values
3. Compilez / Compile: `php compile-translations.php`

## Chaînes actuellement traduites / Currently Translated Strings

- Titre et libellés du widget du tableau de bord / Dashboard widget title and labels
- Éléments du menu d'administration / Admin menu items
- Libellés des statistiques (Tout accepter, Nécessaires seulement, Total, Aujourd'hui) / Statistics labels (Accept All, Necessary Only, Total, Today)

## Note

Le texte de la **bannière frontend** (visible par les visiteurs) est contrôlé par les réglages du plugin, et non par les fichiers de traduction. Les utilisateurs peuvent personnaliser le texte de la bannière en français et en anglais directement depuis le panneau d'administration.

The **frontend banner** text (visible to website visitors) is controlled by the plugin settings, not by translation files. Users can customize the banner text in both French and English directly from the admin panel.
