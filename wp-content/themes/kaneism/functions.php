<?php
/**
 * Kaneism engine room
 *
 * @package kaneism
 */

/**
 * Assign the Kaneism version to a var
 */
$theme              = wp_get_theme( 'kaneism' );
$kaneism_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$kaneism = (object) array(
	'version'    => $kaneism_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-kaneism.php',
	//'customizer' => require 'inc/customizer/class-kaneism-customizer.php',
);

require 'inc/kaneism-functions/kaneism-functions.php';

// LOAD TEMPLATE DEVELOPMENT FUNCTIONS (not required but helper stuff for debugging and development)
require_once( 'inc/kaneism-functions/template.php' );

/**
 * Front end Functions
 */
require 'inc/kaneism-frontend/kaneism-bloat.php';
require 'inc/kaneism-frontend/kaneism-meta-description-functions.php';
require 'inc/kaneism-frontend/kaneism-social-meta-functions.php';
require 'inc/kaneism-frontend/kaneism-title-functions.php';

/**
 * Core Functions
 */
require 'inc/kaneism-functions/kaneism-template-hooks.php';
require 'inc/kaneism-functions/kaneism-template-functions.php';
require 'inc/wordpress-shims.php';
require 'inc/bits-metabox.php';

/**
 * WebP
 */
require 'inc/img-optimization/kaneism-webp-functions.php';
require 'inc/img-optimization/kaneism-webp-conversion.php';
require 'inc/admin/webp-test-admin.php';

if ( kaneism_is_woocommerce_activated() ) {

	require 'inc/woocommerce/class-kaneism-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/kaneism-woocommerce-template-hooks.php';
	require 'inc/woocommerce/kaneism-woocommerce-template-functions.php';
	require 'inc/woocommerce/kaneism-woocommerce-functions.php';
}

if ( is_admin() ) {
	$kaneism->admin = require 'inc/admin/class-kaneism-admin.php';

	require 'inc/admin/class-kaneism-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	// require 'inc/nux/class-kaneism-nux-admin.php';
	// require 'inc/nux/class-kaneism-nux-guided-tour.php';
	// require 'inc/nux/class-kaneism-nux-starter-content.php';
}

add_action('rest_api_init', function() {
    error_log('WordPress REST API initialization');
    register_rest_route('test/v1', '/ping', [
        'methods' => 'GET',
        'callback' => function() {
            return ['status' => 'ok'];
        },
        'permission_callback' => '__return_true'
    ]);
});

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */
