<?php
/**
 * WebP image support functions
 *
 * @package kaneism
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if the browser supports WebP
 *
 * @return bool Whether the browser supports WebP
 */
function kaneism_webp_is_supported() {
	// Check for Accept header
	if ( isset( $_SERVER['HTTP_ACCEPT'] ) && strpos( $_SERVER['HTTP_ACCEPT'], 'image/webp' ) !== false ) {
		return true;
	}

	// Check user agent for known WebP compatible browsers
	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		// Chrome 9+, Opera 12+, Firefox 65+, Edge 18+, Safari 14+
		if ( 
			( strpos( $user_agent, 'Chrome/' ) !== false && preg_match( '/Chrome\/([0-9]+)/', $user_agent, $matches ) && (int) $matches[1] >= 9 ) ||
			( strpos( $user_agent, 'Opera/' ) !== false ) ||
			( strpos( $user_agent, 'Firefox/' ) !== false && preg_match( '/Firefox\/([0-9]+)/', $user_agent, $matches ) && (int) $matches[1] >= 65 ) ||
			( strpos( $user_agent, 'Edge/' ) !== false && preg_match( '/Edge\/([0-9]+)/', $user_agent, $matches ) && (int) $matches[1] >= 18 ) ||
			( strpos( $user_agent, 'Safari/' ) !== false && preg_match( '/Version\/([0-9]+)/', $user_agent, $matches ) && (int) $matches[1] >= 14 )
		) {
			return true;
		}
	}

	return false;
}

/**
 * Check if WebP version of an image exists
 *
 * @param string $image_url URL of the original image.
 * @return bool|string WebP image URL if exists, false otherwise
 */
function kaneism_get_webp_image( $image_url ) {
	// Skip if URL is empty or already a WebP image
	if ( empty( $image_url ) || strpos( $image_url, '.webp' ) !== false ) {
		return false;
	}

	// Skip SVG images
	if ( strpos( $image_url, '.svg' ) !== false ) {
		return false;
	}

	// Get file path from URL
	$upload_dir = wp_upload_dir();
	$file_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $image_url );
	
	// Check if file exists
	if ( ! file_exists( $file_path ) ) {
		return false;
	}
	
	// Get the extension of the original file
	$path_parts = pathinfo($file_path);
	
	// First check for the proper WebP format (image.webp)
	$webp_path = str_replace('.' . $path_parts['extension'], '.webp', $file_path);
    
	// If that doesn't exist, check for the appended format (image.jpg.webp)
	if (!file_exists($webp_path)) {
		$webp_path = $file_path . '.webp';
	}
    
	// Check if either WebP version exists
	if (file_exists($webp_path)) {
		// Also update the URL accordingly
		if (strpos($webp_path, $file_path . '.webp') !== false) {
			// For appended format (image.jpg.webp)
			$webp_url = $image_url . '.webp';
		} else {
			// For replacement format (image.webp)
			$webp_url = str_replace('.' . $path_parts['extension'], '.webp', $image_url);
		}
		
		return $webp_url;
	}
	
	return false;
}

/**
 * Replace image URLs with WebP versions if available and supported
 *
 * @param string $image_url Original image URL.
 * @return string Modified image URL.
 */
function kaneism_replace_with_webp( $image_url ) {
	// Skip if browser doesn't support WebP
	if ( ! kaneism_webp_is_supported() ) {
		return $image_url;
	}
	
	$webp_url = kaneism_get_webp_image( $image_url );
	
	if ( $webp_url ) {
		return $webp_url;
	}
	
	return $image_url;
}

/**
 * Filter image source to use WebP when available
 *
 * @param array|false $image Image data.
 * @param int $attachment_id Attachment ID.
 * @param string|array $size Registered image size or dimensions.
 * @return array|false Modified image data.
 */
function kaneism_filter_get_image_src( $image, $attachment_id, $size ) {
	// Skip processing in admin area to prevent timeouts during post updates
	if (is_admin() && !wp_doing_ajax()) {
		return $image;
	}
	
	if ( ! $image ) {
		return $image;
	}
	
	$image[0] = kaneism_replace_with_webp( $image[0] );
	
	return $image;
}
// Enable WebP for attachment images
add_filter( 'wp_get_attachment_image_src', 'kaneism_filter_get_image_src', 10, 3 );

/**
 * Filter post thumbnail image source to use WebP
 *
 * @param string $html Image HTML.
 * @param int $post_id Post ID.
 * @param int $post_thumbnail_id Thumbnail ID.
 * @param string|array $size Registered image size or dimensions.
 * @param string $attr Query string of attributes.
 * @return string Modified image HTML.
 */
