<?php
/**
 * Admin-specific functionality
 *
 * @package Work
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Add custom columns to the Work post type admin listing
 *
 * @param array $columns The existing columns.
 * @return array Modified columns.
 */
function work_custom_columns($columns) {
    $new_columns = array();
    
    // Add thumbnail after checkbox but before title
    foreach ($columns as $key => $value) {
        if ($key === 'cb') {
            $new_columns[$key] = $value;
            $new_columns['thumbnail'] = __('Thumbnail', 'work');
        } else {
            $new_columns[$key] = $value;
        }
    }
    
    // Add featured column after title
    $title_position = array_search('title', array_keys($new_columns));
    if ($title_position !== false) {
        $new_columns = array_slice($new_columns, 0, $title_position + 1, true) +
                      array('featured' => __('Featured', 'work')) +
                      array_slice($new_columns, $title_position + 1, count($new_columns) - $title_position - 1, true);
    }
    
    // Add gallery count column
    $new_columns['gallery_count'] = __('Gallery Images', 'work');
    
    return $new_columns;
}
add_filter('manage_work_posts_columns', 'work_custom_columns');

/**
 * Display content for custom columns in the Work post type admin listing
 *
 * @param string $column The column name.
 * @param int    $post_id The post ID.
 */
function work_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo '<a href="' . esc_url(get_edit_post_link($post_id)) . '">';
                echo get_the_post_thumbnail($post_id, array(50, 50));
                echo '</a>';
            } else {
                echo '<span aria-hidden="true">—</span>';
            }
            break;
            
        case 'featured':
            $is_featured = get_post_meta($post_id, '_work_is_featured', true);
            $nonce = wp_create_nonce('work_toggle_featured_nonce');
            
            echo '<a href="#" class="work-toggle-featured" data-post-id="' . esc_attr($post_id) . '" data-nonce="' . esc_attr($nonce) . '">';
            if ($is_featured) {
                echo '<span class="dashicons dashicons-star-filled" style="color: #ffb900;" title="' . esc_attr__('Featured - Click to remove', 'work') . '"></span>';
            } else {
                echo '<span class="dashicons dashicons-star-empty" style="color: #ccc;" title="' . esc_attr__('Not Featured - Click to feature', 'work') . '"></span>';
            }
            echo '</a>';
            break;
            
        case 'gallery_count':
            $gallery_images = get_post_meta($post_id, '_work_gallery_images', true);
            if (!empty($gallery_images) && is_array($gallery_images)) {
                echo count($gallery_images);
            } else {
                echo '<span aria-hidden="true">0</span>';
            }
            break;
    }
}
add_action('manage_work_posts_custom_column', 'work_custom_column_content', 10, 2);

/**
 * Make custom columns sortable
 *
 * @param array $columns The sortable columns.
 * @return array Modified sortable columns.
 */
function work_sortable_columns($columns) {
    $columns['gallery_count'] = 'gallery_count';
    $columns['featured'] = 'featured';
    return $columns;
}
add_filter('manage_edit-work_sortable_columns', 'work_sortable_columns');

/**
 * Add custom sorting for gallery count and featured status
 *
 * @param WP_Query $query The query object.
 */
function work_custom_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    if ($query->get('post_type') === 'work') {
        // Handle sorting
        if ($query->get('orderby') === 'gallery_count') {
            $query->set('meta_key', '_work_gallery_images');
            $query->set('orderby', 'meta_value_num');
        } elseif ($query->get('orderby') === 'featured') {
            $query->set('meta_key', '_work_is_featured');
            $query->set('orderby', 'meta_value_num');
        }
        
        // Handle featured filter
        if (isset($_GET['featured_filter']) && $_GET['featured_filter'] === 'featured') {
            $query->set('meta_key', '_work_is_featured');
            $query->set('meta_value', '1');
        }
    }
}
add_action('pre_get_posts', 'work_custom_orderby');

/**
 * Add featured filter to the admin list
 */
function work_add_featured_filter() {
    global $typenow;
    
    if ($typenow === 'work') {
        $current_filter = isset($_GET['featured_filter']) ? $_GET['featured_filter'] : '';
        ?>
        <select name="featured_filter" id="featured-filter">
            <option value=""><?php _e('All items', 'work'); ?></option>
            <option value="featured" <?php selected($current_filter, 'featured'); ?>><?php _e('Featured only', 'work'); ?></option>
        </select>
        <?php
    }
}
add_action('restrict_manage_posts', 'work_add_featured_filter');

/**
 * Add custom CSS for admin columns
 */
