<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package kaneism
 */

get_header(); ?>

<main id="main-content" class="site-main" role="main">

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
        do_action( 'kaneism_page_after' );
    endwhile; // End of the loop.
    ?>

</main><?php /* #main */ ?>

<?php
do_action( 'kaneism_sidebar' );
get_footer();
