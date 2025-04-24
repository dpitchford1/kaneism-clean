<?php
/**
 * Kaneism Title Functions
 *
 * Enhances the WordPress title tag with category hierarchy for improved SEO
 *
 * @package kaneism
 */

// Register our hooks very early in the execution
add_action('init', 'kaneism_register_title_hooks', 5);

/**
 * Register all title-related hooks early in the WordPress execution
 */
function kaneism_register_title_hooks() {
    // Add the pre_get_document_title filter which runs last in title generation
    add_filter('pre_get_document_title', 'kaneism_override_title', 1);
    
    // Also add wp_title filter for older WordPress versions
    add_filter('wp_title', 'kaneism_filter_wp_title', 1, 2);

    // Add debug function
    //add_action('wp_head', 'kaneism_title_debug', 1);
    
    // Add a debug comment to show the hooks are registered
    // add_action('wp_head', function() {
    //     echo "<!-- Title hooks registered during init -->\n";
    // }, 1);
}

/**
 * Output debug information about the title generation
 */
function kaneism_title_debug() {
    echo "<!-- Title debug: Kaneism title functions are active -->\n";
    
    // Check if we're on a page where title modification applies
    if (is_singular('work') || is_tax('work_category') || is_post_type_archive('work')) {
        echo "<!-- On a Work page: ";
        if (is_singular('work')) echo "single work";
        elseif (is_tax('work_category')) echo "work category";
        elseif (is_post_type_archive('work')) echo "work archive";
        echo " -->\n";
        
        // Output the hierarchy that would be used
        $hierarchy = kaneism_get_work_hierarchy();
        echo "<!-- Work hierarchy: " . esc_html($hierarchy) . " -->\n";
    } 
    elseif (function_exists('is_woocommerce') && (is_woocommerce() || is_shop() || is_product_category() || is_product_tag())) {
        echo "<!-- On a WooCommerce page: ";
        if (is_shop()) echo "shop";
        elseif (is_product()) echo "single product";
        elseif (is_product_category()) echo "product category";
        elseif (is_product_tag()) echo "product tag";
        echo " -->\n";
        
        // Output the hierarchy that would be used
        $hierarchy = kaneism_get_woocommerce_hierarchy();
        echo "<!-- WooCommerce hierarchy: " . esc_html($hierarchy) . " -->\n";
    } else {
        echo "<!-- Not on a page that needs title modification -->\n";
    }
}

/**
 * Complete override for document title
 * Handles both Work and WooCommerce pages
 */
function kaneism_override_title($title) {
    // Handle Work pages
    if (is_singular('work') || is_tax('work_category') || is_post_type_archive('work')) {
        return kaneism_override_work_title($title);
    }
    
    // Handle WooCommerce pages
    if (function_exists('is_woocommerce') && (is_woocommerce() || is_shop() || is_product_category() || is_product_tag())) {
        return kaneism_override_woocommerce_title($title);
    }
    
    // For all other pages, return the original title
    return $title;
}

/**
 * Complete override for document title on Work pages
 */
function kaneism_override_work_title($title) {
    // Get our hierarchy
    $hierarchy = kaneism_get_work_hierarchy();
    
    // Get site name for the title
    $site_name = get_bloginfo('name');
    
    // Create an appropriate title based on page type
    if (is_singular('work')) {
        $post_title = get_the_title();
        $new_title = "$post_title | $hierarchy | $site_name";
    } 
    elseif (is_tax('work_category')) {
        $term = get_queried_object();
        $new_title = "$term->name | $hierarchy | $site_name";
    }
    elseif (is_post_type_archive('work')) {
        $new_title = "Work | $site_name";
    }
    
    // Add debug comment
    if (!empty($new_title)) {
        //echo "<!-- Work title override: " . esc_html($new_title) . " -->\n";
        return $new_title;
    }
    
    return $title;
}

/**
 * Complete override for document title on WooCommerce pages
 */
function kaneism_override_woocommerce_title($title) {
    // Get our hierarchy
    $hierarchy = kaneism_get_woocommerce_hierarchy();
    
    // Get site name for the title
    $site_name = get_bloginfo('name');
    
    // Create an appropriate title based on page type
    if (is_product()) {
        $post_title = get_the_title();
        $new_title = "$post_title | $hierarchy | $site_name";
    } 
    elseif (is_product_category()) {
        $term = get_queried_object();
        $new_title = "$term->name | $hierarchy | $site_name";
    }
    elseif (is_product_tag()) {
        $term = get_queried_object();
        $new_title = "$term->name | $hierarchy | $site_name";
    }
    elseif (is_shop()) {
        $new_title = "Shop | $site_name";
    }
    
    // Add debug comment
    if (!empty($new_title)) {
        echo "<!-- WooCommerce title override: " . esc_html($new_title) . " -->\n";
        return $new_title;
    }
    
    return $title;
}

/**
 * Filter wp_title for older WordPress versions
 */
