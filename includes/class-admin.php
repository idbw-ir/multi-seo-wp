<?php
if (!defined('ABSPATH')) {
    exit;
}

class MS_Admin {
    
    public function init() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    public function add_admin_menu() {
        add_options_page(
            __('SEO چندزبانه', 'multi-seo'),
            __('SEO چندزبانه', 'multi-seo'),
            'manage_options',
            'multi-seo',
            [$this, 'render_page']
        );
    }
    
    public function register_settings() {
        register_setting('ms_seo_group', 'ms_seo_defaults', [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);
    }
    
    public function sanitize_settings($input) {
        $sanitized = [];
        
        if (is_array($input)) {
            foreach ($input as $post_type => $languages) {
                if (is_array($languages)) {
                    foreach ($languages as $lang => $fields) {
                        $sanitized[$post_type][$lang] = [
                            'title' => sanitize_text_field($fields['title'] ?? ''),
                            'keyword' => sanitize_text_field($fields['keyword'] ?? ''),
                            'desc' => sanitize_textarea_field($fields['desc'] ?? ''),
                            'archive_title' => sanitize_text_field($fields['archive_title'] ?? ''),
                            'archive_desc' => sanitize_textarea_field($fields['archive_desc'] ?? ''),
                        ];
                    }
                }
            }
        }
        
        return $sanitized;
    }
    
    public function render_page() {
        $post_types = ms_get_post_types();
        $langs = ms_get_languages();
        $options = get_option('ms_seo_defaults', []);
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('تنظیمات SEO چندزبانه', 'multi-seo'); ?></h1>
            
            <form method="post" action="options.php">
                <?php settings_fields('ms_seo_group'); ?>
                
                <?php foreach ($post_types as $pt): ?>
                    <div style="background:#fff;padding:15px;margin-bottom:20px;border:1px solid #ddd;">
                        <h2><?php echo esc_html($pt->labels->name); ?> (<?php echo esc_html($pt->name); ?>)</h2>
                        
                        <?php foreach ($langs as $lang): ?>
                            <div style="margin-bottom:15px;padding:10px;background:#f9f9f9;">
                                <strong><?php echo esc_html__('زبان:', 'multi-seo'); ?> <?php echo esc_html(strtoupper($lang)); ?></strong>
                                
                                <p>
                                    <label><?php echo esc_html__('عنوان SEO:', 'multi-seo'); ?></label>
                                    <input type="text"
                                           name="ms_seo_defaults[<?php echo esc_attr($pt->name); ?>][<?php echo esc_attr($lang); ?>][title]"
                                           value="<?php echo esc_attr($options[$pt->name][$lang]['title'] ?? ''); ?>"
                                           placeholder="%title% | %sitename%"
                                           style="width:100%">
                                </p>
                                
                                <p>
                                    <label><?php echo esc_html__('کلمه کلیدی فوکوس:', 'multi-seo'); ?></label>
                                    <input type="text"
                                           name="ms_seo_defaults[<?php echo esc_attr($pt->name); ?>][<?php echo esc_attr($lang); ?>][keyword]"
                                           value="<?php echo esc_attr($options[$pt->name][$lang]['keyword'] ?? ''); ?>"
                                           placeholder="<?php echo esc_attr__('کلمه کلیدی اصلی', 'multi-seo'); ?>"
                                           style="width:100%">
                                </p>
                                
                                <p>
                                    <label><?php echo esc_html__('توضیحات متا:', 'multi-seo'); ?></label>
                                    <textarea name="ms_seo_defaults[<?php echo esc_attr($pt->name); ?>][<?php echo esc_attr($lang); ?>][desc]"
                                              placeholder="<?php echo esc_attr__('توضیحات متا', 'multi-seo'); ?>"
                                              style="width:100%;height:70px"><?php echo esc_textarea($options[$pt->name][$lang]['desc'] ?? ''); ?></textarea>
                                </p>
                                
                                <p><strong><?php echo esc_html__('آرشیو', 'multi-seo'); ?></strong></p>
                                
                                <input type="text"
                                       name="ms_seo_defaults[<?php echo esc_attr($pt->name); ?>][<?php echo esc_attr($lang); ?>][archive_title]"
                                       value="<?php echo esc_attr($options[$pt->name][$lang]['archive_title'] ?? ''); ?>"
                                       placeholder="<?php echo esc_attr__('عنوان آرشیو', 'multi-seo'); ?>"
                                       style="width:100%;margin-bottom:5px">
                                
                                <textarea name="ms_seo_defaults[<?php echo esc_attr($pt->name); ?>][<?php echo esc_attr($lang); ?>][archive_desc]"
                                          placeholder="<?php echo esc_attr__('توضیحات آرشیو', 'multi-seo'); ?>"
                                          style="width:100%;height:70px"><?php echo esc_textarea($options[$pt->name][$lang]['archive_desc'] ?? ''); ?></textarea>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}