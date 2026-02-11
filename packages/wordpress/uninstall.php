<?php
/**
 * Uninstall — Loi 25 Quebec Cookie Consent
 * Removes all plugin data from the database.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) exit;

// Delete all options
$options = array(
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

foreach ($options as $opt) {
    delete_option($opt);
}

// Drop statistics table
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}loi25_stats");
