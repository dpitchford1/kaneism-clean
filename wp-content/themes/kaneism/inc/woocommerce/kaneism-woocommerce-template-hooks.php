<?php
/**
 * Kaneism WooCommerce hooks
 *
 * @package kaneism
 */

/**
 * Homepage
 *
 * @see  kaneism_product_categories()
 * @see  kaneism_recent_products()
 * @see  kaneism_featured_products()
 * @see  kaneism_popular_products()
 * @see  kaneism_on_sale_products()
 * @see  kaneism_best_selling_products()
 */
add_action( 'homepage', 'kaneism_product_categories', 20 );
add_action( 'homepage', 'kaneism_recent_products', 30 );
add_action( 'homepage', 'kaneism_featured_products', 40 );
add_action( 'homepage', 'kaneism_popular_products', 50 );
add_action( 'homepage', 'kaneism_on_sale_products', 60 );
add_action( 'homepage', 'kaneism_best_selling_products', 70 );

/**
 * Layout
 *
 * @see  kaneism_before_content()
 * @see  kaneism_after_content()
 * @see  woocommerce_breadcrumb()
 * @see  kaneism_shop_messages()
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_main_content', 'kaneism_before_content', 10 );
add_action( 'woocommerce_after_main_content', 'kaneism_after_content', 10 );
add_action( 'kaneism_content_top', 'kaneism_shop_messages', 15 );
add_action( 'kaneism_before_content', 'woocommerce_breadcrumb', 10 );

add_action( 'woocommerce_after_shop_loop', 'kaneism_sorting_wrapper', 9 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 30 );
add_action( 'woocommerce_after_shop_loop', 'kaneism_sorting_wrapper_close', 31 );

add_action( 'woocommerce_before_shop_loop', 'kaneism_sorting_wrapper', 9 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
add_action( 'woocommerce_before_shop_loop', 'kaneism_woocommerce_pagination', 30 );
add_action( 'woocommerce_before_shop_loop', 'kaneism_sorting_wrapper_close', 31 );

add_action( 'kaneism_footer', 'kaneism_handheld_footer_bar', 999 );

/**
 * Products
 *
 * @see kaneism_edit_post_link()
 * @see kaneism_upsell_display()
 * @see kaneism_single_product_pagination()
 * @see kaneism_sticky_single_add_to_cart()
 */
add_action( 'woocommerce_single_product_summary', 'kaneism_edit_post_link', 60 );

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'woocommerce_after_single_product_summary', 'kaneism_upsell_display', 15 );

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 6 );

add_action( 'woocommerce_after_single_product_summary', 'kaneism_single_product_pagination', 30 );
add_action( 'kaneism_after_footer', 'kaneism_sticky_single_add_to_cart', 999 );

/**
 * Header
 *
 * @see kaneism_product_search()
 * @see kaneism_header_cart()
 */
add_action( 'kaneism_header', 'kaneism_product_search', 40 );
add_action( 'kaneism_header', 'kaneism_header_cart', 60 );

/**
 * Cart fragment
 *
 * @see kaneism_cart_link_fragment()
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'kaneism_cart_link_fragment' );

/**
 * Integrations
 *
 * @see kaneism_woocommerce_brands_archive()
 * @see kaneism_woocommerce_brands_single()
 * @see kaneism_woocommerce_brands_homepage_section()
 */
if ( class_exists( 'WC_Brands' ) ) {
	add_action( 'woocommerce_archive_description', 'kaneism_woocommerce_brands_archive', 5 );
	add_action( 'woocommerce_single_product_summary', 'kaneism_woocommerce_brands_single', 4 );
	add_action( 'homepage', 'kaneism_woocommerce_brands_homepage_section', 80 );
}
