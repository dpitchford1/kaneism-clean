<?php
/**
 * Kaneism hooks
 *
 * @package kaneism
 */

/**
 * Posts
 *
 * @see  kaneism_post_header()
 * @see  kaneism_post_meta()
 * @see  kaneism_post_content()
 * @see  kaneism_paging_nav()
 * @see  kaneism_single_post_header()
 * @see  kaneism_post_nav()
 */
add_action( 'kaneism_loop_post', 'kaneism_post_header', 10 );
add_action( 'kaneism_loop_post', 'kaneism_post_content', 30 );
add_action( 'kaneism_loop_post', 'kaneism_post_taxonomy', 40 );
add_action( 'kaneism_loop_after', 'kaneism_paging_nav', 10 );
add_action( 'kaneism_single_post', 'kaneism_post_header', 10 );
add_action( 'kaneism_single_post', 'kaneism_post_content', 30 );
add_action( 'kaneism_single_post_bottom', 'kaneism_post_taxonomy', 5 );
add_action( 'kaneism_single_post_bottom', 'kaneism_post_nav', 10 );
add_action( 'kaneism_post_header_before', 'kaneism_post_meta', 10 );
add_action( 'kaneism_post_content_before', 'kaneism_post_thumbnail', 10 );

/**
 * Pages
 *
 * @see  kaneism_page_header()
 * @see  kaneism_page_content()
 */
//add_action( 'kaneism_page', 'kaneism_page_header', 10 );
add_action( 'kaneism_page', 'kaneism_page_content', 20 );

/**
 * Homepage Page Template
 *
 * @see  kaneism_homepage_header()
 * @see  kaneism_page_content()
 */
add_action( 'kaneism_homepage', 'kaneism_homepage_header', 10 );
add_action( 'kaneism_homepage', 'kaneism_page_content', 20 );
