<?php
if (!defined('ABSPATH')) {
    exit;
}

class Multi_SEO {
    
    private $admin;
    private $seo_compatibility;
    
    public function __construct() {
        $this->seo_compatibility = new MS_SEO_Compatibility();
        $this->admin = new MS_Admin();
    }
    
    public function init() {
        // بارگذاری کامپوننت‌ها
        $this->seo_compatibility->init();
        $this->admin->init();
        
        // Hook ذخیره پست
        add_action('save_post', [$this, 'sync_seo_defaults_for_post'], 20);
        
        // Hook بازسازی تنظیمات آرشیو
        add_action('init', [$this, 'sync_archive_seo_defaults']);
    }
    
    /**
     * ذخیره خودکار متا برای افزونه فعال SEO
     */
    public function sync_seo_defaults_for_post($post_id) {
        $plugin = $this->seo_compatibility->get_active_plugin();
        if (!$plugin) return;
        
        $post_type = get_post_type($post_id);
        if (!$post_type) return;
        
        $lang = function_exists('pll_get_post_language') ? pll_get_post_language($post_id) : 'fa';
        $options = get_option('ms_seo_defaults', []);
        $defaults = $options[$post_type][$lang] ?? [];
        
        if (empty($defaults)) return;
        
        $field_map = $this->seo_compatibility->get_field_map($plugin);
        
        foreach ($field_map as $field_name => $meta_key) {
            if (empty($defaults[$field_name])) continue;
            
            $default_value = trim($defaults[$field_name]);
            $resolved_value = ms_replace_placeholders($default_value, $post_id);
            
            $generated_marker = "_ms_seo_generated_{$plugin}_{$meta_key}";
            $is_system_generated = get_post_meta($post_id, $generated_marker, true);
            $current_value = get_post_meta($post_id, $meta_key, true);
            
            if (empty($current_value) || $is_system_generated) {
                update_post_meta($post_id, $meta_key, $resolved_value);
                update_post_meta($post_id, $generated_marker, '1');
            }
        }
    }
    
    /**
     * Sync آرشیوها هنگام بازسازی یا ذخیره
     */
    public function sync_archive_seo_defaults() {
        $post_types = ms_get_post_types();
        $langs = ms_get_languages();
        $options = get_option('ms_seo_defaults', []);
        $plugin = $this->seo_compatibility->get_active_plugin();
        
        if ($plugin === 'yoast') {
            $wpseo_titles = get_option('wpseo_titles', []);
            
            foreach ($post_types as $pt) {
                $pt_name = $pt->name;
                
                foreach ($langs as $lang) {
                    $archive_title = $options[$pt_name][$lang]['archive_title'] ?? '';
                    $archive_desc  = $options[$pt_name][$lang]['archive_desc'] ?? '';
                    
                    if ($archive_title) {
                        $resolved = ms_replace_placeholders($archive_title, null, $pt_name);
                        foreach (["title-{$pt_name}", "title-{$pt_name}-archive", "title-ptarchive-{$pt_name}"] as $key) {
                            $wpseo_titles[$key] = $resolved;
                        }
                    }
                    
                    if ($archive_desc) {
                        $resolved = ms_replace_placeholders($archive_desc, null, $pt_name);
                        foreach (["metadesc-{$pt_name}", "metadesc-{$pt_name}-archive", "metadesc-ptarchive-{$pt_name}"] as $key) {
                            $wpseo_titles[$key] = $resolved;
                        }
                    }
                }
            }
            
            update_option('wpseo_titles', $wpseo_titles);
            return;
        }
        
        if ($plugin === 'rank_math') {
            foreach ($post_types as $pt) {
                $pt_name = $pt->name;
                
                foreach ($langs as $lang) {
                    $archive_title = $options[$pt_name][$lang]['archive_title'] ?? '';
                    $archive_desc  = $options[$pt_name][$lang]['archive_desc'] ?? '';
                    
                    if ($archive_title) {
                        update_option(
                            "rank_math_archive_title_{$pt_name}_{$lang}", 
                            ms_replace_placeholders($archive_title, null, $pt_name)
                        );
                    }
                    
                    if ($archive_desc) {
                        update_option(
                            "rank_math_archive_desc_{$pt_name}_{$lang}", 
                            ms_replace_placeholders($archive_desc, null, $pt_name)
                        );
                    }
                }
            }
        }
    }
}