<?php
/**
 * The template for displaying search results pages.
 *
 * @package kaneism
 */

get_header(); ?>

<main id="main" class="site-main" role="main">

<?php if ( have_posts() ) : ?>
    <h2 class="sizes-XLG"><?php printf( esc_attr__( 'Search Results for: %s', 'kaneism' ), '<span>' . get_search_query() . '</span>' );?></h2>
    <?php
    get_template_part( 'loop' );
else :
    get_template_part( 'content', 'none' );
endif;
?>
</main><?php /* #main */ ?>

<?php
//do_action( 'kaneism_sidebar' );
get_footer();
