<?php
/*------------------------------------
 * Theme: Template by studio.bio 
 * File: Template dev functions file 
 * Author: Joshua Michaels
 * URI: https://studio.bio/template
 *------------------------------------
 */

// Debug marker
define('KANEISM_TEMPLATE_DEBUG', true);

// Initialize global variables for template tracking
global $kaneism_template_data;
$kaneism_template_data = array(
    'filename' => '',
    'path' => '',
    'source' => 'unknown',
    'type' => 'unknown',
    'context' => array()
);

/**
 * Track the template being included
 * Hooked into template_include with high priority
 */
function kaneism_track_template($template) {
    global $kaneism_template_data;
    
    // Store basic template info
    $kaneism_template_data['filename'] = basename($template);
    $kaneism_template_data['path'] = $template;
    
    // Determine template source (theme, plugin, etc)
    $wp_content_dir = WP_CONTENT_DIR;
    $theme_dir = get_template_directory();
    
    if (strpos($template, $theme_dir) !== false) {
        $kaneism_template_data['source'] = 'theme';
    } elseif (strpos($template, $wp_content_dir . '/plugins/') !== false) {
        // Extract plugin name from path
        $plugin_path = str_replace($wp_content_dir . '/plugins/', '', $template);
        $plugin_name = explode('/', $plugin_path)[0];
        $kaneism_template_data['source'] = 'plugin:' . $plugin_name;
    }
    
    // Gather context information
    $context = array();
    
    // Post type information
    if (is_singular()) {
        $post_type = get_post_type();
        $kaneism_template_data['type'] = 'singular:' . $post_type;
        $context[] = "Post Type: $post_type";
        $context[] = "Template: " . get_page_template_slug();
    } 
    // Taxonomy information
    elseif (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        $kaneism_template_data['type'] = 'taxonomy:' . $term->taxonomy;
        $context[] = "Taxonomy: " . $term->taxonomy;
        $context[] = "Term: " . $term->name . " (ID: " . $term->term_id . ")";
        
        if (isset($term->parent) && $term->parent > 0) {
            $context[] = "Is child term (parent ID: " . $term->parent . ")";
        }
    } 
    // Archive information
    elseif (is_archive()) {
        $context[] = "Is Archive: Yes";
        
        if (is_post_type_archive()) {
            $post_type = get_query_var('post_type');
            if (is_array($post_type)) $post_type = reset($post_type);
            $kaneism_template_data['type'] = 'archive:' . $post_type;
            $context[] = "Archive Type: Post Type ($post_type)";
        } elseif (is_date()) {
            $kaneism_template_data['type'] = 'archive:date';
            $context[] = "Archive Type: Date";
        } elseif (is_author()) {
            $kaneism_template_data['type'] = 'archive:author';
            $context[] = "Archive Type: Author";
        }
    }
    
    $kaneism_template_data['context'] = $context;
    
    if (defined('KANEISM_TEMPLATE_DEBUG') && KANEISM_TEMPLATE_DEBUG) {
        error_log('Template tracked: ' . $kaneism_template_data['filename']);
        error_log('Source: ' . $kaneism_template_data['source']);
        error_log('Type: ' . $kaneism_template_data['type']);
    }
    
    // Return the template path unchanged to allow normal template loading
    return $template;
}
add_filter('template_include', 'kaneism_track_template', 1000);

/**
 * Get the current template filename
 * 
 * @param bool $echo Whether to echo the result
 * @return string The template filename or formatted template info
 */
function get_current_template($echo = false) {
    global $kaneism_template_data;
    
    if (empty($kaneism_template_data['filename'])) {
        if (defined('KANEISM_TEMPLATE_DEBUG') && KANEISM_TEMPLATE_DEBUG) {
            error_log('get_current_template called but no template data available');
        }
        return false;
    }
    
    // Build template information string
    $template_info = $kaneism_template_data['filename'];
    
    // Add plugin information if applicable
    if (strpos($kaneism_template_data['source'], 'plugin:') === 0) {
        $plugin_name = str_replace('plugin:', '', $kaneism_template_data['source']);
        $template_info .= ' [Plugin: ' . $plugin_name . ']';
    }
    
    // Add post type information if applicable
    if (strpos($kaneism_template_data['type'], 'singular:') === 0) {
        $post_type = str_replace('singular:', '', $kaneism_template_data['type']);
        if ($post_type !== 'post' && $post_type !== 'page') {
            $template_info .= ' [CPT: ' . $post_type . ']';
        }
    } 
    // Add taxonomy information if applicable
    elseif (strpos($kaneism_template_data['type'], 'taxonomy:') === 0) {
        $taxonomy = str_replace('taxonomy:', '', $kaneism_template_data['type']);
        if ($taxonomy !== 'category' && $taxonomy !== 'post_tag') {
            $template_info .= ' [Tax: ' . $taxonomy . ']';
        }
    } 
    // Add archive information if applicable
    elseif (strpos($kaneism_template_data['type'], 'archive:') === 0) {
        $archive_type = str_replace('archive:', '', $kaneism_template_data['type']);
        if ($archive_type !== 'post') {
            $template_info .= ' [Archive: ' . $archive_type . ']';
        }
    }
    
    if (defined('KANEISM_TEMPLATE_DEBUG') && KANEISM_TEMPLATE_DEBUG) {
        error_log('get_current_template returning: ' . $template_info);
    }
    
    if ($echo) {
        echo $template_info;
    } else {
        return $template_info;
    }
}

/**
 * Get detailed context information about the current template
 * 
 * @param bool $echo Whether to echo the result
 * @return string|void Context information as a string, or void if echo is true
 */
function get_template_context($echo = false) {
    global $kaneism_template_data;
    
    if (empty($kaneism_template_data['context'])) {
        if (defined('KANEISM_TEMPLATE_DEBUG') && KANEISM_TEMPLATE_DEBUG) {
            error_log('get_template_context called but no context data available');
        }
        return '';
    }
    
    // Join context information with separator
    $context_info = implode(' | ', $kaneism_template_data['context']);
    
    // Add template path information
    if (!empty($kaneism_template_data['path'])) {
        $context_info .= ' | Template Path: ' . $kaneism_template_data['path'];
    }
    
    if (defined('KANEISM_TEMPLATE_DEBUG') && KANEISM_TEMPLATE_DEBUG) {
        error_log('get_template_context returning: ' . $context_info);
    }
    
    if ($echo) {
        echo $context_info;
    } else {
        return $context_info;
    }
}

// Legacy functions for backward compatibility
function var_template_include($template) {
    // No longer needed, but kept for backward compatibility
    // We're using kaneism_track_template instead
    return $template;
}

// Debug loaded confirmation
if (defined('KANEISM_TEMPLATE_DEBUG') && KANEISM_TEMPLATE_DEBUG) {
    error_log('Template functions loaded');
    add_action('wp_footer', function() {
        global $kaneism_template_data;
        error_log('Template data at footer: ' . print_r($kaneism_template_data, true));
    }, 999);
}
?>