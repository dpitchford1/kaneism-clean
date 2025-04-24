<?php
/**
 * Post Type Registration
 *
 * @package Work
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Register the Work custom post type
 */
function create_work_cpt() {
	$labels = array(
		'name' => _x('Work', 'Post Type General Name', 'work'),
		'singular_name' => _x('Work', 'Post Type Singular Name', 'work'),
		'menu_name' => _x('Work', 'Admin Menu text', 'work'),
		'name_admin_bar' => _x('Work', 'Add New on Toolbar', 'work'),
		'archives' => __('Work Archives', 'work'),
		'attributes' => __('Work Attributes', 'work'),
		'parent_item_colon' => __('Parent Work:', 'work'),
		'all_items' => __('All Jobs', 'work'),
		'add_new_item' => __('Add New Job', 'work'),
		'add_new' => __('Add New', 'work'),
		'new_item' => __('New Job', 'work'),
		'edit_item' => __('Edit Job', 'work'),
		'update_item' => __('Update Job', 'work'), 
		'view_item' => __('View Job', 'work'),
		'view_items' => __('View Jobs', 'work'),
		'search_items' => __('Search Work', 'work'),
		'not_found' => __('Not found', 'work'),
		'not_found_in_trash' => __('Not found in Trash', 'work'),
		'featured_image' => __('Featured Image', 'work'),
		'set_featured_image' => __('Set featured image', 'work'),
		'remove_featured_image' => __('Remove featured image', 'work'),
		'use_featured_image' => __('Use as featured image', 'work'),
		'insert_into_item' => __('Insert into Job', 'work'),
		'uploaded_to_this_item' => __('Uploaded to this Job', 'work'),
		'items_list' => __('Jobs list', 'work'),
		'items_list_navigation' => __('Jobs list navigation', 'work'),
		'filter_items_list' => __('Filter Jobs list', 'work'),
	);
    $rewrite = array(
		'slug'                  => 'work',
		'with_front'            => false,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label' => __('Work', 'work'),
		'description' => __('Portfolio Works', 'work'),
		'labels' => $labels,
		'menu_icon' => 'dashicons-dashboard',
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'post-formats'),
		'taxonomies' => array('work_category', 'category'),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 6,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => true,
		'hierarchical' => true,
		'exclude_from_search' => false,
		'show_in_rest' => true,
		'publicly_queryable' => true,
		'capability_type' => 'page',
        'rewrite' => $rewrite,
	);
	register_post_type('work', $args);
}

/**
 * Add rewrite rules for work items to include categories in URL
 */
function work_add_post_type_rewrite_rules() {
    add_rewrite_rule(
        'work/([^/]+)/([^/]+)/?$',
        'index.php?work=$matches[2]',
        'top'
    );
    
    add_rewrite_tag('%work_category%', '([^&]+)');
}
add_action('init', 'work_add_post_type_rewrite_rules', 10);

/**
 * Modify the permalinks for work posts to include the category
 */
function work_modify_post_link($permalink, $post, $leavename) {
    if ($post->post_type != 'work' || empty($permalink)) {
        return $permalink;
    }
    
    // If post is a draft, don't modify permalink
    if ($post->post_status == 'draft' || $post->post_status == 'auto-draft' || $post->post_status == 'pending') {
        return $permalink;
    }
    
    // Get the terms
    $terms = wp_get_post_terms($post->ID, 'work_category');
    
    // Only proceed if we have at least one category
    if (empty($terms) || is_wp_error($terms)) {
        return $permalink;
    }
    
    // Use the first category (prioritize primary if available)
    $primary_category = '';
    foreach ($terms as $term) {
        $primary_category = $term->slug;
        break;
    }
    
    if (empty($primary_category)) {
        return $permalink;
    }
    
    // Build new permalink with category
    $permalink = str_replace('work/', 'work/' . $primary_category . '/', $permalink);
    
    return $permalink;
}
add_filter('post_type_link', 'work_modify_post_link', 10, 3);
