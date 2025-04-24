<?php
/**
 * The template for displaying search results pages.
 *
 * @package kaneism
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

				<h2 class="page-title">
					<?php
						/* translators: %s: search term */
						printf( esc_attr__( 'Search Results for: %s', 'kaneism' ), '<span>' . get_search_query() . '</span>' );
					?>
				</h2>

			<?php
			get_template_part( 'loop' );

		else :

			get_template_part( 'content', 'none' );

		endif;
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action( 'kaneism_sidebar' );
get_footer();
