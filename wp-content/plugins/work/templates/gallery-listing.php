<?php
/**
 * Template part for displaying photo grid content
 *
 * @package Kaneism
 */

?>


<section class="region">
    <h3 class="hide-text">Listing of Canvas Works</h3>
    <div class="grid-general grid--2col tight--grid">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('work-gallery-item'); ?>>
            <h3 class="hide-text"><?php the_title(); ?></h3>
            <?php
            $gallery_images = function_exists('work_get_gallery_images') ? work_get_gallery_images(get_the_ID()) : array();
            if (!empty($gallery_images)) : ?>

            <div class="swiper">
                <ul class="swiper-wrapper">
                    <?php foreach ($gallery_images as $index => $image_id) : ?>
                    <li class="swiper-slide">
                        <?php
                        echo wp_get_attachment_image(
                            $image_id,
                            'full',
                            false,
                            array(
                                'class' => 'wp-post-image',
                                'loading' => ($index === 0) ? 'eager' : 'lazy'
                            )
                        );
                        ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper--overlay">
                    <strong><?php the_title(); ?></strong> <?php the_excerpt(); ?>
                </div>
            </div>
                
            <?php elseif (has_post_thumbnail()) : ?>
                <div class="work-featured-image">
                    <?php the_post_thumbnail('full'); ?>
                </div>
            <?php endif; ?>
            <?php if (has_excerpt()) : ?>
                <?php the_excerpt(); ?>
            <?php endif; ?>
        </article>
    <?php endwhile; endif; ?>
    </div>
</section>

<?php get_footer(); ?>