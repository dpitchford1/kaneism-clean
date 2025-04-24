<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package kaneism
 */

get_header(); ?>

	<div id="primary" class="content-area">

		<main id="main" class="site-main" role="main">

			<div class="error-404 not-found">

				<div class="page-content">

					<h2 class="page-title">Oops! That page can&rsquo;t be found.</h2>
					<p>Nothing was found at this location. Try searching, or check out the links below.</p>

					<?php
					echo '<section aria-label="' . esc_html__( 'Search', 'kaneism' ) . '">';

					if ( kaneism_is_woocommerce_activated() ) {
						the_widget( 'WC_Widget_Product_Search' );
					} else {
						get_search_form();
					}

					echo '</section>';

					if ( kaneism_is_woocommerce_activated() ) {

						echo '<div class="fourohfour-columns-2">';

							echo '<section class="col-1" aria-label="' . esc_html__( 'Promoted Products', 'kaneism' ) . '">';

								kaneism_promoted_products();

							echo '</section>';

							echo '<nav class="col-2" aria-label="' . esc_html__( 'Product Categories', 'kaneism' ) . '">';

								echo '<h2>' . esc_html__( 'Product Categories', 'kaneism' ) . '</h2>';

								the_widget(
									'WC_Widget_Product_Categories',
									array(
										'count' => 1,
									)
								);

							echo '</nav>';

						echo '</div>';

						echo '<section aria-label="' . esc_html__( 'Popular Products', 'kaneism' ) . '">';

							echo '<h2>' . esc_html__( 'Popular Products', 'kaneism' ) . '</h2>';

							$shortcode_content = kaneism_do_shortcode(
								'best_selling_products',
								array(
									'per_page' => 4,
									'columns'  => 4,
								)
							);

							echo $shortcode_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						echo '</section>';
					}
					?>

				</div><!-- .page-content -->
			</div><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
