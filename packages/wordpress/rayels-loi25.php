<?php
/**
 * Plugin Name: Loi 25 Quebec Cookie Consent — by Rayels Consulting
 * Plugin URI: https://rayelsconsulting.com/tools/loi25-wordpress-plugin
 * Description: The most complete FREE Loi 25 (Bill 64) cookie consent solution for Quebec. 100% free — no premium version. Script blocking, Google Consent Mode v2, 3 banner styles, bilingual, zero dependencies.
 * Version: 2.0.0
 * Author: Rayels Consulting
 * Author URI: https://rayelsconsulting.com
 * License: MIT
 * Text Domain: rayels-loi25
 * Requires at least: 5.0
 * Tested up to: 6.7
 * Requires PHP: 7.2
 */

if (!defined('ABSPATH')) exit;

class Rayels_Loi25 {

    private $version = '2.0.0';

    public function __construct() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('wp_footer', array($this, 'inject_banner'));
        add_action('wp_head', array($this, 'inject_head'), 1);
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
        add_action('wp_ajax_loi25_log_consent', array($this, 'ajax_log_consent'));
        add_action('wp_ajax_nopriv_loi25_log_consent', array($this, 'ajax_log_consent'));
        register_activation_hook(__FILE__, array($this, 'activate'));
    }

    // ─── Load translations ───
    public function load_textdomain() {
        load_plugin_textdomain('rayels-loi25', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    // ─── Activation: Create stats table ───
    public function activate() {
        global $wpdb;
        $table = $wpdb->prefix . 'loi25_stats';
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
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
        if (!in_array($type, array('all', 'necessary'))) {
            wp_send_json_error();
        }
        global $wpdb;
        $table = $wpdb->prefix . 'loi25_stats';
        $ip_hash = hash('sha256', $_SERVER['REMOTE_ADDR'] . wp_salt());
        $wpdb->insert($table, array(
            'consent_type' => $type,
            'ip_hash' => $ip_hash,
        ));
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
            'powered_by'      => get_option('rayels_loi25_powered_by', '1'),
            'brand_color'     => get_option('rayels_loi25_brand_color', '#1d4ed8'),
            'consent_mode'    => get_option('rayels_loi25_consent_mode', '0'),
            'expiry_days'     => intval(get_option('rayels_loi25_expiry', 365)),
            'show_reconsent'  => get_option('rayels_loi25_reconsent', '1'),
            'animation'       => get_option('rayels_loi25_animation', 'slide'),
            'custom_css'      => get_option('rayels_loi25_custom_css', ''),
            'scripts_analytics' => get_option('rayels_loi25_scripts_analytics', ''),
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

    // ─── HEAD injection: Google Consent Mode + blocked scripts ───
    public function inject_head() {
        $o = $this->get_opts();

        // Google Consent Mode v2
        if ($o['consent_mode'] === '1') {
            ?>
            <script>
            window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
            (function(){var c=localStorage.getItem('loi25-consent');
            if(!c){gtag('consent','default',{'ad_storage':'denied','ad_user_data':'denied','ad_personalization':'denied','analytics_storage':'denied'});}
            else if(c==='all'){gtag('consent','default',{'ad_storage':'granted','ad_user_data':'granted','ad_personalization':'granted','analytics_storage':'granted'});}
            else{gtag('consent','default',{'ad_storage':'denied','ad_user_data':'denied','ad_personalization':'denied','analytics_storage':'denied'});}
            })();
            </script>
            <?php
        }

        // Script Vault: inject analytics scripts ONLY if consent === 'all'
        $scripts = trim($o['scripts_analytics']);
        if (!empty($scripts)) {
            ?>
            <script>
            (function(){
                if(localStorage.getItem('loi25-consent')==='all'){
                    var s=<?php echo json_encode($scripts); ?>;
                    var tmp=document.createElement('div');tmp.innerHTML=s;
                    var els=tmp.querySelectorAll('script');
                    for(var i=0;i<els.length;i++){
                        var ns=document.createElement('script');
                        if(els[i].src){ns.src=els[i].src;}
                        else{ns.textContent=els[i].text||els[i].textContent||'';}
                        var attrs=els[i].attributes;
                        for(var j=0;j<attrs.length;j++){
                            if(attrs[j].name!=='src')ns.setAttribute(attrs[j].name,attrs[j].value);
                        }
                        document.head.appendChild(ns);
                    }
                }
            })();
            </script>
            <?php
        }
    }

    // ─── FOOTER injection: Banner + Re-consent button ───
    public function inject_banner() {
        $o = $this->get_opts();

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
            'hasScripts' => !empty(trim($o['scripts_analytics'])),
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
        ?>

        <!-- Loi 25 Quebec Cookie Consent v<?php echo $this->version; ?> — by Rayels Consulting (rayelsconsulting.com) -->
        <a href="https://rayelsconsulting.com" rel="noopener" style="position:absolute;left:-9999px;opacity:0;pointer-events:none;" tabindex="-1" aria-hidden="true">Loi 25 Cookie Consent by Rayels Consulting</a>

        <?php if (!empty($o['custom_css'])): ?>
        <style><?php echo wp_strip_all_tags($o['custom_css']); ?></style>
        <?php endif; ?>

        <style>
        #loi25-banner{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;line-height:1.5;box-sizing:border-box;}
        #loi25-banner *{box-sizing:border-box;margin:0;padding:0;}
        #loi25-banner button{cursor:pointer;transition:transform .15s,opacity .15s;}
        #loi25-banner button:hover{transform:translateY(-1px);opacity:.9;}
        #loi25-banner button:focus-visible,#loi25-banner a:focus-visible{outline:2px solid #1d4ed8;outline-offset:2px;}
        #loi25-banner.loi25-anim-slide{transition:transform .4s cubic-bezier(.4,0,.2,1),opacity .4s ease;}
        #loi25-banner.loi25-anim-fade{transition:opacity .5s ease;}
        #loi25-banner.loi25-hidden-bottom{transform:translateY(100%);opacity:0;}
        #loi25-banner.loi25-hidden-top{transform:translateY(-100%);opacity:0;}
        #loi25-banner.loi25-hidden-fade{opacity:0;}
        #loi25-banner.loi25-hidden-popup{opacity:0;transform:scale(.9);}
        #loi25-banner.loi25-style-popup{transition:transform .35s cubic-bezier(.4,0,.2,1),opacity .35s ease;}
        #loi25-banner.loi25-style-corner{transition:transform .35s cubic-bezier(.4,0,.2,1),opacity .35s ease;}
        #loi25-banner.loi25-glass{backdrop-filter:blur(16px) saturate(1.8);-webkit-backdrop-filter:blur(16px) saturate(1.8);}
        #loi25-reconsent{position:fixed;bottom:20px;left:20px;z-index:999998;width:44px;height:44px;border-radius:50%;border:none;background:#1d4ed8;color:#fff;font-size:20px;cursor:pointer;box-shadow:0 4px 12px rgba(0,0,0,.15);transition:transform .2s,opacity .3s;display:flex;align-items:center;justify-content:center;}
        #loi25-reconsent:hover{transform:scale(1.1);}
        @media(max-width:600px){
            #loi25-banner .loi25-inner{padding:16px!important;}
            #loi25-banner .loi25-btns{flex-direction:column!important;}
            #loi25-banner .loi25-btns button{width:100%!important;}
        }
        </style>

        <script>
        (function(){
            'use strict';
            var CFG=<?php echo json_encode($js_config); ?>;
            var SK='loi25-consent',SD='loi25-consent-date';

            // ─── Check expiry ───
            function isExpired(){
                var d=localStorage.getItem(SD);
                if(!d)return true;
                var age=(Date.now()-parseInt(d,10))/(1000*60*60*24);
                return age>CFG.expiry;
            }
            function hasConsent(){
                try{return localStorage.getItem(SK)&&!isExpired();}catch(e){return false;}
            }

            // ─── Re-consent floating button ───
            function showReconsent(){
                if(!CFG.reconsent)return;
                var rb=document.createElement('button');
                rb.id='loi25-reconsent';
                rb.setAttribute('aria-label',CFG.lang==='fr'?'Gérer les cookies':'Manage cookies');
                rb.innerHTML=CFG.showIcon?'🍪':'⚙️';
                rb.style.background=CFG.brand;
                rb.onclick=function(){
                    try{localStorage.removeItem(SK);localStorage.removeItem(SD);}catch(e){}
                    rb.remove();
                    showBanner();
                };
                document.body.appendChild(rb);
            }

            // ─── If already consented ───
            if(hasConsent()){
                showReconsent();
                return;
            }

            // ─── If expired, clear old consent ───
            if(localStorage.getItem(SK)&&isExpired()){
                try{localStorage.removeItem(SK);localStorage.removeItem(SD);}catch(e){}
            }

            // ─── Theme colors ───
            var dk=CFG.theme==='dark';
            var colors={
                bg:dk?'rgba(24,24,27,'+(CFG.glass?'.75':'1')+')':'rgba(255,255,255,'+(CFG.glass?'.8':'1')+')',
                text:dk?'#e4e4e7':'#1e293b',
                muted:dk?'#a1a1aa':'#64748b',
                border:dk?'#3f3f46':'#e2e8f0',
                btnBg:dk?'#27272a':'#f1f5f9',
                btnText:dk?'#e4e4e7':'#334155',
            };

            function showBanner(){
                // Cleanup any existing banner/overlay first
                var old=document.getElementById('loi25-banner');if(old)old.remove();
                var oldOv=document.getElementById('loi25-overlay');if(oldOv)oldOv.remove();

                var d=document.createElement('div');
                d.id='loi25-banner';
                d.setAttribute('role','dialog');
                d.setAttribute('aria-label',CFG.lang==='fr'?'Consentement aux cookies':'Cookie consent');
                d.setAttribute('aria-modal','false');

                // ─── Style: Bar ───
                if(CFG.style==='bar'){
                    d.style.cssText='position:fixed;left:0;right:0;'+(CFG.position==='top'?'top:0':'bottom:0')+';z-index:999999;background:'+colors.bg+';border-'+(CFG.position==='top'?'bottom':'top')+':1px solid '+colors.border+';padding:0;color:'+colors.text+';box-shadow:0 '+(CFG.position==='top'?'2':'-2')+'px 20px rgba(0,0,0,.1);';
                    if(CFG.animation==='slide'){
                        d.classList.add('loi25-anim-slide','loi25-hidden-'+CFG.position);
                    }else{
                        d.classList.add('loi25-anim-fade','loi25-hidden-fade');
                    }
                }
                // ─── Style: Popup ───
                else if(CFG.style==='popup'){
                    d.style.cssText='position:fixed;top:50%;left:50%;transform:translate(-50%,-50%) scale(.9);z-index:999999;background:'+colors.bg+';border-radius:16px;padding:0;color:'+colors.text+';box-shadow:0 25px 60px rgba(0,0,0,.2);max-width:480px;width:calc(100% - 40px);';
                    d.classList.add('loi25-style-popup','loi25-hidden-popup');
                    // Overlay
                    var overlay=document.createElement('div');
                    overlay.id='loi25-overlay';
                    overlay.style.cssText='position:fixed;top:0;left:0;right:0;bottom:0;z-index:999998;background:rgba(0,0,0,.4);opacity:0;transition:opacity .35s ease;';
                    document.body.appendChild(overlay);
                    requestAnimationFrame(function(){overlay.style.opacity='1';});
                }
                // ─── Style: Corner ───
                else if(CFG.style==='corner'){
                    d.style.cssText='position:fixed;'+(CFG.position==='top'?'top:20px':'bottom:20px')+';right:20px;z-index:999999;background:'+colors.bg+';border-radius:16px;padding:0;color:'+colors.text+';box-shadow:0 8px 30px rgba(0,0,0,.12);max-width:380px;width:calc(100% - 40px);border:1px solid '+colors.border+';';
                    d.classList.add('loi25-style-corner');
                    if(CFG.animation==='slide'){
                        d.style.transform='translateX(120%)';d.style.opacity='0';
                        d.style.transition='transform .4s cubic-bezier(.4,0,.2,1),opacity .4s ease';
                    }else{
                        d.classList.add('loi25-hidden-fade');
                        d.style.transition='opacity .5s ease';
                    }
                }

                if(CFG.glass)d.classList.add('loi25-glass');

                // ─── Inner HTML ───
                var T=CFG.texts;
                var inner='<div class="loi25-inner" style="padding:24px 28px;">'
                    +'<div style="font-weight:700;font-size:17px;margin-bottom:10px;display:flex;align-items:center;gap:8px;">'
                    +(CFG.showIcon?'<span style="font-size:22px;">🍪</span> ':'')+escHtml(T.title)+'</div>'
                    +'<p style="margin:0 0 18px;color:'+colors.muted+';font-size:14px;line-height:1.6;">'+escHtml(T.message)+'</p>'
                    +'<div class="loi25-btns" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">'
                    +'<button id="loi25-yes" style="background:'+CFG.brand+';color:#fff;border:none;padding:11px 24px;border-radius:8px;font-weight:600;font-size:14px;">'+escHtml(T.accept)+'</button>'
                    +'<button id="loi25-no" style="background:'+colors.btnBg+';color:'+colors.btnText+';border:1px solid '+colors.border+';padding:11px 24px;border-radius:8px;font-weight:600;font-size:14px;">'+escHtml(T.reject)+'</button>'
                    +'</div>'
                    +'<div style="margin-top:14px;display:flex;flex-wrap:wrap;gap:12px;align-items:center;">'
                    +'<a href="'+CFG.privacyUrl+'" style="color:'+colors.muted+';font-size:12px;text-decoration:underline;" target="_blank" rel="noopener">'+escHtml(T.privacy)+'</a>';

                if(CFG.poweredBy){
                    inner+='<a href="https://rayelsconsulting.com" target="_blank" rel="noopener" style="color:'+colors.muted+';font-size:11px;margin-left:auto;text-decoration:none;opacity:.6;">'+escHtml(T.powered)+' Rayels</a>';
                }
                inner+='</div></div>';

                d.innerHTML=inner;
                document.body.appendChild(d);

                // ─── Animate in ───
                requestAnimationFrame(function(){requestAnimationFrame(function(){
                    if(CFG.style==='bar'){
                        d.classList.remove('loi25-hidden-'+CFG.position,'loi25-hidden-fade');
                    }else if(CFG.style==='popup'){
                        d.classList.remove('loi25-hidden-popup');
                        d.style.transform='translate(-50%,-50%) scale(1)';
                        d.style.opacity='1';
                    }else if(CFG.style==='corner'){
                        d.style.transform='translateX(0)';d.style.opacity='1';
                        d.classList.remove('loi25-hidden-fade');
                    }
                });});

                // ─── Accept handler ───
                function accept(level){
                    try{
                        localStorage.setItem(SK,level);
                        localStorage.setItem(SD,Date.now().toString());
                    }catch(e){}

                    // Animate out
                    if(CFG.style==='bar'){
                        d.classList.add('loi25-hidden-'+CFG.position);
                    }else if(CFG.style==='popup'){
                        d.style.transform='translate(-50%,-50%) scale(.9)';d.style.opacity='0';
                        var ov=document.getElementById('loi25-overlay');
                        if(ov)ov.style.opacity='0';
                    }else if(CFG.style==='corner'){
                        d.style.transform='translateX(120%)';d.style.opacity='0';
                    }

                    setTimeout(function(){
                        d.remove();
                        var ov=document.getElementById('loi25-overlay');
                        if(ov)ov.remove();
                        showReconsent();
                    },400);

                    // Google Consent Mode update
                    if(window.gtag){
                        if(level==='all'){
                            gtag('consent','update',{'ad_storage':'granted','ad_user_data':'granted','ad_personalization':'granted','analytics_storage':'granted'});
                        }
                    }

                    // Load blocked scripts
                    if(level==='all'&&CFG.hasScripts){
                        window.location.reload();
                    }

                    // Log consent via AJAX
                    try{
                        var xhr=new XMLHttpRequest();
                        xhr.open('POST',CFG.ajaxUrl,true);
                        xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
                        xhr.send('action=loi25_log_consent&type='+level);
                    }catch(e){}
                }

                document.getElementById('loi25-yes').onclick=function(){accept('all');};
                document.getElementById('loi25-no').onclick=function(){accept('necessary');};

                // ─── Keyboard: Escape = Necessary Only ───
                document.addEventListener('keydown',function handler(e){
                    if(e.key==='Escape'){
                        accept('necessary');
                        document.removeEventListener('keydown',handler);
                    }
                });

                // Focus first button for accessibility
                setTimeout(function(){
                    var b=document.getElementById('loi25-yes');
                    if(b)b.focus();
                },500);
            }

            function escHtml(s){
                var d=document.createElement('div');d.textContent=s;return d.innerHTML;
            }

            showBanner();
        })();
        </script>
        <?php
    }

    // ─── Dashboard Widget ───
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'loi25_stats_widget',
            '🍪 ' . __('Loi 25 — Consent Statistics', 'rayels-loi25'),
            array($this, 'render_dashboard_widget')
        );
    }

    public function render_dashboard_widget() {
        global $wpdb;
        $table = $wpdb->prefix . 'loi25_stats';

        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") !== $table) {
            echo '<p>' . esc_html__('No data yet. Statistics will appear once visitors interact with the consent banner.', 'rayels-loi25') . '</p>';
            return;
        }

        $total   = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table");
        $all     = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE consent_type='all'");
        $nec     = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE consent_type='necessary'");
        $today   = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE DATE(created_at)=%s", current_time('Y-m-d')));
        $pct_all = $total > 0 ? round(($all / $total) * 100) : 0;
        $pct_nec = $total > 0 ? round(($nec / $total) * 100) : 0;
        ?>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
            <div style="text-align:center;padding:16px;background:#f0fdf4;border-radius:8px;">
                <div style="font-size:28px;font-weight:700;color:#16a34a;"><?php echo $all; ?></div>
                <div style="font-size:12px;color:#64748b;"><?php echo esc_html__('Accept All', 'rayels-loi25'); ?> (<?php echo $pct_all; ?>%)</div>
            </div>
            <div style="text-align:center;padding:16px;background:#fef2f2;border-radius:8px;">
                <div style="font-size:28px;font-weight:700;color:#dc2626;"><?php echo $nec; ?></div>
                <div style="font-size:12px;color:#64748b;"><?php echo esc_html__('Necessary Only', 'rayels-loi25'); ?> (<?php echo $pct_nec; ?>%)</div>
            </div>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px;color:#64748b;">
            <span><?php echo esc_html__('Total', 'rayels-loi25'); ?>: <strong><?php echo $total; ?></strong></span>
            <span><?php echo esc_html__('Today', 'rayels-loi25'); ?>: <strong><?php echo $today; ?></strong></span>
        </div>
        <div style="margin-top:12px;height:8px;background:#e2e8f0;border-radius:99px;overflow:hidden;">
            <div style="height:100%;width:<?php echo $pct_all; ?>%;background:linear-gradient(90deg,#16a34a,#22c55e);border-radius:99px;transition:width .5s;"></div>
        </div>
        <p style="margin-top:12px;font-size:11px;color:#94a3b8;">Data by <a href="https://rayelsconsulting.com" target="_blank">Rayels Consulting</a></p>
        <?php
    }

    // ─── Admin Settings Page ───
    public function add_settings_page() {
        add_options_page(
            __('Loi 25 Cookie Consent', 'rayels-loi25'),
            '🍪 ' . __('Loi 25', 'rayels-loi25'),
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
            'rayels_loi25_custom_css', 'rayels_loi25_scripts_analytics',
            'rayels_loi25_title_fr', 'rayels_loi25_title_en',
            'rayels_loi25_message_fr', 'rayels_loi25_message_en',
            'rayels_loi25_btn_accept_fr', 'rayels_loi25_btn_accept_en',
            'rayels_loi25_btn_reject_fr', 'rayels_loi25_btn_reject_en',
            'rayels_loi25_show_icon',
        );
        foreach ($fields as $f) {
            register_setting('rayels_loi25_settings', $f);
        }
    }

    public function render_settings_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        ?>
        <style>
            .loi25-admin{max-width:900px;}
            .loi25-admin h1{display:flex;align-items:center;gap:10px;font-size:24px;}
            .loi25-admin .loi25-badge{background:linear-gradient(135deg,#1d4ed8,#7c3aed);color:#fff;font-size:11px;padding:3px 10px;border-radius:99px;font-weight:500;}
            .loi25-tabs{display:flex;gap:0;border-bottom:2px solid #e2e8f0;margin:20px 0 0;}
            .loi25-tab{padding:10px 20px;cursor:pointer;color:#64748b;font-weight:500;border-bottom:2px solid transparent;margin-bottom:-2px;text-decoration:none;font-size:14px;transition:all .15s;}
            .loi25-tab:hover{color:#1d4ed8;}
            .loi25-tab.active{color:#1d4ed8;border-bottom-color:#1d4ed8;font-weight:600;}
            .loi25-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;margin-top:20px;}
            .loi25-card h2{font-size:17px;margin:0 0 4px;display:flex;align-items:center;gap:8px;}
            .loi25-card p.desc{color:#64748b;font-size:13px;margin:0 0 20px;}
            .loi25-field{margin-bottom:20px;}
            .loi25-field label{display:block;font-weight:600;font-size:13px;margin-bottom:6px;color:#334155;}
            .loi25-field .hint{font-size:12px;color:#94a3b8;margin-top:4px;}
            .loi25-field input[type="text"],.loi25-field textarea,.loi25-field select{width:100%;max-width:500px;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;transition:border-color .15s;}
            .loi25-field input[type="text"]:focus,.loi25-field textarea:focus,.loi25-field select:focus{border-color:#1d4ed8;outline:none;box-shadow:0 0 0 3px rgba(29,78,216,.1);}
            .loi25-field textarea{min-height:100px;font-family:monospace;font-size:13px;}
            .loi25-field input[type="color"]{width:50px;height:36px;border:1px solid #d1d5db;border-radius:8px;padding:2px;cursor:pointer;}
            .loi25-field input[type="number"]{width:100px;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;}
            .loi25-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
            @media(max-width:782px){.loi25-row{grid-template-columns:1fr;}}
            .loi25-footer{margin-top:24px;padding:16px 0;border-top:1px solid #e2e8f0;font-size:12px;color:#94a3b8;text-align:center;}
        </style>

        <div class="wrap loi25-admin">
            <h1>🍪 Loi 25 Cookie Consent <span class="loi25-badge">v<?php echo $this->version; ?></span></h1>
            <p>The most complete <strong>100% free</strong> Loi 25 compliance solution. By <a href="https://rayelsconsulting.com" target="_blank" style="color:#1d4ed8;text-decoration:none;font-weight:500;">Rayels Consulting</a></p>

            <div class="loi25-tabs">
                <a href="?page=rayels-loi25&tab=general" class="loi25-tab <?php echo $active_tab === 'general' ? 'active' : ''; ?>">⚙️ General</a>
                <a href="?page=rayels-loi25&tab=appearance" class="loi25-tab <?php echo $active_tab === 'appearance' ? 'active' : ''; ?>">🎨 Appearance</a>
                <a href="?page=rayels-loi25&tab=text" class="loi25-tab <?php echo $active_tab === 'text' ? 'active' : ''; ?>">✍️ Custom Text</a>
                <a href="?page=rayels-loi25&tab=scripts" class="loi25-tab <?php echo $active_tab === 'scripts' ? 'active' : ''; ?>">🛡️ Script Vault</a>
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
                            <option value="1" <?php selected(get_option('rayels_loi25_powered_by','1'),'1'); ?>>Show (Recommended ❤️)</option>
                            <option value="0" <?php selected(get_option('rayels_loi25_powered_by','1'),'0'); ?>>Hide</option>
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

                <?php if ($active_tab === 'scripts'): ?>
                <div class="loi25-card">
                    <h2>🛡️ Script Vault — Auto-block Tracking Scripts</h2>
                    <p class="desc">Paste your tracking scripts below. They will <strong>only load after the user clicks "Accept All"</strong>. This is the easiest way to be 100% compliant with Loi 25.</p>

                    <div class="loi25-field">
                        <label>Analytics / Tracking Scripts</label>
                        <textarea name="rayels_loi25_scripts_analytics" rows="10" placeholder="<!-- Paste your Google Analytics, Meta Pixel, or any tracking code here -->"><?php echo esc_textarea(get_option('rayels_loi25_scripts_analytics','')); ?></textarea>
                        <div class="hint">Supports Google Analytics, Google Tag Manager, Meta Pixel, Hotjar, etc. The scripts will be injected into &lt;head&gt; only after consent is granted.</div>
                    </div>

                    <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:14px 18px;font-size:13px;color:#92400e;">
                        ⚠️ <strong>Important:</strong> Remove these tracking scripts from your theme or other plugins to avoid duplicate loading. This vault replaces them.
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
                Loi 25 Cookie Consent v<?php echo $this->version; ?> — Made with ❤️ by <a href="https://rayelsconsulting.com" target="_blank" style="color:#1d4ed8;">Rayels Consulting</a> — Montreal, Quebec
            </div>
        </div>
        <?php
    }
}

new Rayels_Loi25();
