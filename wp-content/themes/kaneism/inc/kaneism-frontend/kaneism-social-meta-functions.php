<?php
/**
 * Kaneism Social Meta Functions
 *
 * Handles Open Graph, Twitter Card, and other social media metadata
 * for the site head following WordPress best practices.
 *
 * @package kaneism
 */

/**
 * Register hooks for social meta functionality
 */
function kaneism_social_meta_setup() {
    // Check for SEO plugins that might handle this already
    if (class_exists('WPSEO_Frontend') || class_exists('RankMath')) {
        // Some SEO plugins handle this already, but not all aspects
        // We could add complementary tags that the plugins don't handle
        return;
    }
    
    // Add our social meta tags after meta descriptions (priority 2)
    add_action('wp_head', 'kaneism_add_social_meta', 2);
    
    // Add image dimensions for Open Graph images
    add_action('wp_head', 'kaneism_add_social_image_dimensions', 3);
    
    // Remove old Open Graph function from MU plugins if it exists
    kaneism_maybe_remove_old_social_meta();
}
add_action('init', 'kaneism_social_meta_setup');

/**
 * Remove the old Open Graph function from MU plugins if it exists
 */
function kaneism_maybe_remove_old_social_meta() {
    // Check if the old function exists and remove it
    if (function_exists('kane_add_opengraph')) {
        remove_action('wp_head', 'kane_add_opengraph');
    }
    
    // Check if there are any other plugins with the same functionality
    global $wp_filter;
    if (isset($wp_filter['wp_head']) && is_object($wp_filter['wp_head'])) {
        // Look for common OG function names
        $og_functions = array(
            'add_opengraph',
            'og_tags',
            'open_graph_meta',
            'add_open_graph'
        );
        
        foreach ($og_functions as $function) {
            if (has_filter('wp_head', $function)) {
                remove_filter('wp_head', $function);
            }
        }
    }
}

/**
 * Add social media metadata tags to the site head
 */
