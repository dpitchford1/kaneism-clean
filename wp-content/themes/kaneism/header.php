<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package kaneism
 */

?><!doctype html>
<html class="no-js" dir="ltr" <?php language_attributes(); ?> data-off-canvas="" id="site-body">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php /* Mobile */ ?>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="/assets/css/dev/storefront-styles.min.css" media="screen">
<link rel="stylesheet" href="/assets/css/dev/bits.min.css" media="screen">

<link rel="manifest" href="/kaneism.json">
<?php /* favicon */ ?>
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/assets/img/icon/safari-pinned-tab.svg" type="image/svg+xml">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/img/icon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/assets/img/icon/favicon-16x16.png">
<link rel="mask-icon" href="/assets/img/icon/safari-pinned-tab.svg" color="#12034a">
<?php /* Theme */ ?>
<link rel="apple-touch-icon" href="/assets/img/icon/apple-touch-icon.png">
<link rel="apple-touch-icon" sizes="180x180" href="/assets/img/icon/apple-touch-icon.png">

<?php /* APPLE SPECIFIC */ ?>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="Kaneism">

<?php wp_head(); ?>

<?php /* COPYRIGHTS */ ?>
<meta name="author" content="Kane">
<meta name="copyright" content="© Kaneism designs. All right reserved. <?php echo date('Y'); ?>">
<?php /* SEARCH AND SEO */ ?>
<meta name="googlebot" content="NOODP">
<?php if ( is_front_page() ) : ?><link rel="home" title="Home page" href="/"><?php endif ?>

</head>

<body <?php body_class(); ?> data-off-screen="hidden" id="page-body" data-theme="dark">
<?php wp_body_open(); ?>
<a href="#global-header" id="exit-off-canvas" class="exit-offcanvas" aria-controls="global-header"><span class="hide-text">Hide Menu</span></a>
<?php /* accessibility nav */ ?>
<a class="quick-links" href="#main-content">Skip to Main Content</a>
<a class="quick-links" href="#global-footer">Skip to Footer</a> 

<?php /* small screen header bar */ ?>
<div class="region is--fixed global-header--ss" id="global-header--ss"><span class="hide-text">Kane</span></div>
<?php if ( wp_is_mobile() ) : ?>
    <?php if (sizeof( WC()->cart->get_cart() ) > 0 ) : ?>
<p class="cart--bubble bubble--ss"><a class="bubble--contents <?php if (is_page('cart')) { echo 'is--selected'; } ?>" href="/cart/">Cart <span class="bubble--count"><?php echo sprintf ( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?></span></a></p>
<?php endif; ?>
<?php endif; ?>

<?php do_action( 'kaneism_before_site' ); ?>

<div id="page" class="hfeed site">
	<?php do_action( 'kaneism_before_header' ); ?>

	<header id="masthead" class="site-header" role="banner">

		<?php
		/**
		 * Functions hooked into kaneism_header action
		 *
		 * @hooked kaneism_header_container                 - 0
		 * @hooked kaneism_skip_links                       - 5
		 * @hooked kaneism_social_icons                     - 10
		 * @hooked kaneism_site_branding                    - 20
		 * @hooked kaneism_secondary_navigation             - 30
		 * @hooked kaneism_product_search                   - 40
		 * @hooked kaneism_header_container_close           - 41
		 * @hooked kaneism_primary_navigation_wrapper       - 42
		 * @hooked kaneism_primary_navigation               - 50
		 * @hooked kaneism_header_cart                      - 60
		 * @hooked kaneism_primary_navigation_wrapper_close - 68
		 */
		do_action( 'kaneism_header' );
		?>

	</header><!-- #masthead -->

	<?php
	/**
	 * Functions hooked in to kaneism_before_content
	 *
	 * @hooked kaneism_header_widget_region - 10
	 * @hooked woocommerce_breadcrumb - 10
	 */
	do_action( 'kaneism_before_content' );
	?>

	<div id="content" class="site-content" tabindex="-1">
		<div class="col-full">

		<?php
		do_action( 'kaneism_content_top' );