function kaneism_filter_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	// Skip processing in admin area to prevent timeouts during post updates
	if (is_admin() && !wp_doing_ajax()) {
		return $html;
	}
	
	// Check if we should exclude WebP for this context
	if(kaneism_should_exclude_webp()) {
		return $html;
	}
	
	if (!kaneism_webp_is_supported() || empty($html)) {
		return $html;
	}
	
	// Replace image URLs in src attribute
	$html = preg_replace_callback( 
		'/src="([^"]+\.(jpg|jpeg|png))"/i',
		function($matches) {
			$webp_url = kaneism_get_webp_image($matches[1]);
			if ($webp_url) {
				return 'src="' . esc_url($webp_url) . '"';
			}
			return $matches[0];
		},
		$html
	);
	
	return $html;
}
// Enable WebP for post thumbnails
add_filter( 'post_thumbnail_html', 'kaneism_filter_post_thumbnail_html', 10, 5 );

/**
 * Should WebP conversion be excluded in the current context?
 *
 * @return bool Whether to exclude WebP conversion.
 */
function kaneism_should_exclude_webp() {
    // Skip WebP processing in admin during post save/update operations
    if (is_admin() && !wp_doing_ajax()) {
        // Check if we're in a post save/update context
        global $pagenow;
        if (in_array($pagenow, array('post.php', 'post-new.php'))) {
            return true;
        }
    }
    
    // Simple check for URL parameters that indicate we should skip WebP
    if (isset($_GET['disable_webp']) || isset($_GET['test_native_srcset'])) {
        return true;
    }
    
    // Skip WebP for posts with gallery shortcodes
    global $post;
    if ($post && has_shortcode($post->post_content, 'gallery')) {
        return true;
    }
    
    return false;
}

/**
 * Check if a specific element should have WebP excluded based on classes
 * 
 * @param array $attr Image attributes including class
 * @return bool Whether to exclude WebP for this element
 */