function kaneism_filter_wp_title($title, $sep) {
    // Handle Work pages
    if (is_singular('work') || is_tax('work_category') || is_post_type_archive('work')) {
        return kaneism_append_work_hierarchy($title, $sep);
    }
    
    // Handle WooCommerce pages
    if (function_exists('is_woocommerce') && (is_woocommerce() || is_shop() || is_product_category() || is_product_tag())) {
        return kaneism_append_woocommerce_hierarchy($title, $sep);
    }
    
    // For all other pages, return the original title
    return $title;
}

/**
 * Append work hierarchy to wp_title
 */
function kaneism_append_work_hierarchy($title, $sep) {
    // Get our hierarchy
    $hierarchy = kaneism_get_work_hierarchy();
    
    // Only append hierarchy if we have it
    if (!empty($hierarchy)) {
        // Add separator if needed
        if (strpos($title, $sep) === false) {
            $title .= " $sep ";
        }
        $title .= " $hierarchy";
        
        // Debug output
        echo "<!-- wp_title modified for Work: " . esc_html($title) . " -->\n";
    }
    
    return $title;
}

/**
 * Append WooCommerce hierarchy to wp_title
 */
function kaneism_append_woocommerce_hierarchy($title, $sep) {
    // Get our hierarchy
    $hierarchy = kaneism_get_woocommerce_hierarchy();
    
    // Only append hierarchy if we have it
    if (!empty($hierarchy)) {
        // Add separator if needed
        if (strpos($title, $sep) === false) {
            $title .= " $sep ";
        }
        $title .= " $hierarchy";
        
        // Debug output
        echo "<!-- wp_title modified for WooCommerce: " . esc_html($title) . " -->\n";
    }
    
    return $title;
}

/**
 * Get work hierarchy string by mimicking the breadcrumb logic
 */
function kaneism_get_work_hierarchy() {
    // Start with empty hierarchy
    $hierarchy_parts = array();
    
    // Work is always the top-level
    $hierarchy_parts[] = 'Work';
    
    if (is_singular('work')) {
        // Get categories for this post - directly mimicking work_breadcrumb() logic
        $terms = get_the_terms(get_the_ID(), 'work_category');
        
        if ($terms && !is_wp_error($terms)) {
            // Get the first category
            $term = reset($terms);
            
            // Get ancestors for this category
            $ancestors = get_ancestors($term->term_id, 'work_category', 'taxonomy');
            
            // Display ancestors in order from highest to lowest (reversed array)
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_term($ancestor_id, 'work_category');
                if ($ancestor && !is_wp_error($ancestor)) {
                    $hierarchy_parts[] = $ancestor->name;
                }
            }
            
            // Add the direct parent category
            $hierarchy_parts[] = $term->name;
        }
    } 
    elseif (is_tax('work_category')) {
        // Get the term and its ancestors - directly mimicking work_breadcrumb() logic
        $term = get_queried_object();
        $ancestors = get_ancestors($term->term_id, 'work_category', 'taxonomy');
        
        // Display ancestors in order from highest to lowest (reversed array)
        $ancestors = array_reverse($ancestors);
        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_term($ancestor_id, 'work_category');
            if ($ancestor && !is_wp_error($ancestor)) {
                $hierarchy_parts[] = $ancestor->name;
            }
        }
        
        // Add the current category
        $hierarchy_parts[] = $term->name;
    }
    
    // Join with separator and return
    return implode(' › ', $hierarchy_parts);
}

/**
 * Get WooCommerce hierarchy string
 */
function kaneism_get_woocommerce_hierarchy() {
    // Start with empty hierarchy
    $hierarchy_parts = array();
    
    // Shop is always the top-level for WooCommerce
    $hierarchy_parts[] = 'Shop';
    
    if (is_product()) {
        // Get product categories
        $terms = get_the_terms(get_the_ID(), 'product_cat');
        
        if ($terms && !is_wp_error($terms)) {
            // Get the first category
            $term = reset($terms);
            
            // Get ancestors for this category
            $ancestors = get_ancestors($term->term_id, 'product_cat', 'taxonomy');
            
            // Display ancestors in order from highest to lowest (reversed array)
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_term($ancestor_id, 'product_cat');
                if ($ancestor && !is_wp_error($ancestor)) {
                    $hierarchy_parts[] = $ancestor->name;
                }
            }
            
            // Add the direct parent category
            $hierarchy_parts[] = $term->name;
        }
    } 
    elseif (is_product_category()) {
        // Get the term and its ancestors
        $term = get_queried_object();
        $ancestors = get_ancestors($term->term_id, 'product_cat', 'taxonomy');
        
        // Display ancestors in order from highest to lowest (reversed array)
        $ancestors = array_reverse($ancestors);
        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_term($ancestor_id, 'product_cat');
            if ($ancestor && !is_wp_error($ancestor)) {
                $hierarchy_parts[] = $ancestor->name;
            }
        }
        
        // Add the current category
        $hierarchy_parts[] = $term->name;
    }
    elseif (is_product_tag()) {
        // For tags, we just add the tag name (no ancestry)
        $term = get_queried_object();
        $hierarchy_parts[] = $term->name;
    }
    
    // Join with separator and return
    return implode(' › ', $hierarchy_parts);
}
