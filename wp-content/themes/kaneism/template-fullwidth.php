<?php
/**
 * The template for displaying full width pages.
 *
 * Template Name: Full width
 *
 * @package kaneism
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			while ( have_posts() ) :
				the_post();

				do_action( 'kaneism_page_before' );

				get_template_part( 'content', 'page' );

				/**
				 * Functions hooked in to kaneism_page_after action
				 *
				 * @hooked kaneism_display_comments - 10
				 */
				//do_action( 'kaneism_page_after' );

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
