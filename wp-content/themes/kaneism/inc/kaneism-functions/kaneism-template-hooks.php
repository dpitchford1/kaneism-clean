<?php
/**
 * Kaneism hooks
 *
 * @package kaneism
 */

/**
 * General
 *
 * @see  kaneism_header_widget_region()
 * @see  kaneism_get_sidebar()
 */
//add_action( 'kaneism_before_content', 'kaneism_header_widget_region', 10 );
add_action( 'kaneism_sidebar', 'kaneism_get_sidebar', 10 );

/**
 * Header
 *
 * @see  kaneism_secondary_navigation()
 * @see  kaneism_primary_navigation()
 */
add_action( 'kaneism_header', 'kaneism_header_container', 0 );
add_action( 'kaneism_header', 'kaneism_secondary_navigation', 30 );
add_action( 'kaneism_header', 'kaneism_header_container_close', 41 );
add_action( 'kaneism_header', 'kaneism_primary_navigation_wrapper', 42 );
add_action( 'kaneism_header', 'kaneism_primary_navigation', 50 );
add_action( 'kaneism_header', 'kaneism_primary_navigation_wrapper_close', 68 );

/**
 * Footer
 *
 * @see  kaneism_footer_widgets()
 */
add_action( 'kaneism_footer', 'kaneism_footer_widgets', 10 );

/**
 * Homepage
 *
 * @see  kaneism_homepage_content()
 */
add_action( 'homepage', 'kaneism_homepage_content', 10 );

/**
 * Posts
 *
 * @see  kaneism_post_header()
 * @see  kaneism_post_meta()
 * @see  kaneism_post_content()
 * @see  kaneism_single_post_header()
 */
add_action( 'kaneism_loop_post', 'kaneism_post_header', 10 );
add_action( 'kaneism_loop_post', 'kaneism_post_content', 30 );
add_action( 'kaneism_loop_post', 'kaneism_post_taxonomy', 40 );
add_action( 'kaneism_single_post', 'kaneism_post_header', 10 );
add_action( 'kaneism_single_post', 'kaneism_post_content', 30 );
add_action( 'kaneism_single_post_bottom', 'kaneism_post_taxonomy', 5 );
add_action( 'kaneism_post_header_before', 'kaneism_post_meta', 10 );
add_action( 'kaneism_post_content_before', 'kaneism_post_thumbnail', 10 );

/**
 * Pages
 *
 * @see  kaneism_page_header()
 * @see  kaneism_page_content()
 */
add_action( 'kaneism_page', 'kaneism_page_header', 10 );
add_action( 'kaneism_page', 'kaneism_page_content', 20 );

/**
 * Homepage Page Template
 *
 * @see  kaneism_page_content()
 */
add_action( 'kaneism_homepage', 'kaneism_page_content', 20 );
