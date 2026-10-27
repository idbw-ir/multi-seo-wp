<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// حذف تنظیمات افزونه
delete_option('ms_seo_defaults');

// حذف تمام متاهای تولید شده توسط افزونه
global $wpdb;

// حذف متاهای Yoast
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_ms_seo_generated_yoast_%'");

// حذف متاهای Rank Math
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_ms_seo_generated_rank_math_%'");

// حذف transients
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_ms_saving_post_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_ms_saving_post_%'");