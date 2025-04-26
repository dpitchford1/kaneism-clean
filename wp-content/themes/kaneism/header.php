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
<link rel="manifest" href="/kaneism.json">
<?php /* service worker 
<script>
if (navigator && navigator.serviceWorker) { navigator.serviceWorker.register('/workerfont.js'); }
</script>*/ ?>
<script>var doc = window.document; doc.documentElement.className = document.documentElement.className.replace(/\bno-js\b/g, '') + 'has-js enhanced';</script>
<!-- <link rel="preload" as="image" href="/assets/img/bg/splat-corner.webp"> -->
<link rel="preload" href="/assets/fonts/copernicus-book.woff2" as="font" type="font/woff2" crossorigin>

<script src="/assets/js/core/themer.min.js" async></script>

<?php /* css injector */ ?>
<?php
    // Load critical CSS with transient caching
    $css_transient_key = 'kaneism_critical_css';
    $css_file = ABSPATH . 'assets/css/build/kaneism-inline-head' . ((defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min') . '.css';
    $css_content = get_transient($css_transient_key);
    $css_file_mtime = file_exists($css_file) ? filemtime($css_file) : 0;
    
    // Check if transient exists and if CSS file has been modified
    if (false === $css_content || !isset($css_content['mtime']) || $css_content['mtime'] !== $css_file_mtime) {
        if (file_exists($css_file)) {
            $css_data = array(
                'content' => file_get_contents($css_file), // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
                'mtime' => $css_file_mtime
            );
            set_transient($css_transient_key, $css_data, WEEK_IN_SECONDS);
            $css_output = $css_data['content'];
        } else {
            $css_output = '/* CSS file not found */';
        }
    } else {
        $css_output = $css_content['content'];
    }
    
    echo '<style id="critical-injector">' . $css_output . '</style>';
?>

<?php /* css files */ ?>
<link rel="stylesheet" href="/assets/css/build/kaneism-base-layout.min.css" media="screen">
<link rel="stylesheet" href="/assets/css/build/01-theme-clean.min.css" media="screen">
<link rel="stylesheet" href="/assets/css/build/kaneism-global-layout.min.css" as="style" onload="this.rel='stylesheet'">

<?php if ( is_singular('work') || is_tax('work_category') ) : ?>
<link rel="stylesheet" href="/assets/css/build/swiper.min.css" media="screen">
<?php endif; ?>
<noscript><link rel="stylesheet" href="/assets/css/dev/kaneism-global-layout.min.css" media="screen"></noscript>

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

<?php /* COPYRIGHTS */ ?>
<meta name="author" content="Kane">
<meta name="copyright" content="© Kaneism designs. All right reserved. <?php echo date('Y'); ?>">
<?php /* SEARCH AND SEO */ ?>
<meta name="googlebot" content="NOODP">
<?php if ( is_front_page() ) : ?><link rel="home" title="Home page" href="/"><?php endif ?>

<?php wp_head(); ?>
</head>

<body <?php body_class('light'); ?> data-off-screen="hidden" id="page-body" data-theme="dark">
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

<?php /* Header Start */ ?>
<div class="region is--fixed global-header" data-nav-slide="slide" id="global-header">
	<header class="brand-header fluid ov cf" role="banner">
		<?php if ( is_front_page() ) : ?>
			<h1 class="brand brand-fs" id="logo" itemscope itemtype="http://schema.org/Organization"><span class="is--logo">Kaneism Design</span></h1>
		<?php else : ?>
			<h1 class="brand brand-fs" id="logo" itemscope itemtype="http://schema.org/Organization"><a class="is--logo" href="/" rel="home">Kaneism Design</a></h1>
		<?php endif ?>
		<?php /* Theme Switcher */ ?>
		<div class="theme-switcher">
			<div class="theme-toggle theme-toggle--small">
				<input class="theme-checkbox" id="b" type="checkbox">
				<label class="theme-label" for="b">
					<p class="theme-toggle--label">Dark Mode</p>
					<span class="theme-toggle--switch" data-checked="On" data-unchecked="Off"></span>
				</label>
			</div>
        </div>
        <?php /* Utility Nav */ ?>
	    <nav class="menu-utilities cf" role="navigation" itemscope itemtype="http://www.schema.org/SiteNavigationElement">
	        <p class="hide-text">Submenu:</p>
		    <?php 
				wp_nav_menu( 
					array(
						'theme_location'  => 'utility',
						'menu_class' => 'utility-menu',
						'menu_id' => 'utility-menu',
						'container' => false
					)
				);
            ?>
	    </nav>
        <?php /* Site Search */ ?>
        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<?php get_product_search_form(); ?>
        <?php else : ?>
        <?php get_search_form(); ?>
        <?php endif ?>
    </header>
    <?php /* Global Menus */ ?>
    <div class="menu-global">
		<div class="fluid cf" role="navigation" itemscope itemtype="http://www.schema.org/SiteNavigationElement">
		    <h2 class="hide-text">Main Menu</h2>
		    <div class="menu-logo"></div>
		    <?php 
				wp_nav_menu( 
					array(
						'theme_location'  => 'primary',
						'menu_class' => 'navigation-global',
						'menu_id' => 'primary-menu',
						'container' => 'ul'
					)
				);
			?>
            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
		    <h3 class="hide-text">Your Cart</h3>
            <p class="cart--bubble"><a class="bubble--contents <?php if (is_page('cart')) { echo 'is--selected'; } ?>" href="/cart/">Cart <span class="bubble--count"><?php echo sprintf ( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?></span>
			</a></p>
            <div class="header--cart">
                <a class="cart--content <?php if (is_page('cart')) { echo 'is--selected'; } ?>" href="<?php echo wc_get_cart_url(); ?>" title="View your shopping cart"><span class="cart--label">Cart:</span> <span class="count"><?php echo sprintf ( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?> – <?php echo WC()->cart->get_cart_total(); ?></span></a>
            </div>
            <?php endif ?>
		</div>
	</div>
</div>
<?php /* Header End */ ?>
<hr class="hide-divider">
<div id="page" class="hfeed fluid inner-content">

<?php do_action( 'kaneism_before_site' ); ?>

<!-- <div id="page" class="hfeed site"> -->
	<?php do_action( 'kaneism_before_header' ); ?>

	<!-- <header id="masthead" class="site-header" role="banner"> -->

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
		//do_action( 'kaneism_header' );
		?>

	<!-- </header> -->
    <!-- #masthead -->

	<?php
	/**
	 * Functions hooked in to kaneism_before_content
	 *
	 * @hooked kaneism_header_widget_region - 10
	 * @hooked woocommerce_breadcrumb - 10
	 */
	do_action( 'kaneism_before_content' );
	?>

	<!-- <div id="content" class="site-content" tabindex="-1">
		<div class="col-full"> -->

		<?php
		do_action( 'kaneism_content_top' );
