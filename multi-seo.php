<?php
/**
 * Plugin Name: Multi Language SEO System
 * Plugin URI: https://example.com/multi-seo
 * Description: سیستم SEO چندزبانه با پشتیبانی از Polylang و افزونه‌های Yoast/Rank Math
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: multi-seo
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

if (!defined('ABSPATH')) {
    exit;
}

// تعریف ثابت‌های افزونه
define('MS_VERSION', '1.0.0');
define('MS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MS_PLUGIN_BASENAME', plugin_basename(__FILE__));

// بارگذاری فایل‌های مورد نیاز
require_once MS_PLUGIN_DIR . 'includes/functions.php';
require_once MS_PLUGIN_DIR . 'includes/class-seo-compatibility.php';
require_once MS_PLUGIN_DIR . 'includes/class-admin.php';
require_once MS_PLUGIN_DIR . 'includes/class-multi-seo.php';

// راه‌اندازی افزونه
function ms_init() {
    // بارگذاری متن‌های ترجمه
    load_plugin_textdomain('multi-seo', false, dirname(MS_PLUGIN_BASENAME) . '/languages');
    
    // ایجاد نمونه اصلی افزونه
    $multi_seo = new Multi_SEO();
    $multi_seo->init();
}
add_action('plugins_loaded', 'ms_init');

// Hook فعال‌سازی افزونه
register_activation_hook(__FILE__, 'ms_activate');
function ms_activate() {
    // تنظیمات پیش‌فرض در صورت نیاز
    if (false === get_option('ms_seo_defaults')) {
        add_option('ms_seo_defaults', []);
    }
}

// Hook غیرفعال‌سازی افزونه
register_deactivation_hook(__FILE__, 'ms_deactivate');
function ms_deactivate() {
    // پاکسازی transients
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_ms_saving_post_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_ms_saving_post_%'");
}