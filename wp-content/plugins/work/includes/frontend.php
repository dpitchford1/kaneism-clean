<?php
/**
 * Frontend Display Functions
 *
 * @package Work
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants if they don't exist
if (!defined('WORK_VERSION')) {
    define('WORK_VERSION', '1.0.0');
}

// Get plugin directory path and URL
$work_plugin_dir = plugin_dir_path(dirname(__FILE__));
$work_plugin_url = plugin_dir_url(dirname(__FILE__));

/**
 * Enqueue scripts and styles for the frontend
 */
function work_enqueue_scripts() {
    // Get the plugin directory URL
    $plugin_url = plugin_dir_url(dirname(__FILE__));

    // Load on single work post
    if (is_singular('work')) {
        // Get the current post ID
        $post_id = get_the_ID();
        
        // Check if this post has gallery images
        $has_gallery = false;
        if (function_exists('work_get_gallery_images')) {
            $gallery_images = work_get_gallery_images($post_id);
            $has_gallery = !empty($gallery_images);
        }
        
        // Only load gallery scripts if we have images
        if ($has_gallery) {
            // Enqueue local Swiper library
            wp_enqueue_script(
                'swiper-js',
                $plugin_url . 'assets/js/resources/swiper.min.js',
                array(),
                defined('WORK_VERSION') ? WORK_VERSION : '1.0',
                true
            );
            
            // Enqueue Work gallery script
            wp_enqueue_script(
                'work-gallery',
                $plugin_url . 'assets/js/work-gallery.min.js',
                array('swiper-js'), // Depend on Swiper
                defined('WORK_VERSION') ? WORK_VERSION : '1.0',
                true // Load in footer
            );
        }
        return;
    }

    // Load on "canvases" category archive (taxonomy-work_category.php with canvases)
    if (is_tax('work_category')) {
        $term = get_queried_object();
        if ($term && $term->slug === 'canvases') {
            // Enqueue Swiper
            wp_enqueue_script(
                'swiper-js',
                $plugin_url . 'assets/js/resources/swiper.min.js',
                array(),
                defined('WORK_VERSION') ? WORK_VERSION : '1.0',
                true
            );
            // Enqueue gallery-listing.js
            wp_enqueue_script(
                'work-gallery-listing',
                $plugin_url . 'assets/js/gallery-listing.min.js',
                array('swiper-js'),
                defined('WORK_VERSION') ? WORK_VERSION : '1.0',
                true
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'work_enqueue_scripts');

/**
 * Fix term links for the work_category taxonomy
 * 
 * @param string $termlink The term link.
 * @param object $term The term object.
 * @param string $taxonomy The taxonomy slug.
 * @return string Modified term link.
 */
function work_fix_category_term_link($termlink, $term, $taxonomy) {
    if ($taxonomy == 'work_category') {
        // Replace the default /work_category/ with /work/
        $termlink = str_replace('/work_category/', '/work/', $termlink);
    }
    return $termlink;
}
add_filter('term_link', 'work_fix_category_term_link', 10, 3);

/**
 * Get just the work description (content without gallery)
 *
 * @param int $post_id Post ID.
 * @return string HTML content.
 */
function work_get_description($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $post = get_post($post_id);
    if (!$post) {
        return '';
    }
    
    // Return just the post content without the gallery
    return apply_filters('the_content', $post->post_content);
}

/**
 * Get project details for a work item
 *
 * @param int $post_id Post ID.
 * @return array Project details.
 */
function work_get_project_details($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // Get details from new data function
    $project_details = work_get_project_details_data($post_id);
    
    // Define default fields (should match meta box defaults)
    $default_fields = array(
        'type' => __('Type', 'work'),
        'location' => __('Location', 'work'),
        'materials' => __('Materials', 'work'),
        'size' => __('Size', 'work'),
        'date' => __('Date', 'work'),
        'schedule' => __('Schedule', 'work')
    );
    
    // Filter for adding custom fields
    $fields = apply_filters('work_project_detail_fields', $default_fields);
    
    // Build the details array
    $details = array();
    
    // Add standard fields
    foreach ($fields as $field_id => $field_label) {
        if (isset($project_details[$field_id]) && !empty($project_details[$field_id])) {
            $details[] = array(
                'label' => $field_label,
                'value' => $project_details[$field_id]
            );
        }
    }
    
    // Add custom fields
    if (isset($project_details['custom_fields']) && is_array($project_details['custom_fields'])) {
        foreach ($project_details['custom_fields'] as $custom_field) {
            if (!empty($custom_field['label']) && !empty($custom_field['value'])) {
                $details[] = array(
                    'label' => $custom_field['label'],
                    'value' => $custom_field['value']
                );
            }
        }
    }
    
    return $details;
}

/**
 * Generate Schema.org structured data for work items
 *
 * @param int $post_id Post ID.
 * @return array Schema data.
 */
function work_generate_schema_data($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'work') {
        return array();
    }
    
    // Get project details from meta
    $project_details = get_post_meta($post_id, '_work_project_details', true);
    if (!is_array($project_details)) {
        $project_details = array();
    }
    
    // Base schema data
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'VisualArtwork',
        'name' => get_the_title($post_id),
        'description' => wp_strip_all_tags(get_the_excerpt($post_id)),
        'image' => get_the_post_thumbnail_url($post_id, 'full'),
        'author' => array(
            '@type' => 'Person',
            'name' => 'kaneism'
        ),
        'datePublished' => get_the_date('c', $post_id)
    );
    
    // Map project detail fields to schema properties
    $field_mappings = array(
        'type' => 'artform',
        'location' => 'contentLocation',
        'materials' => 'artMedium',
        'size' => null, // Handle separately for width/height
        'date' => 'dateCreated',
        'schedule' => null // Not a standard schema property
    );
    
    // Add mapped fields to schema
    foreach ($field_mappings as $field_id => $schema_property) {
        if ($schema_property && isset($project_details[$field_id]) && !empty($project_details[$field_id])) {
            $schema[$schema_property] = $project_details[$field_id];
        }
    }
    
    // Handle size specially (parse for width/height/depth)
    if (isset($project_details['size']) && !empty($project_details['size'])) {
        // If size contains dimensions like "90ft x 38ft"
        $size = $project_details['size'];
        
        // Try to parse width and height
        if (preg_match('/(\d+[.\d]*\s*(?:ft|in|cm|m)?)(?:\s*x\s*)(\d+[.\d]*\s*(?:ft|in|cm|m)?)(?:\s*x\s*(\d+[.\d]*\s*(?:ft|in|cm|m)?))?/i', $size, $matches)) {
            if (isset($matches[1])) {
                $schema['width'] = $matches[1];
            }
            if (isset($matches[2])) {
                $schema['height'] = $matches[2];
            }
            if (isset($matches[3])) {
                $schema['depth'] = $matches[3];
            } else {
                $schema['depth'] = '0';
            }
        } else {
            // Just use the whole size as artworkSurface
            $schema['artworkSurface'] = $size;
        }
    }
    
    // Add custom fields
    if (isset($project_details['custom_fields']) && is_array($project_details['custom_fields'])) {
        foreach ($project_details['custom_fields'] as $custom_field) {
            if (!empty($custom_field['label']) && !empty($custom_field['value'])) {
                // Convert common field names to schema properties
                $label = strtolower($custom_field['label']);
                
                // Map common field labels to schema properties
                switch ($label) {
                    case 'surface':
                        $schema['artworkSurface'] = $custom_field['value'];
                        break;
                    case 'medium':
                        $schema['artMedium'] = $custom_field['value'];
                        break;
                    case 'width':
                        $schema['width'] = $custom_field['value'];
                        break;
                    case 'height':
                        $schema['height'] = $custom_field['value'];
                        break;
                    case 'depth':
                        $schema['depth'] = $custom_field['value'];
                        break;
                    default:
                        // Add as additional property
                        if (!isset($schema['additionalProperty'])) {
                            $schema['additionalProperty'] = array();
                        }
                        $schema['additionalProperty'][] = array(
                            '@type' => 'PropertyValue',
                            'name' => $custom_field['label'],
                            'value' => $custom_field['value']
                        );
                }
            }
        }
    }
    
    return apply_filters('work_schema_data', $schema, $post_id);
}

/**
 * Output Schema.org structured data in the footer using dedicated schema fields
 */
function work_output_schema_data() {
    if (is_singular('work')) {
        $post_id = get_the_ID();
        
        // First try to get dedicated schema from meta
        $schema_data = get_post_meta($post_id, '_work_schema_data', true);
        
        // If no dedicated schema found or it's incomplete, generate it automatically
        if (empty($schema_data) || !is_array($schema_data) || count($schema_data) < 3) {
            // Use the automatic schema generation as a fallback
            $schema = work_generate_schema_data($post_id);
        } else {
            // Build schema array from manual schema data
            $schema = array(
                '@context' => 'https://schema.org',
                '@type' => isset($schema_data['type']) && !empty($schema_data['type']) ? $schema_data['type'] : 'VisualArtwork',
                'name' => get_the_title($post_id),
                'author' => array(
                    '@type' => 'Person',
                    'name' => 'kaneism'
                ),
                'datePublished' => get_the_date('c', $post_id)
            );
            
            // Add description
            if (isset($schema_data['desc']) && !empty($schema_data['desc'])) {
                $schema['description'] = $schema_data['desc'];
            } else {
                $schema['description'] = wp_strip_all_tags(get_the_excerpt($post_id));
            }
            
            // Add featured image
            if (has_post_thumbnail($post_id)) {
                $schema['image'] = get_the_post_thumbnail_url($post_id, 'full');
            }
            
            // Add location
            if (isset($schema_data['location']) && !empty($schema_data['location'])) {
                $schema['contentLocation'] = array(
                    '@type' => 'Place',
                    'name' => $schema_data['location']
                );
            }
            
            // Add medium
            if (isset($schema_data['medium']) && !empty($schema_data['medium'])) {
                $schema['artMedium'] = $schema_data['medium'];
            }
            
            // Add surface
            if (isset($schema_data['surface']) && !empty($schema_data['surface'])) {
                $schema['artworkSurface'] = $schema_data['surface'];
            }
            
            // Add date
            if (isset($schema_data['date']) && !empty($schema_data['date'])) {
                $schema['dateCreated'] = $schema_data['date'];
            }
            
            // Add dimensions
            if (
                (isset($schema_data['width']) && !empty($schema_data['width'])) || 
                (isset($schema_data['height']) && !empty($schema_data['height'])) || 
                (isset($schema_data['depth']) && !empty($schema_data['depth']))
            ) {
                // Add individual dimension properties
                if (isset($schema_data['width']) && !empty($schema_data['width'])) {
                    $schema['width'] = $schema_data['width'];
                }
                
                if (isset($schema_data['height']) && !empty($schema_data['height'])) {
                    $schema['height'] = $schema_data['height'];
                }
                
                if (isset($schema_data['depth']) && !empty($schema_data['depth'])) {
                    $schema['depth'] = $schema_data['depth'];
                }
            }
        }
        
        // Add a comment to indicate the schema source
        echo '<!-- Schema.org data for Work project -->' . PHP_EOL;
        
        // Apply filter for custom modifications
        $schema = apply_filters('work_schema_output', $schema, $post_id);
        
        if (!empty($schema)) {
            echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>' . PHP_EOL;
        }
    }
}
// Changed from wp_head to wp_footer for better placement
add_action('wp_footer', 'work_output_schema_data', 5);

/**
 * Fix query variables to ensure taxonomy pages and single posts work with our custom URL structure
 * 
 * @param object $query The WP_Query instance.
 * @return void
 */
function work_fix_category_query($query) {
    // Only modify main query
    if (!$query->is_main_query()) {
        return;
    }
    
    // Get the full request URI
    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    
    // Process URL pattern for work items with category: /work/category/item/
    if (preg_match('#/work/([^/]+)/([^/]+)/?$#', $request_uri, $matches)) {
        $category_slug = $matches[1];
        $post_slug = $matches[2];
        
        // Check if this is a valid post
        $posts = get_posts([
            'name' => $post_slug,
            'post_type' => 'work',
            'posts_per_page' => 1,
        ]);
        
        if (!empty($posts)) {
            // This is a single post
            $query->set('work', $post_slug);
            $query->set('post_type', 'work');
            $query->set('name', $post_slug);
            
            // Set category for reference
            $query->set('work_category', $category_slug);
            
            // Update query flags
            $query->is_tax = false;
            $query->is_singular = true;
            $query->is_single = true;
            $query->is_archive = false;
            
            return;
        }
    }
    
    // Process URL pattern for category archives: /work/category/
    if (preg_match('#/work/([^/]+)/?$#', $request_uri, $matches)) {
        $category_slug = $matches[1];
        
        // Check if this is actually a work_category term
        $term = get_term_by('slug', $category_slug, 'work_category');
        
        if ($term) {
            // This is a category archive
            $query->set('work_category', $category_slug);
            $query->set('post_type', 'work');
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'work_category',
                    'field'    => 'slug',
                    'terms'    => $category_slug,
                ),
            ));
            
            // Clear conflicting query vars
            $query->set('name', '');
            $query->set('work', '');
            
            // Make sure the term is properly set as the queried object
            // This is crucial for menu and template loading
            $query->queried_object = $term;
            $query->queried_object_id = $term->term_id;
            
            // Update query flags
            $query->is_tax = true;
            $query->is_singular = false;
            $query->is_single = false;
            $query->is_post_type_archive = false;
            $query->is_archive = true;
            
            return;
        }
    }
}
add_action('pre_get_posts', 'work_fix_category_query', 5);