function kaneism_should_exclude_element_webp($attr) {
    // If global exclusion is active, exclude this element too
    if (kaneism_should_exclude_webp()) {
        return true;
    }
    
    // Check for specific classes that should bypass WebP
    if (isset($attr['class'])) {
        $excluded_classes = array('native-img', 'no-webp');
        $classes = explode(' ', $attr['class']);
        
        foreach ($excluded_classes as $excluded_class) {
            if (in_array($excluded_class, $classes)) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * Filter image attributes to use WebP
 *
 * @param array $attr Attributes for the image markup.
 * @param WP_Post $attachment Image attachment post.
 * @param string|array $size Requested size.
 * @return array Modified attributes.
 */
function kaneism_filter_wp_get_attachment_image_attributes( $attr, $attachment, $size ) {
    // Skip processing in admin area to prevent timeouts during post updates
    if (is_admin() && !wp_doing_ajax()) {
        return $attr;
    }
    
    // Check if we should exclude WebP for this context
    if (kaneism_should_exclude_element_webp($attr)) {
        return $attr;
    }
    
    // Skip WebP if not supported by browser
    if (!kaneism_webp_is_supported()) {
        return $attr;
    }
    
    // Replace src with WebP version if available
    if (isset($attr['src'])) {
        $webp_src = kaneism_get_webp_image($attr['src']);
        if ($webp_src) {
            $attr['src'] = $webp_src;
            
            // Also handle srcset to ensure format consistency
            if (isset($attr['srcset'])) {
                $srcset_urls = explode(',', $attr['srcset']);
                $new_srcset = array();
                
                foreach ($srcset_urls as $srcset_url) {
                    // Each srcset entry has format "url size"
                    if (preg_match('/(.+?)(\s+.+)/', $srcset_url, $matches)) {
                        $url = trim($matches[1]);
                        $size_info = $matches[2];
                        
                        // Convert URL to WebP
                        $webp_url = kaneism_get_webp_image($url);
                        if ($webp_url) {
                            $new_srcset[] = $webp_url . $size_info;
                        } else {
                            $new_srcset[] = $srcset_url;
                        }
                    } else {
                        // If we can't parse it, keep original
                        $new_srcset[] = $srcset_url;
                    }
                }
                
                $attr['srcset'] = implode(', ', $new_srcset);
            }
        }
    } else if (isset($attr['srcset'])) {
        // If no src but srcset exists, we still need to process the srcset
        $srcset_urls = explode(',', $attr['srcset']);
        $new_srcset = array();
        $any_webp_found = false;
        
        foreach ($srcset_urls as $srcset_url) {
            if (preg_match('/(.+?)(\s+.+)/', $srcset_url, $matches)) {
                $url = trim($matches[1]);
                $size_info = $matches[2];
                
                $webp_url = kaneism_get_webp_image($url);
                if ($webp_url) {
                    $new_srcset[] = $webp_url . $size_info;
                    $any_webp_found = true;
                } else {
                    $new_srcset[] = $srcset_url;
                }
            } else {
                $new_srcset[] = $srcset_url;
            }
        }
        
        if ($any_webp_found) {
            $attr['srcset'] = implode(', ', $new_srcset);
        }
    }
    
    return $attr;
}
// Enable WebP for image attributes
add_filter( 'wp_get_attachment_image_attributes', 'kaneism_filter_wp_get_attachment_image_attributes', 10, 3 );

/**
 * Check WebP conversion for all image URLs in an HTML string
 *
 * @param string $html HTML with image tags
 * @return string Modified HTML with WebP images
 */
function kaneism_check_webp_conversion($html) {
    // Skip processing in admin area to prevent timeouts during post updates
    if (is_admin() && !wp_doing_ajax()) {
        return $html;
    }
    
    // Skip if browser doesn't support WebP
    if (!kaneism_webp_is_supported()) {
        return $html;
    }
    
    // Process src and srcset attributes for img tags
    $html = preg_replace_callback(
        '/(src|srcset)=["\'](.*?)["\']/i',
        function($matches) {
            $attr = $matches[1]; // src or srcset
            $value = $matches[2]; // URL or srcset string
            
            if ($attr === 'src') {
                // Single URL for src
                $webp_url = kaneism_get_webp_image($value);
                if ($webp_url) {
                    return 'src="' . esc_url($webp_url) . '"';
                }
                return $matches[0];
            } else if ($attr === 'srcset') {
                // Multiple URLs for srcset
                $srcset_parts = explode(',', $value);
                $new_srcset_parts = array();
                
                foreach ($srcset_parts as $part) {
                    if (preg_match('/(.+?)(\s+.+)/', trim($part), $url_matches)) {
                        $url = $url_matches[1];
                        $descriptor = $url_matches[2];
                        
                        $webp_url = kaneism_get_webp_image($url);
                        if ($webp_url) {
                            $new_srcset_parts[] = $webp_url . $descriptor;
                        } else {
                            $new_srcset_parts[] = trim($part);
                        }
                    } else {
                        $new_srcset_parts[] = trim($part);
                    }
                }
                
                return 'srcset="' . implode(', ', $new_srcset_parts) . '"';
            }
            
            return $matches[0];
        },
        $html
    );
    
    return $html;
}

// Apply the WebP check to all image HTML output
add_filter('wp_get_attachment_image', 'kaneism_check_webp_conversion', 999);

/**
 * Filter content images to use WebP
 *
 * @param string $content Post content.
 * @return string Modified post content.
 */
function kaneism_filter_content_images( $content ) {
    // Skip processing in admin area to prevent timeouts during post updates
    if (is_admin() && !wp_doing_ajax()) {
        return $content;
    }
    
    // Check if we should exclude WebP for this context
    if (kaneism_should_exclude_webp()) {
        return $content;
    }
    
    if (!kaneism_webp_is_supported() || empty($content)) {
        return $content;
    }
    
    // Replace image URLs in src attribute
    $content = preg_replace_callback(
        '/<img[^>]+src="([^"]+\.(jpg|jpeg|png))"[^>]*>/i',
        function($matches) {
            $img_tag = $matches[0];
            $src = $matches[1];
            
            $webp_url = kaneism_get_webp_image($src);
            if ($webp_url) {
                $img_tag = str_replace('src="' . $src . '"', 'src="' . $webp_url . '"', $img_tag);
            }
            
            return $img_tag;
        },
        $content
    );
    
    return $content;
}
// Enable WebP conversion for content
add_filter( 'the_content', 'kaneism_filter_content_images', 10 );

// Apply the WebP check to images in content
add_filter('the_content', function($content) {
    // Skip processing in admin area to prevent timeouts during post updates
    if (is_admin() && !wp_doing_ajax()) {
        return $content;
    }
    
    return preg_replace_callback(
        '/<img[^>]+class=["\'][^"\']*wp-image-[^"\']*["\'][^>]*>/i',
        function($matches) {
            return kaneism_check_webp_conversion($matches[0]);
        },
        $content
    );
}, 999);

/**
 * Debug function to log WebP conversion activity
 * Only active when WP_DEBUG is true
 *
 * @param string $message The message to log
 * @param mixed $data Additional data to log
 */
function kaneism_webp_debug_log($message, $data = null) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        // Check if we're in admin
        $context = is_admin() ? 'ADMIN' : 'FRONTEND';
        
        // Get the current page
        global $pagenow;
        $page = isset($pagenow) ? $pagenow : 'unknown';
        
        // Create log message
        $log_message = sprintf('[WEBP-%s/%s] %s', $context, $page, $message);
        
        if ($data !== null) {
            $log_message .= ' - Data: ' . (is_array($data) || is_object($data) ? json_encode($data) : $data);
        }
        
        // Log to error log
        error_log($log_message);
    }
}

// Uncomment this to debug WebP conversion activity
// add_action('init', function() {
//     kaneism_webp_debug_log('WebP module initialized');
// });


