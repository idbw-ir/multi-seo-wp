<?php
if (!defined('ABSPATH')) {
    exit;
}

class MS_SEO_Compatibility {
    
    public function init() {
        if (defined('WPSEO_VERSION')) {
            $this->init_yoast_compatibility();
        }
        
        if (defined('RANK_MATH_VERSION')) {
            $this->init_rank_math_compatibility();
        }
    }
    
    public function get_active_plugin() {
        if (defined('WPSEO_VERSION')) {
            return 'yoast';
        }
        if (defined('RANK_MATH_VERSION')) {
            return 'rank_math';
        }
        return false;
    }
    
    public function get_field_map($plugin = null) {
        $plugin = $plugin ?: $this->get_active_plugin();
        
        if ($plugin === 'yoast') {
            return [
                'title' => '_yoast_wpseo_title',
                'desc' => '_yoast_wpseo_metadesc',
                'keyword' => '_yoast_wpseo_focuskw',
            ];
        }
        
        return [
            'title' => 'rank_math_title',
            'desc' => 'rank_math_description',
            'keyword' => 'rank_math_focus_keyword',
        ];
    }
    
    private function init_yoast_compatibility() {
        add_filter('wpseo_title', [$this, 'yoast_title_filter'], 20);
        add_filter('wpseo_metadesc', [$this, 'yoast_metadesc_filter'], 20);
    }
    
    public function yoast_title_filter($title) {
        if (is_admin() || !is_singular()) {
            return $title;
        }
        
        global $post;
        if (!$post) {
            return $title;
        }
        
        $custom_title = get_post_meta($post->ID, '_yoast_wpseo_title', true);
        return $custom_title ?: $title;
    }
    
    public function yoast_metadesc_filter($desc) {
        if (is_admin() || !is_singular()) {
            return $desc;
        }
        
        global $post;
        if (!$post) {
            return $desc;
        }
        
        $custom_desc = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
        return $custom_desc ?: $desc;
    }
    
    private function init_rank_math_compatibility() {
        add_filter('rank_math/frontend/title', [$this, 'rank_math_title_filter'], 999);
        add_filter('rank_math/frontend/description', [$this, 'rank_math_desc_filter'], 999);
        add_filter('rank_math/metabox/post_title', [$this, 'rank_math_metabox_title_filter'], 999);
        add_filter('rank_math/metabox/description', [$this, 'rank_math_metabox_desc_filter'], 999);
    }
    
    public function rank_math_title_filter($title) {
        if (is_admin()) return $title;
        
        if (is_singular()) {
            global $post;
            $rank_math_title = get_post_meta($post->ID, 'rank_math_title', true);
            if ($rank_math_title) return $rank_math_title;
        }
        
        return $title;
    }
    
    public function rank_math_desc_filter($desc) {
        if (is_admin()) return $desc;
        
        if (is_singular()) {
            global $post;
            $rank_math_desc = get_post_meta($post->ID, 'rank_math_description', true);
            if ($rank_math_desc) return $rank_math_desc;
        }
        
        return $desc;
    }
    
    public function rank_math_metabox_title_filter($title) {
        global $post;
        if (!$post) return $title;
        if (!empty($title)) return $title;
        
        $rank_math_title = get_post_meta($post->ID, 'rank_math_title', true);
        if ($rank_math_title) return $rank_math_title;
        
        return $title;
    }
    
    public function rank_math_metabox_desc_filter($desc) {
        global $post;
        if (!$post) return $desc;
        if (!empty($desc)) return $desc;
        
        $rank_math_desc = get_post_meta($post->ID, 'rank_math_description', true);
        if ($rank_math_desc) return $rank_math_desc;
        
        return $desc;
    }
}