/**
 * Fix menu item active state for Work pages
 * 
 * @param array $classes The CSS classes applied to the menu item
 * @param object $item The current menu item
 * @param array $args An array of wp_nav_menu() arguments
 * @param int $depth Depth of menu item
 * @return array Modified CSS classes
 */
function work_fix_menu_active_state($classes, $item, $args, $depth) {
    // Only proceed if we're on a work-related page
    if (!is_singular('work') && !is_post_type_archive('work') && !is_tax('work_category')) {
        return $classes;
    }
    
    // If the menu item URL is the Work archive
    if (is_post_type_archive_link('work', $item->url)) {
        // Add active classes
        $classes[] = 'current-menu-item';
        $classes[] = 'current_page_item';
    }
    
    // If it's a Work category page
    if (is_tax('work_category')) {
        $term = get_queried_object();
        $term_url = home_url('/work/' . $term->slug . '/');
        
        // If the menu item is this category
        if (trailingslashit($item->url) === $term_url) {
            $classes[] = 'current-menu-item';
            $classes[] = 'current_page_item';
        }
        
        // Also highlight the main Work menu item as parent
        if (is_post_type_archive_link('work', $item->url)) {
            $classes[] = 'current-menu-ancestor';
            $classes[] = 'current-menu-parent';
        }
    }
    
    // If it's a single Work post
    if (is_singular('work')) {
        // Always highlight the Work archive item as the parent
        if (is_post_type_archive_link('work', $item->url)) {
            $classes[] = 'current-menu-ancestor';
            $classes[] = 'current-menu-parent';
        }
        
        // If the menu item is for the current post's category
        $categories = get_the_terms(get_the_ID(), 'work_category');
        if ($categories && !is_wp_error($categories)) {
            $category = reset($categories);
            $category_url = home_url('/work/' . $category->slug . '/');
            
            if (trailingslashit($item->url) === $category_url) {
                $classes[] = 'current-menu-ancestor';
                $classes[] = 'current-menu-parent';
            }
        }
        
        // If the menu item is this specific post (unlikely but possible)
        $post_url = work_get_proper_permalink(get_the_ID());
        if (trailingslashit($item->url) === $post_url) {
            $classes[] = 'current-menu-item';
            $classes[] = 'current_page_item';
        }
    }
    
    return $classes;
}
add_filter('nav_menu_css_class', 'work_fix_menu_active_state', 10, 4);