function kaneism_add_social_meta() {
    // Initialize variables
    $title = '';
    $url = '';
    $image = '';
    $type = 'website';
    $site_name = get_bloginfo('name');
    
    // Default image (used if no featured image is found)
    $default_image = get_theme_mod('kane_default_share_image', get_template_directory_uri() . '/assets/img/logos/login_logo.png');
    
    // Set basic URL
    $url = esc_url((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    
    // Handle different page types
    if (is_singular()) {
        global $post;
        
        // Title - use the post title
        $title = get_the_title();
        
        // URL - use the permalink
        $url = get_permalink();
        
        // Content type
        $type = 'article';
        
        // Image - use featured image or default
        if (has_post_thumbnail($post->ID)) {
            $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
            if ($thumbnail_src) {
                $image = esc_url($thumbnail_src[0]);
            }
        }
        
        // If we're on a product page, set type to product
        if (function_exists('is_product') && is_product()) {
            $type = 'product';
        }
    } elseif (is_home() || is_front_page()) {
        // Home page
        $title = get_bloginfo('name');
        if (get_bloginfo('description')) {
            $title .= ' | ' . get_bloginfo('description');
        }
        $url = home_url('/');
        
        // Try to get image from customizer or theme options
        $image = get_theme_mod('kane_home_share_image', $default_image);
    } elseif (is_tax() || is_category() || is_tag()) {
        // Taxonomy archives
        $term = get_queried_object();
        
        if ($term) {
            $title = $term->name;
            if (is_category()) {
                $title .= ' | Category';
            } elseif (is_tag()) {
                $title .= ' | Tag';
            }
            $title .= ' | ' . get_bloginfo('name');
            
            // Get term image if available (commonly used in WooCommerce)
            if (function_exists('get_term_meta')) {
                $term_image_id = get_term_meta($term->term_id, 'thumbnail_id', true);
                if ($term_image_id) {
                    $term_image = wp_get_attachment_image_src($term_image_id, 'full');
                    if ($term_image) {
                        $image = esc_url($term_image[0]);
                    }
                }
            }
        }
    } elseif (function_exists('is_shop') && is_shop()) {
        // WooCommerce shop page
        $shop_page_id = wc_get_page_id('shop');
        
        if ($shop_page_id > 0) {
            $title = get_the_title($shop_page_id);
            $url = get_permalink($shop_page_id);
            
            if (has_post_thumbnail($shop_page_id)) {
                $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($shop_page_id), 'full');
                if ($thumbnail_src) {
                    $image = esc_url($thumbnail_src[0]);
                }
            }
        } else {
            $title = 'Shop | ' . get_bloginfo('name');
        }
    } elseif (is_post_type_archive()) {
        // Post type archives
        $post_type = get_query_var('post_type');
        $post_type_obj = get_post_type_object($post_type);
        
        if ($post_type_obj) {
            $title = isset($post_type_obj->labels->name) ? $post_type_obj->labels->name : $post_type;
            $title .= ' | ' . get_bloginfo('name');
        }
    } elseif (is_author()) {
        // Author archives
        $author = get_queried_object();
        
        if ($author) {
            $title = isset($author->display_name) ? 'Posts by ' . $author->display_name : 'Author Archive';
            $title .= ' | ' . get_bloginfo('name');
            
            // Get author avatar as image
            $image = get_avatar_url($author->ID, array('size' => 512));
        }
    } elseif (is_search()) {
        // Search results
        $title = 'Search Results for "' . get_search_query() . '"';
        $title .= ' | ' . get_bloginfo('name');
    } elseif (is_404()) {
        // 404 page
        $title = 'Page Not Found | ' . get_bloginfo('name');
    }
    
    // Make sure we have a title and URL
    if (empty($title)) {
        $title = get_bloginfo('name');
    }
    
    if (empty($url)) {
        $url = home_url('/');
    }
    
    // Make sure we have an image
    if (empty($image)) {
        $image = $default_image;
    }
    
    // Output the meta tags
    
    // Open Graph tags
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . PHP_EOL;
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . PHP_EOL;
    echo '<meta property="og:type" content="' . esc_attr($type) . '">' . PHP_EOL;
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . PHP_EOL;
    
    if (!empty($image)) {
        echo '<meta property="og:image" content="' . esc_url($image) . '">' . PHP_EOL;
    }
    
    // Twitter Card tags
    echo '<meta name="twitter:card" content="summary_large_image">' . PHP_EOL;
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . PHP_EOL;
    echo '<meta name="twitter:url" content="' . esc_url($url) . '">' . PHP_EOL;
    
    if (!empty($image)) {
        echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . PHP_EOL;
    }
    
    // Optional Twitter site/creator handles
    $twitter_site = get_theme_mod('kane_twitter_site', '');
    if (!empty($twitter_site)) {
        echo '<meta name="twitter:site" content="@' . esc_attr(str_replace('@', '', $twitter_site)) . '">' . PHP_EOL;
    }
    
    // If it's a single author post, add the author's Twitter handle if available
    if (is_singular() && function_exists('get_the_author_meta')) {
        $twitter_creator = get_the_author_meta('twitter', get_post_field('post_author', get_the_ID()));
        if (!empty($twitter_creator)) {
            echo '<meta name="twitter:creator" content="@' . esc_attr(str_replace('@', '', $twitter_creator)) . '">' . PHP_EOL;
        }
    }
}

/**
 * Add width and height attributes to Open Graph images
 * This helps social platforms properly display images
 */
function kaneism_add_social_image_dimensions() {
    // Only proceed if we have an Open Graph image in the page
    $html = ob_get_clean();
    
    if (strpos($html, 'og:image') !== false) {
        // Find all og:image meta tags
        preg_match_all('/<meta property="og:image" content="([^"]+)">/i', $html, $matches);
        
        if (!empty($matches[1])) {
            // For each image URL, get its dimensions
            foreach ($matches[1] as $image_url) {
                // Check if this is a local URL so we can get its dimensions
                $site_url = site_url();
                if (strpos($image_url, $site_url) === 0) {
                    $local_path = str_replace($site_url, ABSPATH, $image_url);
                    
                    if (file_exists($local_path) && is_readable($local_path)) {
                        $dimensions = getimagesize($local_path);
                        
                        if ($dimensions) {
                            $width = $dimensions[0];
                            $height = $dimensions[1];
                            
                            // Add width and height meta tags
                            $html = str_replace(
                                '<meta property="og:image" content="' . $image_url . '">',
                                '<meta property="og:image" content="' . $image_url . '">' . PHP_EOL . 
                                '<meta property="og:image:width" content="' . $width . '">' . PHP_EOL . 
                                '<meta property="og:image:height" content="' . $height . '">',
                                $html
                            );
                        }
                    }
                }
            }
        }
    }
    
    echo $html;
    ob_start();
}

/**
 * Get social media metadata for a specific post/page
 * 
 * @param int|WP_Post $post_id Post ID or post object
 * @return array Array of social meta data
 */
function kaneism_get_social_meta($post_id = null) {
    // Initialize output array
    $meta = array(
        'title' => '',
        'url' => '',
        'image' => '',
        'type' => 'website',
        'description' => '',
        'site_name' => get_bloginfo('name')
    );
    
    // Get post object
    $post = get_post($post_id);
    
    if ($post) {
        // Set title and URL
        $meta['title'] = get_the_title($post);
        $meta['url'] = get_permalink($post);
        
        // Set type based on post type
        $meta['type'] = 'article';
        if (function_exists('is_product') && $post->post_type === 'product') {
            $meta['type'] = 'product';
        }
        
        // Set description from excerpt or content
        if (has_excerpt($post->ID)) {
            $meta['description'] = strip_tags(get_the_excerpt($post->ID));
        } else {
            $excerpt = strip_tags($post->post_content);
            $excerpt = strip_shortcodes($excerpt);
            $meta['description'] = wp_trim_words($excerpt, 30, '...');
        }
        
        // Set image from featured image or default
        if (has_post_thumbnail($post->ID)) {
            $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
            if ($thumbnail_src) {
                $meta['image'] = $thumbnail_src[0];
            }
        } else {
            // Default image from theme options
            $meta['image'] = get_theme_mod('kane_default_share_image', 
                get_template_directory_uri() . '/assets/img/logos/login_logo.png');
        }
    }
    
    return $meta;
} 