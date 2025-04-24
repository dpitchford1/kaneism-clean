<?php
/**
 * Uninstall file for Work plugin
 *
 * @package Work
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options if any
// delete_option('work_plugin_options');

// Uncomment the following lines if you want to remove all work posts when uninstalling
// This is generally not recommended as it will delete all user content
/*
// Get all work posts
$work_posts = get_posts(
    array(
        'post_type'      => 'work',
        'posts_per_page' => -1,
        'post_status'    => 'any',
    )
);

// Delete all work posts
foreach ($work_posts as $post) {
    wp_delete_post($post->ID, true);
}

// Delete custom taxonomies terms
$taxonomies = array('murals', 'canavases', 'design');
foreach ($taxonomies as $taxonomy) {
    $terms = get_terms(
        array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        )
    );
    
    foreach ($terms as $term) {
        wp_delete_term($term->term_id, $taxonomy);
    }
}
*/ 