/**
 * Check if a URL is the Work post type archive link
 * 
 * @param string $url URL to check
 * @return bool True if the URL is the work archive
 */
function is_post_type_archive_link($post_type, $url) {
    $archive_url = get_post_type_archive_link($post_type);
    
    // Normalize URLs for comparison
    $url = trailingslashit($url);
    $archive_url = trailingslashit($archive_url);
    
    // Basic direct comparison
    if ($url === $archive_url) {
        return true;
    }
    
    // Handle different domain situations (development vs. production)
    $url_parts = parse_url($url);
    $archive_parts = parse_url($archive_url);
    
    // Compare paths if we have different domains
    if (isset($url_parts['path']) && isset($archive_parts['path'])) {
        return trailingslashit($url_parts['path']) === trailingslashit($archive_parts['path']);
    }
    
    return false;
}

/**
 * Add custom body classes for Work pages
 * 
 * @param array $classes The body classes
 * @return array Modified body classes
 */
function work_body_classes($classes) {
    // Add a general work class for all work-related pages
    if (is_singular('work') || is_post_type_archive('work') || is_tax('work_category')) {
        $classes[] = 'work-section';
    }
    
    // Add specific classes for different work page types
    if (is_singular('work')) {
        $classes[] = 'work-single';
        
        // Add the work category as a body class
        $categories = get_the_terms(get_the_ID(), 'work_category');
        if ($categories && !is_wp_error($categories)) {
            foreach ($categories as $category) {
                $classes[] = 'work-category-' . $category->slug;
            }
        }
    }
    
    if (is_post_type_archive('work')) {
        $classes[] = 'work-archive';
    }
    
    if (is_tax('work_category')) {
        $classes[] = 'work-category-archive';
        $term = get_queried_object();
        $classes[] = 'work-category-' . $term->slug;
    }
    
    return $classes;
}
add_filter('body_class', 'work_body_classes');

