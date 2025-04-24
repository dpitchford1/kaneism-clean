<?php
/**
 * Template Loader
 *
 * @package Work
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Remove any existing template_include filter from this file to avoid duplicates
// This ensures we don't accidentally add the filter twice if the file is included multiple times
remove_filter('template_include', 'work_template_loader', 1001);

/**
 * Load custom templates for the Work post type
 *
 * @param string $template The path of the template to include.
 * @return string The path of the template to include.
 */
function work_template_loader($template) {
    // Enable debugging to track template loading
    $debug = true; // Set to false in production
    $original_template = $template;
    
    // Initialize $term variable
    $term = null;
    
    if ($debug) {
        error_log('Work template loader called. Original template: ' . $template);
        error_log('is_tax: ' . (is_tax('work_category') ? 'true' : 'false'));
        error_log('is_singular: ' . (is_singular('work') ? 'true' : 'false'));
        error_log('is_archive: ' . (is_post_type_archive('work') ? 'true' : 'false'));
        
        if (is_tax('work_category')) {
            $term = get_queried_object();
            error_log('Work taxonomy term: ' . $term->name . ' (ID: ' . $term->term_id . ')');
            error_log('Is subcategory: ' . ($term->parent > 0 ? 'true' : 'false'));
            
            if ($term->parent > 0) {
                $parent = get_term($term->parent, 'work_category');
                if (!is_wp_error($parent)) {
                    error_log('Parent category: ' . $parent->name . ' (ID: ' . $parent->term_id . ')');
                }
            }
        }
    }
    
    // CRITICAL FIX: For work_category taxonomies, we need to load the correct template
    if (is_tax('work_category')) {
        // Get the term if not already fetched
        if (!$term) {
            $term = get_queried_object();
        }
        
        // Special handling for subcategories - this is where the issue happens
        if ($term->parent > 0) {
            // MOST DIRECT APPROACH: Always use the theme's taxonomy-work_category.php for subcategories
            // This is the most reliable solution
            $theme_template = get_template_directory() . '/taxonomy-work_category.php';
            
            if (file_exists($theme_template)) {
                if ($debug) {
                    error_log('FORCE USING theme template for subcategory: ' . $theme_template);
                }
                return $theme_template;
            }
            
            // If the theme template doesn't exist, try to locate it through standard means
            $located_template = locate_template(array(
                'taxonomy-work_category-' . $term->slug . '.php',
                'taxonomy-work_category.php',
                'taxonomy.php'
            ));
            
            if ($debug) {
                error_log('Located theme template for subcategory: ' . ($located_template ? $located_template : 'NONE'));
            }
            
            if ($located_template) {
                return $located_template;
            }
            
            // If no theme template at all, use the plugin's
            $plugin_template = WORK_PLUGIN_DIR . 'templates/taxonomy-work_category.php';
            if (file_exists($plugin_template)) {
                if ($debug) {
                    error_log('Using plugin template for subcategory: ' . $plugin_template);
                }
                return $plugin_template;
            }
        } else {
            // Regular category (not subcategory)
            // Standard template hierarchy applies
            $theme_template = locate_template(array(
                'taxonomy-work_category-' . $term->slug . '.php',
                'taxonomy-work_category.php',
                'taxonomy.php'
            ));
            
            if ($debug) {
                error_log('Located theme template for regular category: ' . ($theme_template ? $theme_template : 'NONE'));
            }
            
            if ($theme_template) {
                return $theme_template;
            }
            
            $plugin_template = WORK_PLUGIN_DIR . 'templates/taxonomy-work_category.php';
            if (file_exists($plugin_template)) {
                if ($debug) {
                    error_log('Using plugin template for regular category: ' . $plugin_template);
                }
                return $plugin_template;
            }
        }
    }

    // Handle single work posts
    if (is_singular('work')) {
        $theme_template = locate_template(array('single-work.php'));
        if ($theme_template) {
            if ($debug) error_log('Using theme template for single work: ' . $theme_template);
            return $theme_template;
        }
        
        $plugin_template = WORK_PLUGIN_DIR . 'templates/single-work.php';
        if (file_exists($plugin_template)) {
            if ($debug) error_log('Using plugin template for single work: ' . $plugin_template);
            return $plugin_template;
        }
    } 

    // Handle work archives
    if (is_post_type_archive('work')) {
        $theme_template = locate_template(array('archive-work.php'));
        if ($theme_template) {
            if ($debug) error_log('Using theme template for work archive: ' . $theme_template);
            return $theme_template;
        }
        
        $plugin_template = WORK_PLUGIN_DIR . 'templates/archive-work.php';
        if (file_exists($plugin_template)) {
            if ($debug) error_log('Using plugin template for work archive: ' . $plugin_template);
            return $plugin_template;
        }
    }
    
    // If no special templates were found, return the original
    if ($debug) error_log('No special template found, returning original: ' . $original_template);
    return $template;
}

// Use the ABSOLUTE HIGHEST priority to ensure this overrides any other template filters
// We're using PHP_INT_MAX-1 to ensure this runs after kane_var_template_include at 1000
// but before any other potential plugin/theme filters at PHP_INT_MAX
add_filter('template_include', 'work_template_loader', PHP_INT_MAX-1);

/**
 * Ultimate fallback - use template_redirect to include the theme template directly
 * This is the absolute last resort if nothing else works
 */
function work_force_subcategory_template() {
    // Only run on work category pages that are subcategories
    if (!is_tax('work_category')) {
        return;
    }
    
    // Get the term to check if it's a subcategory
    $term = get_queried_object();
    if (!$term || $term->parent == 0) {
        return;
    }
    
    // We're on a subcategory page - if debug is enabled, log it
    $debug = true;
    if ($debug) {
        error_log('Work subcategory template_redirect called for term: ' . $term->name);
    }
    
    // The absolute path to the theme's taxonomy template
    $theme_template = get_template_directory() . '/taxonomy-work_category.php';
    
    // Check if the file exists
    if (file_exists($theme_template)) {
        if ($debug) {
            error_log('Force-including theme template: ' . $theme_template);
        }
        
        // Include the template and exit to prevent further template loading
        include($theme_template);
        exit;
    }
}
// Add this hook at a very high priority to ensure it runs before any other output
add_action('template_redirect', 'work_force_subcategory_template', 1);

/**
 * Force the correct body classes for work category templates
 * This ensures proper display of global menus
 */
function work_fix_template_body_classes($classes) {
    if (is_tax('work_category')) {
        // Add specific classes needed by the theme for proper global menu display
        $classes[] = 'work-page';
        $classes[] = 'global-template'; // This might be needed for global menu display
        
        // Get the term to check if it's a subcategory
        $term = get_queried_object();
        if ($term && $term->parent != 0) {
            $classes[] = 'work-subcategory';
            
            // Get parent term info
            $parent = get_term($term->parent, 'work_category');
            if ($parent && !is_wp_error($parent)) {
                $classes[] = 'work-parent-' . $parent->slug;
            }
        }
    }
    return $classes;
}
add_filter('body_class', 'work_fix_template_body_classes', 99);

// Removed the work_force_global_header function and its action hook to comply with requirements 