<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kaneism
 */

get_header(); ?>

<main id="main-content" class="site-main" role="main">

<?php if ( have_posts() ) : ?>
    <?php
        the_archive_title( '<h2 class="sizes-XLG">', '</h2>' );
        the_archive_description( '<div class="taxonomy-description">', '</div>' );
    ?>
    <?php
    get_template_part( 'loop' );
else :
    get_template_part( 'content', 'none' );
endif;
?>

</main><?php /* #main */ ?>

<?php
do_action( 'kaneism_sidebar' );
get_footer();