/**
 * Add special body classes for work subcategory pages
 * This helps ensure the templates display correctly
 *
 * @param array $classes Body classes
 * @return array Modified body classes
 */
function work_fix_subcategory_body_classes($classes) {
    // Check if we're on a work category page
    if (is_tax('work_category')) {
        $term = get_queried_object();
        
        // Add a general work class
        $classes[] = 'work-page';
        $classes[] = 'work-taxonomy';
        
        // Add category-specific class
        $classes[] = 'work-category-' . $term->slug;
        
        // Add class for child categories
        if ($term->parent != 0) {
            $classes[] = 'work-subcategory';
            
            // Add parent category class
            $parent = get_term($term->parent, 'work_category');
            if ($parent && !is_wp_error($parent)) {
                $classes[] = 'work-parent-category-' . $parent->slug;
            }
        } else {
            $classes[] = 'work-parent-category';
        }
        
        // Force global-template class which might be needed for header display
        $classes[] = 'global-template';
    }
    
    return $classes;
}
add_filter('body_class', 'work_fix_subcategory_body_classes', 20);

/**
 * AJAX handler to get the navigation HTML
 * This is used by the diagnostic tool to fetch and inject navigation
 */
function work_ajax_get_navigation() {
    // Get path to navigation template from theme
    $nav_path = get_template_directory() . '/template-parts/header/navigation.php';
    
    // Check if file exists
    if (!file_exists($nav_path)) {
        wp_send_json_error('Navigation template not found at: ' . $nav_path);
        return;
    }
    
    // Setup needed variables that might be used in the navigation template
    global $wp_query;
    $is_front_page = is_front_page();
    $is_cart_page = is_page('cart');
    
    // Start output buffering to capture the navigation HTML
    ob_start();
    
    // Include the navigation template
    include $nav_path;
    
    // Get the buffered content
    $navigation_html = ob_get_clean();
    
    // Send it back without JSON encoding to keep HTML intact
    echo $navigation_html;
    exit;
}

// add_action('wp_ajax_get_work_navigation', 'work_ajax_get_navigation');
// add_action('wp_ajax_nopriv_get_work_navigation', 'work_ajax_get_navigation');