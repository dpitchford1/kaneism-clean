<?php
/**
 * Custom Taxonomies Registration
 *
 * @package Work
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Register custom taxonomies for the Work post type
 */
function register_work_taxonomies() {
    // Work Category Taxonomy
    $labels = array(
        'name'                       => _x('Work Categories', 'Taxonomy General Name', 'work'),
        'singular_name'              => _x('Work Category', 'Taxonomy Singular Name', 'work'),
        'menu_name'                  => __('Work Categories', 'work'),
        'all_items'                  => __('All Categories', 'work'),
        'parent_item'                => __('Parent Category', 'work'),
        'parent_item_colon'          => __('Parent Category:', 'work'),
        'new_item_name'              => __('New Category Name', 'work'),
        'add_new_item'               => __('Add New Category', 'work'),
        'edit_item'                  => __('Edit Category', 'work'),
        'update_item'                => __('Update Category', 'work'),
        'view_item'                  => __('View Category', 'work'),
        'separate_items_with_commas' => __('Separate categories with commas', 'work'),
        'add_or_remove_items'        => __('Add or remove categories', 'work'),
        'choose_from_most_used'      => __('Choose from the most used', 'work'),
        'popular_items'              => __('Popular Categories', 'work'),
        'search_items'               => __('Search Categories', 'work'),
        'not_found'                  => __('Not Found', 'work'),
        'no_terms'                   => __('No categories', 'work'),
        'items_list'                 => __('Categories list', 'work'),
        'items_list_navigation'      => __('Categories list navigation', 'work'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
        'rewrite'                    => array(
            'slug'                   => 'work',
            'with_front'             => false,
            'hierarchical'           => true,
        ),
    );
    register_taxonomy('work_category', array('work'), $args);
    
    // Add default terms for Work Category
    if (!term_exists('Murals', 'work_category')) {
        wp_insert_term('Murals', 'work_category');
    }
    
    if (!term_exists('Canvases', 'work_category')) {
        wp_insert_term('Canvases', 'work_category');
    }
    
    if (!term_exists('Designs', 'work_category')) {
        wp_insert_term('Designs', 'work_category');
    }
}

/**
 * Unregister the old taxonomies if they exist
 */
function unregister_old_work_taxonomies() {
    global $wp_taxonomies;
    
    $taxonomies_to_unregister = array('murals', 'canavases', 'design');
    
    foreach ($taxonomies_to_unregister as $taxonomy) {
        if (taxonomy_exists($taxonomy)) {
            unset($wp_taxonomies[$taxonomy]);
        }
    }
}
add_action('init', 'unregister_old_work_taxonomies', 20);

/**
 * Add custom rewrite rules to fix taxonomy/post type conflict
 */
function work_add_custom_rewrite_rules() {
    // Add a custom rewrite rule for work categories - including subcategories
    add_rewrite_rule(
        'work/([^/]+)/?$',
        'index.php?work_category=$matches[1]',
        'top'
    );
    
    // Support hierarchical category paths up to 3 levels deep
    add_rewrite_rule(
        'work/([^/]+)/([^/]+)/?$',
        'index.php?work_category=$matches[2]',
        'top'
    );
    
    add_rewrite_rule(
        'work/([^/]+)/([^/]+)/([^/]+)/?$',
        'index.php?work_category=$matches[3]',
        'top'
    );
    
    // Rules for single work posts that include categories in URL (up to 3 levels)
    add_rewrite_rule(
        'work/([^/]+)/([^/]+)/([^/]+)/([^/]+)/?$',
        'index.php?work=$matches[4]',
        'top'
    );
    
    add_rewrite_rule(
        'work/([^/]+)/([^/]+)/([^/]+)/?$',
        'index.php?work=$matches[3]',
        'top'
    );
    
    add_rewrite_rule(
        'work/([^/]+)/([^/]+)/?$',
        'index.php?work=$matches[2]',
        'top'
    );
}
add_action('init', 'work_add_custom_rewrite_rules', 10); 