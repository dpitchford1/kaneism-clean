<?php
/**
 * Kaneism Meta Description Functions
 *
 * Enhances the WordPress site with meta descriptions for SEO
 *
 * @package kaneism
 */

/**
 * Register hooks for meta description functions
 */
function kaneism_meta_description_setup() {
    // If Yoast SEO or Rank Math is active, let them handle descriptions
    if (class_exists('WPSEO_Frontend') || class_exists('RankMath')) {
        return;
    }
    
    // Add our meta descriptions at priority 1 so they appear early in head
    add_action('wp_head', 'kaneism_add_meta_descriptions', 1);
}
add_action('init', 'kaneism_meta_description_setup');

/**
 * Add meta description and social meta tags to the header
 */
function kaneism_add_meta_descriptions() {
    try {
        // Default description
        $description = get_bloginfo('description');
        
        // Get more specific description based on current page
        if (is_singular()) {
            global $post;
            
            // Try to get from excerpt first
            if (has_excerpt($post->ID)) {
                $description = strip_tags(get_the_excerpt());
            } 
            // Or get from content
            else {
                $excerpt = strip_tags($post->post_content);
                $excerpt = strip_shortcodes($excerpt);
                $excerpt = wp_trim_words($excerpt, 30, '...');
                
                if (!empty($excerpt)) {
                    $description = $excerpt;
                }
            }
        } elseif (is_category() || is_tag() || is_tax()) {
            $term = get_queried_object();
            if (!empty($term->description)) {
                $description = strip_tags($term->description);
            } else {
                $description = sprintf(__('Browse all %s content', 'kaneism'), single_term_title('', false));
            }
        } elseif (is_post_type_archive()) {
            $post_type = get_query_var('post_type');
            if (is_array($post_type)) {
                $post_type = reset($post_type);
            }
            $post_type_obj = get_post_type_object($post_type);
            if ($post_type_obj) {
                $description = $post_type_obj->description;
            }
        } elseif (is_author()) {
            $author = get_queried_object();
            if ($author) {
                $description = sprintf(__('Posts by %s', 'kaneism'), $author->display_name);
            }
        } elseif (is_search()) {
            $description = sprintf(__('Search results for "%s"', 'kaneism'), get_search_query());
        } elseif (is_archive()) {
            if (is_date()) {
                if (is_day()) {
                    $description = sprintf(__('Archive for %s', 'kaneism'), get_the_date());
                } elseif (is_month()) {
                    $description = sprintf(__('Archive for %s', 'kaneism'), get_the_date('F Y'));
                } elseif (is_year()) {
                    $description = sprintf(__('Archive for %s', 'kaneism'), get_the_date('Y'));
                }
            }
        }
        
        // Ensure description isn't too long
        $description = wp_trim_words($description, 30, '...');
        
        // Add meta description
        echo '<meta name="description" content="' . esc_attr($description) . '">' . PHP_EOL;
        
        // Open Graph description
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . PHP_EOL;
        
        // Twitter description
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . PHP_EOL;
    } catch (Exception $e) {
        // Log the error for administrators
        if (function_exists('error_log')) {
            error_log('Kaneism Theme - Error generating meta descriptions: ' . $e->getMessage());
        }
        
        // Fallback to a basic description
        $fallback = esc_attr(get_bloginfo('description'));
        echo '<meta name="description" content="' . $fallback . '">' . PHP_EOL;
        echo '<meta property="og:description" content="' . $fallback . '">' . PHP_EOL;
        echo '<meta name="twitter:description" content="' . $fallback . '">' . PHP_EOL;
    }
}

/**
 * Get meta description for a specific post/page/term
 * 
 * @param int|WP_Post|WP_Term|null $object Post, term or ID to get description for
 * @param int $word_count Maximum number of words (default 30)
 * @return string The meta description
 */
function kaneism_get_meta_description($object = null, $word_count = 30) {
    // Default description
    $description = get_bloginfo('description');
    
    if ($object instanceof WP_Post || (is_numeric($object) && get_post($object))) {
        // Handle post object
        $post = $object instanceof WP_Post ? $object : get_post($object);
        
        // Try to get from excerpt first
        if (has_excerpt($post->ID)) {
            $description = strip_tags(get_the_excerpt($post->ID));
        } 
        // Or get from content
        else {
            $excerpt = strip_tags($post->post_content);
            $excerpt = strip_shortcodes($excerpt);
            $excerpt = wp_trim_words($excerpt, $word_count, '...');
            
            if (!empty($excerpt)) {
                $description = $excerpt;
            }
        }
    } elseif ($object instanceof WP_Term) {
        // Handle term object
        if (!empty($object->description)) {
            $description = strip_tags($object->description);
        } else {
            $description = sprintf(__('Browse all %s content', 'kaneism'), $object->name);
        }
    }
    
    // Ensure description isn't too long
    return wp_trim_words($description, $word_count, '...');
} 