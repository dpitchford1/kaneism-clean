<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kaneism
 */

?>

<div class="no-results not-found">
	<h2 class="sizes-XLG">Nothing Found</h2>

    <?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

        <p>
            <?php
                /* translators: 1: URL */
                printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'kaneizm' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) );
            ?>
        </p>

    <?php elseif ( is_search() ) : ?>

        <p>Sorry, but nothing matched your search terms. Please try again with some different keywords.</p>
        <?php get_search_form(); ?>

    <?php else : ?>

        <p>It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.</p>
        <?php get_search_form(); ?>

    <?php endif; ?>

</div><!-- .no-results -->
