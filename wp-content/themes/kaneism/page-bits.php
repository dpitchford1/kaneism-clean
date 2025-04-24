<?php
/**
 * The template for displaying the Bits page.
 *
 * This template specifically displays the Bits photo page.
 *
 * @package kaneism
 */

get_header(); ?>

<main id="main" class="site-main" role="main">
    <?php //the_title( '<h2 class="entry-title">', '</h2>' ); ?>
    <h2 class="sizes-XLG">Kaneism Bits</h2>
    <?php
    while ( have_posts() ) :
        the_post();

        do_action( 'kaneism_page_before' );
        ?>
        <?php the_content(); ?>
        
        <?php /* Photo grid with srcset --> template-parts/content-photo-grid.php */ ?>
        <?php kaneism_display_photo_grid(); ?>
        
        <?php
        /**
         * Functions hooked in to kaneism_page_after action
         *
         * @hooked kaneism_display_comments - 10
         */
        do_action( 'kaneism_page_after' );

    endwhile; // End of the loop.
    ?>

</main><?php /* #main */ ?>

<?php
//do_action( 'kaneism_sidebar' );
get_footer();
