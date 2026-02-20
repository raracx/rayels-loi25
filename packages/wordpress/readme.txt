=== Loi 25 Quebec ===
Contributors: rayelsconsulting
Tags: law 25, quebec, cookie consent, privacy, cookies
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 2.0.1
Requires PHP: 7.4
License: MIT
License URI: https://opensource.org/licenses/MIT

Quebec Law 25 (Bill 64) cookie consent banner. Google Consent Mode v2, 3 styles, bilingual.

== Description ==

Quebec's **Law 25** (formerly Bill 64) requires all websites collecting personal information from Quebec residents to obtain explicit consent before using cookies and tracking scripts.

**Loi 25 Cookie Consent** is a consent plugin built for Quebec businesses.

> Features include Google Consent Mode v2, 3 banner styles, and bilingual support.

= Why Choose This Plugin? =

* **Zero config needed** — works out of the box
* **Google Consent Mode v2** — integration with Google Ads & Analytics
* **3 banner styles** — full-width bar, centered popup, or corner widget
* **Glassmorphism mode** — modern frosted glass effect
* **Bilingual** — French (default) and English with auto-detection
* **Custom text** — override strings in both languages
* **Brand color picker** — match your website's colors
* **Consent expiry** — auto re-ask after X days
* **Re-consent button** — floating cookie icon so visitors can change their mind
* **Consent statistics** — dashboard widget showing accept/reject rates
* **Smooth animations** — slide or fade transitions
* **Custom CSS** — styling control
* **Accessible** — keyboard navigation, ARIA labels, focus management
* **Auto cache flush** — clears site cache when settings change, compatible with WP Rocket, LiteSpeed, W3TC, and more
* **Clean uninstall** — removes all data when deleted
* **Zero dependencies** — no external scripts

= Who Is This For? =

*   **Quebec businesses** that need to comply with Loi 25
*   **Web agencies** deploying consent banners for clients
*   **WordPress developers** who want a lightweight, customizable solution

= Built by Rayels Consulting =

[Rayels Consulting](https://rayelsconsulting.com/tools/loi25-wordpress-plugin) is a Montreal-based digital agency. We built this plugin to help Quebec businesses comply with Loi 25.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/rayels-loi25` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to **Settings > Loi 25** to configure your banner.
4. Done! The banner appears automatically for new visitors.

== Frequently Asked Questions ==

= Is this really free? =
Yes, open source (MIT license).

= Does it work with Google Tag Manager? =
Yes. Enable **Google Consent Mode v2** in settings. It automatically manages `ad_storage`, `analytics_storage`, `ad_user_data`, and `ad_personalization` consent signals.

= Can I customize the text? =
Yes. The **Custom Text** tab lets you override the title, message, and button text in both French and English.

= Does it slow down my site? =
No. The entire plugin uses vanilla JavaScript with zero external dependencies.

= What happens when I uninstall? =
All plugin data (settings and statistics) is completely removed from your database.

= Can visitors change their consent? =
Yes. A small floating cookie button lets visitors re-open the banner at any time.

= Will my changes show immediately if I use a caching plugin? =
Yes. The plugin automatically flushes the cache from popular caching plugins (WP Rocket, LiteSpeed, W3 Total Cache, WP Super Cache, and more) whenever you save settings.

== Screenshots ==

1. The consent banner in "Bar" style with light theme.
2. The consent banner in "Popup" style with dark theme and glassmorphism.
3. The consent banner in "Corner" widget style.
4. The settings page — General tab.
5. The Consent Statistics dashboard widget.
6. Custom text settings for both French and English.

== Changelog ==

= 2.0.1 =
*   Update: Removed Script Vault feature to comply with WordPress.org guidelines.
*   Update: Improved security with proper escaping and asset enqueuing.
*   Update: Changed "Powered By" link to be opt-in by default.
*   Fix: Minor bug fixes and improvements.

= 2.0.0 =
*   NEW: 3 banner styles — bar, popup, corner widget.
*   NEW: Glassmorphism (frosted glass) mode.
*   NEW: Consent expiry with configurable days.
*   NEW: Re-consent floating cookie button.
*   NEW: Custom text override for FR and EN.
*   NEW: Consent statistics dashboard widget.
*   NEW: Smooth animations (slide/fade).
*   NEW: Auto language detection from WordPress locale.
*   NEW: Custom CSS field.
*   NEW: Google Consent Mode v2 with full signal support.
*   NEW: Accessibility improvements (ARIA, keyboard, focus).
*   NEW: Clean uninstall removes all data.
*   NEW: Modern tabbed admin settings page.
*   NEW: Auto cache flush on settings save.
*   IMPROVED: Brand color now applies to re-consent button too.

= 1.1.0 =
*   Added brand color picker.
*   Added Google Consent Mode v2 support.

= 1.0.0 =
*   Initial release.
