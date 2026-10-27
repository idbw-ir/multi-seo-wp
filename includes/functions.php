<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * گرفتن لیست Post Types
 */
function ms_get_post_types() {
    return get_post_types(['public' => true], 'objects');
}

/**
 * گرفتن زبان‌ها از Polylang
 */
function ms_get_languages() {
    if (function_exists('pll_languages_list')) {
        return pll_languages_list();
    }
    return ['fa'];
}

/**
 * جایگزینی placeholders
 */
function ms_replace_placeholders($template, $post_id = null, $post_type = null) {
    if ($post_id) {
        $template = str_replace('%title%', get_the_title($post_id), $template);
    }
    
    if ($post_type) {
        $pt_obj = get_post_type_object($post_type);
        $template = str_replace('%posttype%', $pt_obj->labels->name ?? $post_type, $template);
    }
    
    $template = str_replace('%sitename%', get_bloginfo('name'), $template);
    
    if ($post_id) {
        preg_match_all('/%customfield\((.*?)\)%/', $template, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $i => $meta_key) {
                $value = get_post_meta($post_id, $meta_key, true);
                $template = str_replace($matches[0][$i], $value ?: '', $template);
            }
        }
    }
    
    return $template;
}