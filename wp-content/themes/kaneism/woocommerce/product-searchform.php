<?php
/**
 * The template for displaying product search form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/product-searchform.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form id="searchform" role="search" method="get" class="search-form cf" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <fieldset class="fieldset">
        <legend class="hide-text">What are you looking for today?</legend>
        <label class="hide-text" for="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>"><?php esc_html_e( 'Search for:', 'woocommerce' ); ?></label>
        <input type="search" id="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>" class="text-field search-field" placeholder="<?php echo esc_attr__( 'Place Your Bets&hellip;', 'woocommerce' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
        <button type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'woocommerce' ); ?>" class="search-submit ico i-m i--search"><?php echo esc_html_x( 'Search', 'submit button', 'woocommerce' ); ?></button>
        <input type="hidden" name="post_type" value="product" />
    </fieldset>
</form>
