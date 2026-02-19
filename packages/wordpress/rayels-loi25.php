<?php
/**
 * Plugin Name: Loi 25 Quebec
 * Plugin URI: https://rayelsconsulting.com/tools/loi25-wordpress-plugin
 * Description: Loi 25 (Bill 64) cookie consent for Quebec. Google Consent Mode v2, 3 banner styles, bilingual, zero dependencies.
 * Version: 2.0.1
 * Author: Rayels Consulting
 * Author URI: https://rayelsconsulting.com
 * License: MIT
 * Text Domain: loi-25-quebec
 * Requires at least: 5.0
 * Tested up to: 6.9
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) exit;

class Rayels_Loi25 {

    private $version = '2.0.1';
    private $cache_flushed = false;

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'), 1);
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
        add_action('wp_ajax_rayels_loi25_log_consent', array($this, 'ajax_log_consent'));
        add_action('wp_ajax_nopriv_rayels_loi25_log_consent', array($this, 'ajax_log_consent'));
        add_action('updated_option', array($this, 'on_option_update'));
        add_action('admin_notices', array($this, 'cache_flush_notice'));
        register_activation_hook(__FILE__, array($this, 'activate'));
    }

    // ─── Activation: Create stats table ───
    public function activate() {
        global $wpdb;
        $table = $wpdb->prefix . 'rayels_loi25_stats';
        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            consent_type VARCHAR(20) NOT NULL,
            ip_hash VARCHAR(64) DEFAULT '',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // ─── AJAX: Log consent choice ───
    public function ajax_log_consent() {
        // Non-fatal nonce check — cached pages may serve expired nonces
        check_ajax_referer( 'rayels_loi25_consent_nonce', '_nonce', false );

        $type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
        if ( ! in_array( $type, array( 'all', 'necessary' ), true ) ) {
            wp_send_json_error();
        }
        global $wpdb;
        $table   = $wpdb->prefix . 'rayels_loi25_stats';
        $ip_raw  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
        $ip_hash = hash( 'sha256', $ip_raw . wp_salt() );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $wpdb->insert(
            $table,
            array(
                'consent_type' => $type,
                'ip_hash'      => $ip_hash,
            ),
            array( '%s', '%s' )
        );
        wp_send_json_success();
    }

    // ─── Helper: Get all options with defaults ───
    private function get_opts() {
        $wp_locale = substr(get_locale(), 0, 2);
        return array(
            'lang'            => get_option('rayels_loi25_lang', 'auto'),
            'wp_locale'       => $wp_locale,
            'position'        => get_option('rayels_loi25_position', 'bottom'),
            'theme'           => get_option('rayels_loi25_theme', 'light'),
            'style'           => get_option('rayels_loi25_style', 'bar'),
            'glassmorphism'   => get_option('rayels_loi25_glass', '0'),
            'privacy_url'     => get_option('rayels_loi25_privacy_url', '/politique-de-confidentialite'),
            'powered_by'      => get_option('rayels_loi25_powered_by', '0'),
            'brand_color'     => get_option('rayels_loi25_brand_color', '#1d4ed8'),
            'consent_mode'    => get_option('rayels_loi25_consent_mode', '0'),
            'expiry_days'     => intval(get_option('rayels_loi25_expiry', 365)),
            'show_reconsent'  => get_option('rayels_loi25_reconsent', '1'),
            'animation'       => get_option('rayels_loi25_animation', 'slide'),
            'custom_css'      => get_option('rayels_loi25_custom_css', ''),
            'title_fr'        => get_option('rayels_loi25_title_fr', ''),
            'title_en'        => get_option('rayels_loi25_title_en', ''),
            'message_fr'      => get_option('rayels_loi25_message_fr', ''),
            'message_en'      => get_option('rayels_loi25_message_en', ''),
            'btn_accept_fr'   => get_option('rayels_loi25_btn_accept_fr', ''),
            'btn_accept_en'   => get_option('rayels_loi25_btn_accept_en', ''),
            'btn_reject_fr'   => get_option('rayels_loi25_btn_reject_fr', ''),
            'btn_reject_en'   => get_option('rayels_loi25_btn_reject_en', ''),
            'show_cookie_icon' => get_option('rayels_loi25_show_icon', '1'),
        );
    }

    // ─── Enqueue Public Assets ───
    public function enqueue_public_assets() {
        $o = $this->get_opts();

        // ─── Google Consent Mode v2 (loaded in <head>) ───
        if ($o['consent_mode'] === '1') {
            wp_register_script('rayels-loi25-gcm', '', array(), $this->version, false);
            wp_enqueue_script('rayels-loi25-gcm');
            $gcm_js = "window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}"
                . "(function(){var c=localStorage.getItem('loi25-consent');"
                . "if(!c||c!=='all'){gtag('consent','default',{'ad_storage':'denied','ad_user_data':'denied','ad_personalization':'denied','analytics_storage':'denied'});}"
                . "else{gtag('consent','default',{'ad_storage':'granted','ad_user_data':'granted','ad_personalization':'granted','analytics_storage':'granted'});}"
                . "})();";
            wp_add_inline_script('rayels-loi25-gcm', $gcm_js);
        }

        wp_enqueue_style('rayels-loi25-css', plugins_url('assets/css/public.css', __FILE__), array(), $this->version);
        
        if (!empty($o['custom_css'])) {
             wp_add_inline_style('rayels-loi25-css', $o['custom_css']);
        }

        wp_enqueue_script('rayels-loi25-js', plugins_url('assets/js/public.js', __FILE__), array(), $this->version, true);

        // Resolve language
        $lang = $o['lang'] === 'auto' ? (in_array($o['wp_locale'], array('fr', 'en')) ? $o['wp_locale'] : 'fr') : $o['lang'];

        // Default texts
        $defaults = array(
            'fr' => array(
                'title'    => 'Respect de votre vie privée',
                'message'  => 'Ce site utilise des témoins (cookies) pour améliorer votre expérience. Conformément à la Loi 25 du Québec, nous demandons votre consentement.',
                'accept'   => 'Tout accepter',
                'reject'   => 'Nécessaires seulement',
                'privacy'  => 'Politique de confidentialité',
                'powered'  => 'Propulsé par',
            ),
            'en' => array(
                'title'    => 'Your Privacy Matters',
                'message'  => 'This website uses cookies to improve your experience. In compliance with Quebec\'s Law 25, we ask for your consent.',
                'accept'   => 'Accept All',
                'reject'   => 'Necessary Only',
                'privacy'  => 'Privacy Policy',
                'powered'  => 'Powered by',
            ),
        );

        $def = isset($defaults[$lang]) ? $defaults[$lang] : $defaults['fr'];

        // Custom text overrides
        $title   = !empty($o['title_' . $lang])      ? $o['title_' . $lang]      : $def['title'];
        $message = !empty($o['message_' . $lang])    ? $o['message_' . $lang]    : $def['message'];
        $btn_yes = !empty($o['btn_accept_' . $lang]) ? $o['btn_accept_' . $lang] : $def['accept'];
        $btn_no  = !empty($o['btn_reject_' . $lang]) ? $o['btn_reject_' . $lang] : $def['reject'];

        // JSON config for JS
        $js_config = array(
            'lang'       => $lang,
            'position'   => $o['position'],
            'theme'      => $o['theme'],
            'style'      => $o['style'],
            'glass'      => $o['glassmorphism'] === '1',
            'brand'      => $o['brand_color'],
            'expiry'     => $o['expiry_days'],
            'reconsent'  => $o['show_reconsent'] === '1',
            'animation'  => $o['animation'],
            'poweredBy'  => $o['powered_by'] === '1',
            'privacyUrl' => esc_url($o['privacy_url']),
            'ajaxUrl'    => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('rayels_loi25_consent_nonce'),
            'showIcon'   => $o['show_cookie_icon'] === '1',
            'texts'      => array(
                'title'   => $title,
                'message' => $message,
                'accept'  => $btn_yes,
                'reject'  => $btn_no,
                'privacy' => $def['privacy'],
                'powered' => $def['powered'],
            ),
        );

        wp_add_inline_script('rayels-loi25-js', 'window.rayelsLoi25 = ' . wp_json_encode($js_config) . ';', 'before');
    }

    // ─── Enqueue Admin Assets ───
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'rayels-loi25') !== false) {
            wp_enqueue_style('rayels-loi25-admin-css', plugins_url('assets/css/admin.css', __FILE__), array(), $this->version);
        }
    }

    // ─── Dashboard Widget ───
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'rayels_loi25_stats_widget',
            '🍪 ' . __('Loi 25 — Consent Statistics', 'loi-25-quebec'),
            array($this, 'render_dashboard_widget')
        );
    }

    public function render_dashboard_widget() {
        global $wpdb;
        $table = esc_sql( $wpdb->prefix . 'rayels_loi25_stats' );

        // Check if table exists
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
            echo '<p>' . esc_html__('No data yet. Statistics will appear once visitors interact with the consent banner.', 'loi-25-quebec') . '</p>';
            return;
        }

        $cache_key = 'rayels_loi25_stats_widget';
        $stats     = wp_cache_get( $cache_key );

        if ( false === $stats ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name safely constructed from $wpdb->prefix
            $total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$table}`" );
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $all   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `{$table}` WHERE consent_type = %s", 'all' ) );
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $nec   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `{$table}` WHERE consent_type = %s", 'necessary' ) );
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $today = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `{$table}` WHERE DATE(created_at) = %s", current_time( 'Y-m-d' ) ) );

            $stats = array( 'total' => $total, 'all' => $all, 'nec' => $nec, 'today' => $today );
            wp_cache_set( $cache_key, $stats, '', 300 );
        }

        $total   = $stats['total'];
        $all     = $stats['all'];
        $nec     = $stats['nec'];
        $today   = $stats['today'];
        $pct_all = $total > 0 ? round( ( $all / $total ) * 100 ) : 0;
        $pct_nec = $total > 0 ? round( ( $nec / $total ) * 100 ) : 0;
        ?>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
            <div style="text-align:center;padding:16px;background:#f0fdf4;border-radius:8px;">
                <div style="font-size:28px;font-weight:700;color:#16a34a;"><?php echo esc_html( $all ); ?></div>
                <div style="font-size:12px;color:#64748b;"><?php echo esc_html__('Accept All', 'loi-25-quebec'); ?> (<?php echo esc_html( $pct_all ); ?>%)</div>
            </div>
            <div style="text-align:center;padding:16px;background:#fef2f2;border-radius:8px;">
                <div style="font-size:28px;font-weight:700;color:#dc2626;"><?php echo esc_html( $nec ); ?></div>
                <div style="font-size:12px;color:#64748b;"><?php echo esc_html__('Necessary Only', 'loi-25-quebec'); ?> (<?php echo esc_html( $pct_nec ); ?>%)</div>
            </div>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px;color:#64748b;">
            <span><?php echo esc_html__('Total', 'loi-25-quebec'); ?>: <strong><?php echo esc_html( $total ); ?></strong></span>
            <span><?php echo esc_html__('Today', 'loi-25-quebec'); ?>: <strong><?php echo esc_html( $today ); ?></strong></span>
        </div>
        <div style="margin-top:12px;height:8px;background:#e2e8f0;border-radius:99px;overflow:hidden;">
            <div style="height:100%;width:<?php echo esc_attr( $pct_all ); ?>%;background:linear-gradient(90deg,#16a34a,#22c55e);border-radius:99px;transition:width .5s;"></div>
        </div>
        <p style="margin-top:12px;font-size:11px;color:#94a3b8;">Data by <a href="https://rayelsconsulting.com" target="_blank">Rayels Consulting</a></p>
        <?php
    }

    // ─── Admin Settings Page ───
    public function add_settings_page() {
        add_options_page(
            __('Loi 25 Cookie Consent', 'loi-25-quebec'),
            '🍪 ' . __('Loi 25', 'loi-25-quebec'),
            'manage_options',
            'rayels-loi25',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        $fields = array(
            'rayels_loi25_lang', 'rayels_loi25_position', 'rayels_loi25_theme',
            'rayels_loi25_style', 'rayels_loi25_glass', 'rayels_loi25_privacy_url',
            'rayels_loi25_powered_by', 'rayels_loi25_brand_color',
            'rayels_loi25_consent_mode', 'rayels_loi25_expiry',
            'rayels_loi25_reconsent', 'rayels_loi25_animation',
            'rayels_loi25_custom_css',
            'rayels_loi25_title_fr', 'rayels_loi25_title_en',
            'rayels_loi25_message_fr', 'rayels_loi25_message_en',
            'rayels_loi25_btn_accept_fr', 'rayels_loi25_btn_accept_en',
            'rayels_loi25_btn_reject_fr', 'rayels_loi25_btn_reject_en',
            'rayels_loi25_show_icon',
        );
        foreach ($fields as $f) {
            register_setting('rayels_loi25_settings', $f, array('sanitize_callback' => 'sanitize_text_field'));
        }
    }

    // ─── Auto-flush site cache when settings change ───
    public function on_option_update( $option ) {
        if ( $this->cache_flushed || strpos( $option, 'rayels_loi25_' ) !== 0 ) {
            return;
        }
        $this->cache_flushed = true;
        $this->flush_site_cache();
        set_transient( 'rayels_loi25_cache_flushed', true, 30 );
    }

    public function cache_flush_notice() {
        if ( ! get_transient( 'rayels_loi25_cache_flushed' ) ) {
            return;
        }
        delete_transient( 'rayels_loi25_cache_flushed' );
        ?>
        <div class="notice notice-success is-dismissible">
            <p><strong>Loi 25:</strong> Settings saved. Site cache has been automatically cleared — your changes are live now.</p>
        </div>
        <?php
    }

    private function flush_site_cache() {
        // WordPress object cache
        wp_cache_flush();

        // WP Rocket
        if ( function_exists( 'rocket_clean_domain' ) ) {
            rocket_clean_domain();
        }

        // W3 Total Cache
        if ( function_exists( 'w3tc_flush_all' ) ) {
            w3tc_flush_all();
        }

        // WP Super Cache
        if ( function_exists( 'wp_cache_clear_cache' ) ) {
            wp_cache_clear_cache();
        }

        // LiteSpeed Cache
        if ( class_exists( 'LiteSpeed_Cache_API' ) ) {
            LiteSpeed_Cache_API::purge_all();
        }
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- third-party hook
        do_action( 'litespeed_purge_all' );

        // WP Fastest Cache
        if ( function_exists( 'wpfc_clear_all_cache' ) ) {
            wpfc_clear_all_cache();
        } elseif ( isset( $GLOBALS['wp_fastest_cache'] ) && method_exists( $GLOBALS['wp_fastest_cache'], 'deleteCache' ) ) {
            $GLOBALS['wp_fastest_cache']->deleteCache();
        }

        // Autoptimize
        if ( class_exists( 'autoptimizeCache' ) ) {
            autoptimizeCache::clearall();
        }

        // SG Optimizer (SiteGround)
        if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
            sg_cachepress_purge_cache();
        }

        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- third-party hook
        do_action( 'wphb_clear_page_cache' ); // Hummingbird
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- third-party hook
        do_action( 'breeze_clear_all_cache' ); // Breeze (Cloudways)
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- third-party hook
        do_action( 'comet_cache_wipe_cache' ); // Comet Cache
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- third-party hook
        do_action( 'cachify_flush_cache' ); // Cachify
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- third-party hook
        do_action( 'ce_clear_cache' ); // Cache Enabler
    }

    public function render_settings_page() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- tab display only, no data processing
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';
        ?>

        <div class="wrap loi25-admin">
            <h1>🍪 Loi 25 Cookie Consent <span class="loi25-badge">v<?php echo esc_html( $this->version ); ?></span></h1>
            <p>Loi 25 cookie consent compliance for Quebec. By <a href="https://rayelsconsulting.com" target="_blank" style="color:#1d4ed8;text-decoration:none;font-weight:500;">Rayels Consulting</a></p>

            <div class="loi25-tabs">
                <a href="?page=rayels-loi25&tab=general" class="loi25-tab <?php echo $active_tab === 'general' ? 'active' : ''; ?>">⚙️ General</a>
                <a href="?page=rayels-loi25&tab=appearance" class="loi25-tab <?php echo $active_tab === 'appearance' ? 'active' : ''; ?>">🎨 Appearance</a>
                <a href="?page=rayels-loi25&tab=text" class="loi25-tab <?php echo $active_tab === 'text' ? 'active' : ''; ?>">✍️ Custom Text</a>
                <a href="?page=rayels-loi25&tab=advanced" class="loi25-tab <?php echo $active_tab === 'advanced' ? 'active' : ''; ?>">🔧 Advanced</a>
            </div>

            <form method="post" action="options.php">
                <?php settings_fields('rayels_loi25_settings'); ?>

                <?php if ($active_tab === 'general'): ?>
                <div class="loi25-card">
                    <h2>⚙️ General Settings</h2>
                    <p class="desc">Basic configuration for your consent banner.</p>

                    <div class="loi25-field">
                        <label>Language / Langue</label>
                        <select name="rayels_loi25_lang">
                            <option value="auto" <?php selected(get_option('rayels_loi25_lang','auto'),'auto'); ?>>🌐 Auto-detect (WordPress locale)</option>
                            <option value="fr" <?php selected(get_option('rayels_loi25_lang','auto'),'fr'); ?>>🇫🇷 Français</option>
                            <option value="en" <?php selected(get_option('rayels_loi25_lang','auto'),'en'); ?>>🇬🇧 English</option>
                        </select>
                        <div class="hint">Auto-detect uses your WordPress language setting.</div>
                    </div>

                    <div class="loi25-field">
                        <label>Privacy Policy URL</label>
                        <input type="text" name="rayels_loi25_privacy_url" value="<?php echo esc_attr(get_option('rayels_loi25_privacy_url','/politique-de-confidentialite')); ?>" />
                        <div class="hint">Link to your privacy policy page.</div>
                    </div>

                    <div class="loi25-field">
                        <label>Consent Expiry (days)</label>
                        <input type="number" name="rayels_loi25_expiry" value="<?php echo esc_attr(get_option('rayels_loi25_expiry','365')); ?>" min="1" max="730" />
                        <div class="hint">After this many days, the banner will reappear. Default: 365 days.</div>
                    </div>

                    <div class="loi25-field">
                        <label>
                            <input type="checkbox" name="rayels_loi25_consent_mode" value="1" <?php checked(get_option('rayels_loi25_consent_mode','0'), '1'); ?> />
                            Enable Google Consent Mode v2
                        </label>
                        <div class="hint">Automatically manages ad_storage, analytics_storage, ad_user_data, and ad_personalization signals for Google Ads & Analytics.</div>
                    </div>

                    <div class="loi25-field">
                        <label>
                            <input type="checkbox" name="rayels_loi25_reconsent" value="1" <?php checked(get_option('rayels_loi25_reconsent','1'), '1'); ?> />
                            Show re-consent cookie button 🍪
                        </label>
                        <div class="hint">A small floating button lets visitors change their cookie choice at any time.</div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($active_tab === 'appearance'): ?>
                <div class="loi25-card">
                    <h2>🎨 Appearance</h2>
                    <p class="desc">Customize how the banner looks on your site.</p>

                    <div class="loi25-field">
                        <label>Banner Style</label>
                        <select name="rayels_loi25_style">
                            <option value="bar" <?php selected(get_option('rayels_loi25_style','bar'),'bar'); ?>>📏 Full-width Bar</option>
                            <option value="popup" <?php selected(get_option('rayels_loi25_style','bar'),'popup'); ?>>💬 Centered Popup</option>
                            <option value="corner" <?php selected(get_option('rayels_loi25_style','bar'),'corner'); ?>>📌 Corner Widget</option>
                        </select>
                    </div>

                    <div class="loi25-row">
                        <div class="loi25-field">
                            <label>Position</label>
                            <select name="rayels_loi25_position">
                                <option value="bottom" <?php selected(get_option('rayels_loi25_position','bottom'),'bottom'); ?>>Bottom</option>
                                <option value="top" <?php selected(get_option('rayels_loi25_position','bottom'),'top'); ?>>Top</option>
                            </select>
                            <div class="hint">For bar and corner styles.</div>
                        </div>
                        <div class="loi25-field">
                            <label>Theme</label>
                            <select name="rayels_loi25_theme">
                                <option value="light" <?php selected(get_option('rayels_loi25_theme','light'),'light'); ?>>☀️ Light</option>
                                <option value="dark" <?php selected(get_option('rayels_loi25_theme','light'),'dark'); ?>>🌙 Dark</option>
                            </select>
                        </div>
                    </div>

                    <div class="loi25-row">
                        <div class="loi25-field">
                            <label>Brand Color</label>
                            <input type="color" name="rayels_loi25_brand_color" value="<?php echo esc_attr(get_option('rayels_loi25_brand_color','#1d4ed8')); ?>" />
                            <div class="hint">Used for the "Accept" button and cookie icon.</div>
                        </div>
                        <div class="loi25-field">
                            <label>Animation</label>
                            <select name="rayels_loi25_animation">
                                <option value="slide" <?php selected(get_option('rayels_loi25_animation','slide'),'slide'); ?>>Slide</option>
                                <option value="fade" <?php selected(get_option('rayels_loi25_animation','slide'),'fade'); ?>>Fade</option>
                            </select>
                        </div>
                    </div>

                    <div class="loi25-field">
                        <label>
                            <input type="checkbox" name="rayels_loi25_glass" value="1" <?php checked(get_option('rayels_loi25_glass','0'), '1'); ?> />
                            ✨ Enable Glassmorphism (frosted glass effect)
                        </label>
                        <div class="hint">Modern blur effect. Works best with dark theme.</div>
                    </div>

                    <div class="loi25-field">
                        <label>
                            <input type="checkbox" name="rayels_loi25_show_icon" value="1" <?php checked(get_option('rayels_loi25_show_icon','1'), '1'); ?> />
                            🍪 Show cookie emoji in banner
                        </label>
                        <div class="hint">Uncheck for a more professional, minimal look.</div>
                    </div>

                    <div class="loi25-field">
                        <label>"Powered by Rayels" link</label>
                        <select name="rayels_loi25_powered_by">
                            <option value="1" <?php selected(get_option('rayels_loi25_powered_by','0'),'1'); ?>>Show</option>
                            <option value="0" <?php selected(get_option('rayels_loi25_powered_by','0'),'0'); ?>>Hide</option>
                        </select>
                        <div class="hint">Help us grow by keeping the credit link visible!</div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($active_tab === 'text'): ?>
                <div class="loi25-card">
                    <h2>✍️ Custom Banner Text</h2>
                    <p class="desc">Override the default text. Leave empty to use built-in defaults.</p>

                    <h3 style="margin:20px 0 12px;font-size:14px;color:#1d4ed8;">🇫🇷 Français</h3>
                    <div class="loi25-field">
                        <label>Title / Titre</label>
                        <input type="text" name="rayels_loi25_title_fr" value="<?php echo esc_attr(get_option('rayels_loi25_title_fr','')); ?>" placeholder="Respect de votre vie privée" />
                    </div>
                    <div class="loi25-field">
                        <label>Message</label>
                        <input type="text" name="rayels_loi25_message_fr" value="<?php echo esc_attr(get_option('rayels_loi25_message_fr','')); ?>" placeholder="Ce site utilise des témoins (cookies)..." />
                    </div>
                    <div class="loi25-row">
                        <div class="loi25-field">
                            <label>Accept Button</label>
                            <input type="text" name="rayels_loi25_btn_accept_fr" value="<?php echo esc_attr(get_option('rayels_loi25_btn_accept_fr','')); ?>" placeholder="Tout accepter" />
                        </div>
                        <div class="loi25-field">
                            <label>Reject Button</label>
                            <input type="text" name="rayels_loi25_btn_reject_fr" value="<?php echo esc_attr(get_option('rayels_loi25_btn_reject_fr','')); ?>" placeholder="Nécessaires seulement" />
                        </div>
                    </div>

                    <hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0;" />

                    <h3 style="margin:0 0 12px;font-size:14px;color:#1d4ed8;">🇬🇧 English</h3>
                    <div class="loi25-field">
                        <label>Title</label>
                        <input type="text" name="rayels_loi25_title_en" value="<?php echo esc_attr(get_option('rayels_loi25_title_en','')); ?>" placeholder="Your Privacy Matters" />
                    </div>
                    <div class="loi25-field">
                        <label>Message</label>
                        <input type="text" name="rayels_loi25_message_en" value="<?php echo esc_attr(get_option('rayels_loi25_message_en','')); ?>" placeholder="This website uses cookies..." />
                    </div>
                    <div class="loi25-row">
                        <div class="loi25-field">
                            <label>Accept Button</label>
                            <input type="text" name="rayels_loi25_btn_accept_en" value="<?php echo esc_attr(get_option('rayels_loi25_btn_accept_en','')); ?>" placeholder="Accept All" />
                        </div>
                        <div class="loi25-field">
                            <label>Reject Button</label>
                            <input type="text" name="rayels_loi25_btn_reject_en" value="<?php echo esc_attr(get_option('rayels_loi25_btn_reject_en','')); ?>" placeholder="Necessary Only" />
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($active_tab === 'advanced'): ?>
                <div class="loi25-card">
                    <h2>🔧 Advanced</h2>
                    <p class="desc">For developers and power users.</p>

                    <div class="loi25-field">
                        <label>Custom CSS</label>
                        <textarea name="rayels_loi25_custom_css" rows="8" placeholder="#loi25-banner { border-radius: 12px; }"><?php echo esc_textarea(get_option('rayels_loi25_custom_css','')); ?></textarea>
                        <div class="hint">Add custom CSS to style the banner. Target <code>#loi25-banner</code> and <code>#loi25-reconsent</code>.</div>
                    </div>

                    <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:14px 18px;font-size:13px;color:#0c4a6e;margin-top:16px;">
                        💡 <strong>JavaScript API:</strong> You can check consent status anywhere with:<br>
                        <code style="background:#e0f2fe;padding:2px 6px;border-radius:4px;">localStorage.getItem('loi25-consent')</code> — returns <code>'all'</code>, <code>'necessary'</code>, or <code>null</code>.
                    </div>
                </div>
                <?php endif; ?>

                <?php submit_button('Save Settings', 'primary', 'submit', true, array('style' => 'background:#1d4ed8;border-color:#1d4ed8;border-radius:8px;padding:8px 24px;font-weight:600;')); ?>
            </form>

            <div class="loi25-footer">
                Loi 25 Cookie Consent v<?php echo esc_html( $this->version ); ?> — Made with ❤️ by <a href="https://rayelsconsulting.com" target="_blank" style="color:#1d4ed8;">Rayels Consulting</a> — Montreal, Quebec
            </div>
        </div>
        <?php
    }
}

new Rayels_Loi25();
