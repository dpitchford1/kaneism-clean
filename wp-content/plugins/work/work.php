<?php
/*
Plugin Name:        Work
Plugin URI:         https://example.com/plugins/work
Description:        Custom post type for portfolio works
Version:            1.0.1
Author:             Dylan Pitchford
Author URI:         https://example.com
Text Domain:        work
Domain Path:        /languages
License:            GPL v2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.txt
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('WORK_VERSION', '1.0.1');
define('WORK_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WORK_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once WORK_PLUGIN_DIR . 'includes/post-types.php';
require_once WORK_PLUGIN_DIR . 'includes/taxonomies.php';
require_once WORK_PLUGIN_DIR . 'includes/meta-boxes.php';
require_once WORK_PLUGIN_DIR . 'includes/data-functions.php';
require_once WORK_PLUGIN_DIR . 'includes/data-migration.php';
require_once WORK_PLUGIN_DIR . 'includes/frontend.php';
require_once WORK_PLUGIN_DIR . 'includes/template-functions.php';
require_once WORK_PLUGIN_DIR . 'includes/template-loader.php';

// Include our new gallery functionality
require_once WORK_PLUGIN_DIR . 'includes/work-gallery.php';

// Include admin-specific files
if (is_admin()) {
    require_once WORK_PLUGIN_DIR . 'includes/admin.php';
}

// Register the custom post type
add_action('init', 'create_work_cpt');

// Register custom taxonomies
add_action('init', 'register_work_taxonomies');

// Activation hook
register_activation_hook(__FILE__, 'work_plugin_activation');
function work_plugin_activation() {
    // Flush rewrite rules on activation
    create_work_cpt();
    register_work_taxonomies();
    flush_rewrite_rules();
    
    // Migrate terms from old taxonomies if they exist
    work_migrate_taxonomy_terms();
    
    // Set version for tracking
    update_option('work_plugin_version', WORK_VERSION);
}

/**
 * Migrate terms from old taxonomies to the new work_category taxonomy
 */
function work_migrate_taxonomy_terms() {
    $old_taxonomies = array('murals', 'canavases', 'design');
    
    foreach ($old_taxonomies as $old_taxonomy) {
        if (taxonomy_exists($old_taxonomy)) {
            $terms = get_terms(array(
                'taxonomy' => $old_taxonomy,
                'hide_empty' => false,
            ));
            
            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    // Check if term already exists in the new taxonomy
                    if (!term_exists($term->name, 'work_category')) {
                        // Get all posts with this term
                        $posts = get_posts(array(
                            'post_type' => 'work',
                            'numberposts' => -1,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => $old_taxonomy,
                                    'field' => 'id',
                                    'terms' => $term->term_id,
                                ),
                            ),
                        ));
                        
                        // Create the term in the new taxonomy
                        $new_term = wp_insert_term($term->name, 'work_category', array(
                            'description' => $term->description,
                            'slug' => $term->slug,
                        ));
                        
                        // Assign the new term to all posts that had the old term
                        if (!is_wp_error($new_term) && !empty($posts)) {
                            foreach ($posts as $post) {
                                wp_set_object_terms($post->ID, $term->name, 'work_category', true);
                            }
                        }
                    }
                }
            }
        }
    }
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'work_plugin_deactivation');
function work_plugin_deactivation() {
    // Flush rewrite rules on deactivation
    flush_rewrite_rules();
}

/**
 * Check if plugin has been updated and needs rewrite rules flushed
 * Uses a transient and version tracking to ensure it runs when needed
 */
function work_check_version_and_flush() {
    $current_version = get_option('work_plugin_version', '0.0.0');
    
    // If the stored version is different than the current one, we need to flush
    if (version_compare($current_version, WORK_VERSION, '!=')) {
        // Ensure post types and taxonomies are registered
        create_work_cpt();
        register_work_taxonomies();
        work_add_custom_rewrite_rules();
        
        // Flush the rules
        flush_rewrite_rules();
        
        // Update the version option
        update_option('work_plugin_version', WORK_VERSION);
        
        // Also update the transient to prevent double-flush
        set_transient('work_flush_rewrite_rules', 1, HOUR_IN_SECONDS * 24);
    }
}
add_action('admin_init', 'work_check_version_and_flush', 5);

/**
 * Flush rewrite rules when visiting admin page if needed
 * Uses a transient to ensure it only runs once 
 */
function work_flush_rewrite_rules_maybe() {
    // If the transient doesn't exist, we need to flush the rewrite rules
    if (!get_transient('work_flush_rewrite_rules')) {
        flush_rewrite_rules();
        set_transient('work_flush_rewrite_rules', 1, HOUR_IN_SECONDS * 24);
    }
}
add_action('admin_init', 'work_flush_rewrite_rules_maybe', 10);

/**
 * Load plugin text domain for translations
 */
function work_load_textdomain() {
    load_plugin_textdomain('work', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'work_load_textdomain'); 