function work_admin_styles() {
    $screen = get_current_screen();
    
    if ($screen && $screen->post_type === 'work' && $screen->base === 'edit') {
        ?>
        <style>
            .column-thumbnail {
                width: 70px;
            }
            .column-featured {
                width: 80px;
                text-align: center;
            }
            .column-featured .dashicons {
                cursor: pointer;
                transition: transform 0.2s ease;
            }
            .column-featured .dashicons:hover {
                transform: scale(1.2);
            }
            .column-gallery_count {
                width: 100px;
                text-align: center;
            }
        </style>
        <?php
    }
}
add_action('admin_head', 'work_admin_styles');

/**
 * Enqueue admin scripts
 */
function work_admin_scripts() {
    $screen = get_current_screen();
    
    if ($screen && $screen->post_type === 'work' && $screen->base === 'edit') {
        wp_enqueue_script(
            'work-admin-js',
            WORK_PLUGIN_URL . 'assets/js/work-admin.js',
            array('jquery'),
            WORK_VERSION,
            true
        );
        
        wp_localize_script(
            'work-admin-js',
            'workAdminVars',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'toggleFeaturedNonce' => wp_create_nonce('work_toggle_featured_ajax_nonce'),
                'toggleFeaturedSuccess' => __('Featured status updated successfully!', 'work'),
                'toggleFeaturedError' => __('Error updating featured status. Please try again.', 'work')
            )
        );
    }
}
add_action('admin_enqueue_scripts', 'work_admin_scripts');

/**
 * AJAX handler for toggling featured status
 */
function work_toggle_featured_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'work_toggle_featured_ajax_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed.', 'work')));
    }
    
    // Check if post ID is set
    if (!isset($_POST['post_id']) || empty($_POST['post_id'])) {
        wp_send_json_error(array('message' => __('No post ID provided.', 'work')));
    }
    
    $post_id = intval($_POST['post_id']);
    
    // Check if user has permission
    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error(array('message' => __('You do not have permission to edit this post.', 'work')));
    }
    
    // Get current featured status
    $is_featured = get_post_meta($post_id, '_work_is_featured', true);
    
    // Toggle featured status
    $new_status = $is_featured ? '0' : '1';
    $updated = update_post_meta($post_id, '_work_is_featured', $new_status);
    
    if ($updated) {
        wp_send_json_success(array(
            'message' => __('Featured status updated successfully!', 'work'),
            'new_status' => $new_status,
            'post_id' => $post_id
        ));
    } else {
        wp_send_json_error(array('message' => __('Error updating featured status.', 'work')));
    }
}
add_action('wp_ajax_work_toggle_featured', 'work_toggle_featured_ajax');

/**
 * Add bulk actions for featuring/unfeaturing work items
 *
 * @param array $bulk_actions Array of bulk actions.
 * @return array Modified bulk actions.
 */
function work_register_bulk_actions($bulk_actions) {
    $bulk_actions['feature_work'] = __('Mark as Featured', 'work');
    $bulk_actions['unfeature_work'] = __('Remove Featured Status', 'work');
    return $bulk_actions;
}
add_filter('bulk_actions-edit-work', 'work_register_bulk_actions');

/**
 * Handle bulk actions for featuring/unfeaturing work items
 *
 * @param string $redirect_to The redirect URL.
 * @param string $doaction The action being taken.
 * @param array $post_ids The items to take the action on.
 * @return string Modified redirect URL.
 */
function work_handle_bulk_actions($redirect_to, $doaction, $post_ids) {
    if ($doaction !== 'feature_work' && $doaction !== 'unfeature_work') {
        return $redirect_to;
    }
    
    $featured_value = ($doaction === 'feature_work') ? '1' : '0';
    $updated = 0;
    
    foreach ($post_ids as $post_id) {
        if (current_user_can('edit_post', $post_id)) {
            update_post_meta($post_id, '_work_is_featured', $featured_value);
            $updated++;
        }
    }
    
    $redirect_to = add_query_arg(
        array(
            'bulk_featured' => $updated,
            'bulk_featured_action' => $doaction,
        ),
        $redirect_to
    );
    
    return $redirect_to;
}
add_filter('handle_bulk_actions-edit-work', 'work_handle_bulk_actions', 10, 3);

/**
 * Display admin notice after bulk action
 */
function work_bulk_action_admin_notice() {
    if (empty($_REQUEST['bulk_featured'])) {
        return;
    }
    
    $count = intval($_REQUEST['bulk_featured']);
    $action = isset($_REQUEST['bulk_featured_action']) ? sanitize_text_field($_REQUEST['bulk_featured_action']) : '';
    
    if ($action === 'feature_work') {
        $message = sprintf(
            _n(
                '%s work item marked as featured.',
                '%s work items marked as featured.',
                $count,
                'work'
            ),
            number_format_i18n($count)
        );
    } else {
        $message = sprintf(
            _n(
                'Featured status removed from %s work item.',
                'Featured status removed from %s work items.',
                $count,
                'work'
            ),
            number_format_i18n($count)
        );
    }
    
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($message) . '</p></div>';
}
add_action('admin_notices', 'work_bulk_action_admin_notice'); 