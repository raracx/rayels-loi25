<?php
/**
 * Plugin Name: Loi 25 Quebec Cookie Consent — by Rayels Consulting
 * Plugin URI: https://rayelsconsulting.com
 * Description: Banniere de consentement aux cookies conforme a la Loi 25 du Quebec. Bilingue (FR/EN), leger, plug & play. Cookie consent banner compliant with Quebec's Law 25.
 * Version: 1.0.0
 * Author: Rayels Consulting
 * Author URI: https://rayelsconsulting.com
 * License: MIT
 * Text Domain: rayels-loi25
 */

if (!defined('ABSPATH')) exit;

class Rayels_Loi25 {

    public function __construct() {
        add_action('wp_footer', array($this, 'inject_banner'));
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function inject_banner() {
        $lang = get_option('rayels_loi25_lang', 'fr');
        $position = get_option('rayels_loi25_position', 'bottom');
        $theme = get_option('rayels_loi25_theme', 'light');
        $privacy_url = get_option('rayels_loi25_privacy_url', '/politique-de-confidentialite');
        $powered_by = get_option('rayels_loi25_powered_by', '1');
        ?>
        <script>
        (function(){
            var STORAGE_KEY='loi25-consent';
            try{if(localStorage.getItem(STORAGE_KEY))return;}catch(e){return;}

            var lang='<?php echo esc_js($lang); ?>';
            var texts={
                fr:{title:'Respect de votre vie privee',message:'Ce site utilise des cookies pour ameliorer votre experience. Conformement a la Loi 25 du Quebec, nous demandons votre consentement.',acceptAll:'Tout accepter',acceptNecessary:'Necessaires seulement',privacy:'Politique de confidentialite',poweredBy:'Propulse par'},
                en:{title:'Your Privacy Matters',message:'This website uses cookies to improve your experience. In compliance with Quebec\'s Law 25, we ask for your consent.',acceptAll:'Accept All',acceptNecessary:'Necessary Only',privacy:'Privacy Policy',poweredBy:'Powered by'}
            };
            var t=texts[lang]||texts.fr;
            var dk=<?php echo $theme==='dark'?'true':'false'; ?>;
            var bg=dk?'#18181b':'#fff';
            var tc=dk?'#e4e4e7':'#1e293b';
            var mc=dk?'#a1a1aa':'#64748b';
            var bc=dk?'#27272a':'#e2e8f0';

            var d=document.createElement('div');
            d.id='loi25-banner';
            d.style.cssText='position:fixed;left:0;right:0;<?php echo $position==='top'?'top:0':'bottom:0'; ?>;z-index:999999;background:'+bg+';border-<?php echo $position==='top'?'bottom':'top'; ?>:1px solid '+bc+';padding:20px 24px;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;font-size:14px;color:'+tc+';box-shadow:0 -2px 20px rgba(0,0,0,.08)';

            var html='<div style="max-width:960px;margin:0 auto">'
                +'<div style="font-weight:700;font-size:16px;margin-bottom:8px">'+t.title+'</div>'
                +'<p style="margin:0 0 16px;color:'+mc+';line-height:1.5">'+t.message+'</p>'
                +'<div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center">'
                +'<button id="loi25-yes" style="background:#1d4ed8;color:#fff;border:none;padding:10px 20px;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer">'+t.acceptAll+'</button>'
                +'<button id="loi25-no" style="background:'+(dk?'#27272a':'#f1f5f9')+';color:'+(dk?'#e4e4e7':'#334155')+';border:1px solid '+bc+';padding:10px 20px;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer">'+t.acceptNecessary+'</button>'
                +'<a href="<?php echo esc_url($privacy_url); ?>" style="color:'+mc+';font-size:12px;text-decoration:underline;margin-left:8px">'+t.privacy+'</a>'
                <?php if($powered_by === '1'): ?>
                +'<a href="https://rayelsconsulting.com" target="_blank" rel="noopener" style="color:'+mc+';font-size:11px;margin-left:auto;text-decoration:none;opacity:.7">'+t.poweredBy+' Rayels</a>'
                <?php endif; ?>
                +'</div></div>';

            d.innerHTML=html;
            document.body.appendChild(d);

            function accept(level){
                try{localStorage.setItem(STORAGE_KEY,level);localStorage.setItem('loi25-consent-date',Date.now());}catch(e){}
                d.remove();
            }
            document.getElementById('loi25-yes').onclick=function(){accept('all');};
            document.getElementById('loi25-no').onclick=function(){accept('necessary');};
        })();
        </script>
        <?php
    }

    public function add_settings_page() {
        add_options_page(
            'Loi 25 Cookie Consent',
            'Loi 25 Cookies',
            'manage_options',
            'rayels-loi25',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting('rayels_loi25_settings', 'rayels_loi25_lang');
        register_setting('rayels_loi25_settings', 'rayels_loi25_position');
        register_setting('rayels_loi25_settings', 'rayels_loi25_theme');
        register_setting('rayels_loi25_settings', 'rayels_loi25_privacy_url');
        register_setting('rayels_loi25_settings', 'rayels_loi25_powered_by');
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Loi 25 Cookie Consent — by Rayels Consulting</h1>
            <p>Configure your Loi 25 cookie consent banner. <a href="https://rayelsconsulting.com" target="_blank">rayelsconsulting.com</a></p>
            <form method="post" action="options.php">
                <?php settings_fields('rayels_loi25_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th>Language / Langue</th>
                        <td>
                            <select name="rayels_loi25_lang">
                                <option value="fr" <?php selected(get_option('rayels_loi25_lang','fr'),'fr'); ?>>Francais</option>
                                <option value="en" <?php selected(get_option('rayels_loi25_lang','fr'),'en'); ?>>English</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Position</th>
                        <td>
                            <select name="rayels_loi25_position">
                                <option value="bottom" <?php selected(get_option('rayels_loi25_position','bottom'),'bottom'); ?>>Bottom</option>
                                <option value="top" <?php selected(get_option('rayels_loi25_position','bottom'),'top'); ?>>Top</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Theme</th>
                        <td>
                            <select name="rayels_loi25_theme">
                                <option value="light" <?php selected(get_option('rayels_loi25_theme','light'),'light'); ?>>Light</option>
                                <option value="dark" <?php selected(get_option('rayels_loi25_theme','light'),'dark'); ?>>Dark</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Privacy Policy URL</th>
                        <td><input type="text" name="rayels_loi25_privacy_url" value="<?php echo esc_attr(get_option('rayels_loi25_privacy_url','/politique-de-confidentialite')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th>"Powered by Rayels" link</th>
                        <td>
                            <select name="rayels_loi25_powered_by">
                                <option value="1" <?php selected(get_option('rayels_loi25_powered_by','1'),'1'); ?>>Show</option>
                                <option value="0" <?php selected(get_option('rayels_loi25_powered_by','1'),'0'); ?>>Hide</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

new Rayels_Loi25();
