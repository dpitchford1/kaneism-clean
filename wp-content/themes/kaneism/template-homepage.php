<?php
/**
 * The template for displaying the homepage.
 *
 * This page template will display any functions hooked into the `homepage` action.
 * By default this includes a variety of product displays and the page content itself. To change the order or toggle these components
 * use the Homepage Control plugin.
 * https://wordpress.org/plugins/homepage-control/
 *
 * Template name: Homepage
 *
 * @package kaneism
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			/**
			 * Functions hooked in to homepage action
			 *
			 * @hooked kaneism_homepage_content      - 10
			 * @hooked kaneism_product_categories    - 20
			 * @hooked kaneism_recent_products       - 30
			 * @hooked kaneism_featured_products     - 40
			 * @hooked kaneism_popular_products      - 50
			 * @hooked kaneism_on_sale_products      - 60
			 * @hooked kaneism_best_selling_products - 70
			 */
			do_action( 'homepage' );
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
<?php
get_footer();
