<?php
/**
 * The template for displaying the Bits page.
 *
 * This template specifically displays the Bits photo page.
 *
 * @package kaneism
 */

get_header(); ?>

<main id="main" class="site-main bits-page" role="main">
    <?php // the_title( '<h2 class="sizes-LG">', '</h2>' ); ?>
    <h2 class="sizes-XLG">Kaneism Doodads</h2>
    <?php
    while ( have_posts() ) :
        the_post();

        do_action( 'kaneism_page_before' );
        ?>
        <?php the_content(); ?>
        
        <section id="post-<?php the_ID(); ?>"  class="bits-photo-grid">
            <!-- <h3 class="hide-text">Doodads</h3> -->
        <?php
        // Get the bits gallery images
        $gallery_images = get_post_meta(get_the_ID(), '_kaneism_bits_images', true);
        
        if (!empty($gallery_images)) : ?>
            <article>
                <?php foreach ($gallery_images as $image_id) :
                    $full_image_url = wp_get_attachment_image_url($image_id, 'full');
                    $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                    if (empty($image_alt)) {
                        $image_alt = get_the_title();
                    }
                    ?>
                    <figure class="bits-photo-item">
                        
                    <?php echo wp_get_attachment_image($image_id, 'large', false, array('class' => 'bits-photo', 'alt' => $image_alt)); ?>
                        
                    </figure>
                <?php endforeach; ?>
            </article>
        <?php endif; ?>
            
        </section><!-- #post-## -->
        
        <?php
        /**
         * Functions hooked in to kaneism_page_after action
         *
         * @hooked kaneism_display_comments - 10
         */
        do_action( 'kaneism_page_after' );

    endwhile; // End of the loop.
    ?>

</main><!-- #main -->

<?php
//do_action( 'kaneism_sidebar' );
get_footer();
