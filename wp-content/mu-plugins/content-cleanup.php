<?php
/*
Plugin Name:        Content Cleanup
Plugin URI:         
Description:        Robust Content Cleanup and new features
Version:            1.0.0
Author:             Dylan Pitchford
Author URI:         
*/

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'cleanup_content' ) ) :

	/**
	 * The main markup cleanup class
	 */
	class cleanup_content {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {

			// add_action( 'language_attributes', array( $this, 'add_opengraph_doctype' ) );
			// add_action( 'wp_resource_hints', array( $this, 'disable_emojis_remove_dns_prefetch' ), 10, 2 );

			// cleaning up random code around images
			add_action( 'the_content', array( $this, 'filter_ptags_on_images' ) );
	
			// cleaning up excerpt
			//add_filter( 'excerpt_more', 'excerpt_more' );
	
			// Remove Commenting completely
			add_action( 'init', array( $this, 'comments_clean_header_hook' ) );

			add_action( 'style_loader_src', array( $this, 'remove_wp_ver_css_js' ), 9999 );

			// Remove links around images
			add_action( 'the_content', array( $this, 'ks_remove_image_link' ) );

            //add_action( 'wp_head', array( $this, 'meta_description' ), 6 );
            

		}

/**
 * Add support for schema attributes on the markup
 *
 * @since 1.0
 */
function html_schema() {

    $schema = 'http://schema.org/';
 
    // Is single post
    if( is_single()) {
        $type = "Article";
    }
    // Is blog home, archive or category
    else if( is_home() || is_archive() || is_category() ) {
        $type = "Blog";
    }
    // Is static front page
    else if( is_front_page()) {
        $type = "Website";
    }
    // Is a general page
     else {
        $type = 'WebPage';
    }
    echo 'itemscope="itemscope" itemtype="' . $schema . $type . '"';
}


/**
 * remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
 */
public function filter_ptags_on_images($content){
    return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

/**
 * This removes the annoying […] to a Read More link
 */
public function excerpt_more($more) {
    global $post;
    // edit here if you like
    return '...  <a class="excerpt-read-more" href="'. get_permalink( $post->ID ) . '">'. __( 'Read more &raquo;', 'myltheme' ) .'</a>';
}

/**
 * Remove Commenting completely
 */
public function comments_clean_header_hook(){
    wp_deregister_script( 'comment-reply' );
}

/**
 * remove WP version from scripts
 */
public function remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}

/**
 * Remove links around images
 */
public function ks_remove_image_link( $content ) {
    $content =
        preg_replace(
            array('{<a(.*?)(wp-att|wp-content\/uploads)[^>]*><img}',
                '{ wp-image-[0-9]*" /></a>}'),
            array('<img','" />'),
            $content
        );
    return $content;
}
		
	}
endif;

return new cleanup_content();





?>