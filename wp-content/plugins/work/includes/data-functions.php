<?php
/**
 * Data Access Functions
 *
 * Public API functions for accessing Work plugin data.
 * These functions provide a standardized way for themes to access
 * plugin data without directly accessing post meta.
 *
 * @package Work
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Check if a work item is featured
 *
 * @param int $post_id Post ID (optional, defaults to current post).
 * @return bool True if featured, false otherwise.
 */
function work_is_featured($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return (bool) get_post_meta($post_id, '_work_is_featured', true);
}

/**
 * Get gallery images for a work item
 *
 * @param int $post_id Post ID (optional, defaults to current post).
 * @return array Array of attachment IDs, or empty array if none.
 */
function work_get_gallery_images($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $gallery_images = get_post_meta($post_id, '_work_gallery_images', true);
    
    if (!is_array($gallery_images)) {
        return array();
    }
    
    return $gallery_images;
}

/**
 * Get the number of gallery images for a work item
 *
 * @param int $post_id Post ID (optional, defaults to current post).
 * @return int Number of gallery images.
 */
function work_get_gallery_image_count($post_id = null) {
    $gallery_images = work_get_gallery_images($post_id);
    return count($gallery_images);
}

/**
 * Get project details for a work item
 *
 * @param int $post_id Post ID (optional, defaults to current post).
 * @return array Project details.
 */
function work_get_project_details_data($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $project_details = get_post_meta($post_id, '_work_project_details', true);
    
    if (!is_array($project_details)) {
        return array();
    }
    
    return $project_details;
}

/**
 * Get a specific project detail
 *
 * @param string $key     Detail key to retrieve.
 * @param int    $post_id Post ID (optional, defaults to current post).
 * @return string|null Detail value or null if not found.
 */
function work_get_project_detail($key, $post_id = null) {
    $details = work_get_project_details_data($post_id);
    
    if (isset($details[$key])) {
        return $details[$key];
    }
    
    return null;
}

/**
 * Get schema data for a work item
 *
 * @param int $post_id Post ID (optional, defaults to current post).
 * @return array Schema data.
 */
function work_get_schema_data($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $schema_data = get_post_meta($post_id, '_work_schema_data', true);
    
    if (!is_array($schema_data)) {
        return array();
    }
    
    return $schema_data;
}

/**
 * Get featured works
 *
 * @param int   $count Number of featured works to retrieve.
 * @param array $args  Additional WP_Query arguments.
 * @return WP_Query Query object with featured works.
 */
function work_get_featured_works($count = 3, $args = array()) {
    $default_args = array(
        'post_type'      => 'work',
        'posts_per_page' => $count,
        'meta_key'       => '_work_is_featured',
        'meta_value'     => '1',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    
    $query_args = wp_parse_args($args, $default_args);
    
    // Allow filtering of query args
    $query_args = apply_filters('work_featured_works_query_args', $query_args, $count);
    
    return new WP_Query($query_args);
}

/**
 * Get work items by category
 *
 * @param mixed $category Category ID, slug, or object.
 * @param int   $count    Number of items to retrieve.
 * @param array $args     Additional WP_Query arguments.
 * @return WP_Query Query object with category works.
 */
function work_get_category_works($category, $count = -1, $args = array()) {
    $default_args = array(
        'post_type'      => 'work',
        'posts_per_page' => $count,
        'tax_query'      => array(
            array(
                'taxonomy' => 'work_category',
                'field'    => is_numeric($category) ? 'term_id' : 'slug',
                'terms'    => $category,
            ),
        ),
    );
    
    $query_args = wp_parse_args($args, $default_args);
    
    // Allow filtering of query args
    $query_args = apply_filters('work_category_works_query_args', $query_args, $category, $count);
    
    return new WP_Query($query_args);
}

/**
 * Get work category terms for a post
 *
 * @param int $post_id Post ID (optional, defaults to current post).
 * @return array|WP_Error Array of WP_Term objects or WP_Error.
 */
function work_get_categories($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_the_terms($post_id, 'work_category');
}

/**
 * Get work categories as associative array
 *
 * @param array $args Arguments to pass to get_terms().
 * @return array Associative array of term_id => name.
 */
function work_get_category_options($args = array()) {
    $default_args = array(
        'taxonomy'   => 'work_category',
        'hide_empty' => false,
    );
    
    $args = wp_parse_args($args, $default_args);
    $terms = get_terms($args);
    
    $options = array();
    
    if (!is_wp_error($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            $options[$term->term_id] = $term->name;
        }
    }
    
    return $options;
}

/**
 * Get the primary category for a work item
 *
 * @param int $post_id Post ID (optional, defaults to current post).
 * @return WP_Term|false First category term or false if none.
 */
function work_get_primary_category($post_id = null) {
    $categories = work_get_categories($post_id);
    
    if (is_wp_error($categories) || empty($categories)) {
        return false;
    }
    
    // Return the first category (could be enhanced to use a meta field for primary category)
    return $categories[0];
}

/**
 * Register meta fields for REST API access
 */
function work_register_meta_fields() {
    register_meta('post', '_work_is_featured', array(
        'type'          => 'boolean',
        'description'   => 'Whether the work is featured',
        'single'        => true,
        'show_in_rest'  => true,
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
    
    register_meta('post', '_work_gallery_images', array(
        'type'          => 'array',
        'description'   => 'Gallery images for the work',
        'single'        => true,
        'show_in_rest'  => array(
            'schema' => array(
                'items' => array(
                    'type' => 'integer'
                )
            )
        ),
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
    
    register_meta('post', '_work_project_details', array(
        'type'          => 'object',
        'description'   => 'Project details for the work',
        'single'        => true,
        'show_in_rest'  => array(
            'schema' => array(
                'type' => 'object',
                'properties' => array(
                    'type'      => array('type' => 'string'),
                    'location'  => array('type' => 'string'),
                    'materials' => array('type' => 'string'),
                    'size'      => array('type' => 'string'),
                    'date'      => array('type' => 'string'),
                    'schedule'  => array('type' => 'string'),
                    'custom'    => array(
                        'type' => 'array',
                        'items' => array(
                            'type' => 'object',
                            'properties' => array(
                                'label' => array('type' => 'string'),
                                'value' => array('type' => 'string')
                            )
                        )
                    )
                )
            )
        ),
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
    
    register_meta('post', '_work_schema_data', array(
        'type'          => 'object',
        'description'   => 'Schema data for the work',
        'single'        => true,
        'show_in_rest'  => array(
            'schema' => array(
                'type' => 'object',
                'properties' => array(
                    'artform'     => array('type' => 'string'),
                    'artMedium'   => array('type' => 'string'),
                    'artworkSurface' => array('type' => 'string'),
                    'width'       => array('type' => 'string'),
                    'height'      => array('type' => 'string'),
                    'depth'       => array('type' => 'string'),
                    'dimensions'  => array('type' => 'string'),
                    'author'      => array('type' => 'string'),
                    'dateCreated' => array('type' => 'string')
                )
            )
        ),
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
}
add_action('init', 'work_register_meta_fields'); 