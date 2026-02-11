# Translation / Traduction

This plugin supports both English and French WordPress installations.

## How It Works

The plugin automatically detects your WordPress language setting and displays the admin interface in the appropriate language.

### Supported Languages

- **English** (default)
- **Français** (French)

## For Translators

Translation files are located in the `languages/` directory:

- `rayels-loi25-fr_FR.po` — French translation source
- `rayels-loi25-fr_FR.mo` — French compiled translation (binary)

### Adding/Editing Translations

1. Edit the `.po` file with a text editor or use [Poedit](https://poedit.net/)
2. Compile to `.mo` format:
   ```bash
   php compile-translations.php
   ```

### Adding a New Language

1. Copy `rayels-loi25-fr_FR.po` to `rayels-loi25-{locale}.po` (e.g., `rayels-loi25-es_ES.po` for Spanish)
2. Translate the `msgstr` values
3. Compile: `php compile-translations.php`

## Currently Translated Strings

- Dashboard widget title and labels
- Admin menu items
- Statistics labels (Accept All, Necessary Only, Total, Today)

## Note

The **frontend banner** text (visible to website visitors) is controlled by the plugin settings, not by translation files. Users can customize the banner text in both French and English directly from the admin panel.
