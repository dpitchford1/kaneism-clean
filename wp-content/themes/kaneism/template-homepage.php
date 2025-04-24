<?php
/**
 * The template for displaying the homepage.
 *
 * This page template will display any functions hooked into the `homepage` action.
 * By default this includes a variety of product displays and the page content itself. To change the order or toggle these components
 * use the Homepage Control plugin.
 * https://wordpress.org/plugins/homepage-control/
 *
 * Template name: Homepage
 *
 * @package kaneism
 */

get_header(); ?>

<main id="main-content" class="site-main" role="main">
    <h2 class="sizes-XLG">The Goods</h2>
<?php
// Display the page content
while ( have_posts() ) :
    the_post();
    ?>
    <div class="entry-content"><?php the_content(); ?></div>
    <?php
endwhile;
?>

    <?php /* Component 1: Latest Shop Items */ ?>
    <?php if (class_exists('WooCommerce')) : ?>
    <section class="homepage-section latest-shop">
        <h3 class="sizes-LG"><?php echo esc_html__('Latest from the Shop', 'kaneism'); ?></h3>
        <div class="products-grid">
            <?php
            // Get latest products
            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => 4,
                'orderby'        => 'date',
                'order'          => 'DESC',
            );
            
            $latest_products = new WP_Query($args);
            
            if ($latest_products->have_posts()) {
                if (function_exists('woocommerce_product_loop_start')) {
                    woocommerce_product_loop_start();
                }
                
                while ($latest_products->have_posts()) : 
                    $latest_products->the_post();
                    if (function_exists('wc_get_template_part')) {
                        wc_get_template_part('content', 'product');
                    }
                endwhile;
                
                if (function_exists('woocommerce_product_loop_end')) {
                    woocommerce_product_loop_end();
                }
                wp_reset_postdata();
            } else {
                echo '<p>' . esc_html__('No products found', 'kaneism') . '</p>';
            }
            ?>
        </div>
        <div class="view-all">
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="button"><?php echo esc_html__('View All Products', 'kaneism'); ?></a>
        </div>
    </section>
    <?php endif; ?>

    <?php /* Component 2: Latest Projects */ ?>
    <section class="homepage-section latest-projects">
        <h3 class="sizes-LG"><?php echo esc_html__('Latest Projects', 'kaneism'); ?></h3>
        <div class="grid-general grid--3col">
            <?php
            // Check if the work post type exists
            if (post_type_exists('work')) {
                // Get latest work projects
                $args = array(
                    'post_type'      => 'work',
                    'posts_per_page' => 3,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                );
                
                $latest_works = new WP_Query($args);
                
                if ($latest_works->have_posts()) {
                    while ($latest_works->have_posts()) : 
                        $latest_works->the_post();
                        ?>
                        <article class="kane-work-item">
                            <a href="<?php the_permalink(); ?>" class="feature-img" tabindex="-1" aria-hidden="true">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('kaneism-img-sm'); ?> 
                                <?php endif; ?>
                            </a>
                            <h4 class="sizes-L"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                            <?php if (has_excerpt()) : ?>
                                <div><?php the_excerpt(); ?></div>
                            <?php endif; ?>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                } else {
                    echo '<p>' . esc_html__('No projects found', 'kaneism') . '</p>';
                }
            } else {
                echo '<p>' . esc_html__('Work post type is not available', 'kaneism') . '</p>';
            }
            ?>
        </div>
        <div class="view-all">
            <a href="<?php echo esc_url(get_post_type_archive_link('work')); ?>" class="button"><?php echo esc_html__('View All Projects', 'kaneism'); ?></a>
        </div>
    </section>

    <?php /* Component 3: Featured Projects */ ?>
    <section class="homepage-section featured-projects">
        <h3 class="sizes-LG"><?php echo esc_html__('Featured Projects', 'kaneism'); ?></h3>
        <div class="grid-general grid--3col">
            <?php
            // Check if the work post type exists and if the required function exists
            if (post_type_exists('work')) {
                $featured_query_args = array(
                    'post_type'      => 'work',
                    'posts_per_page' => 3,
                    'meta_query'     => array(
                        array(
                            'key'     => '_work_is_featured',
                            'value'   => '1',
                            'compare' => '=',
                        ),
                    ),
                );
                
                $featured_works = new WP_Query($featured_query_args);
                
                if ($featured_works->have_posts()) {
                    while ($featured_works->have_posts()) : 
                        $featured_works->the_post();
                        ?>
                        <article class="kane-work-item feature is--featured" >
                            <a href="<?php the_permalink(); ?>" class="feature-img img--isFeatured" tabindex="-1" aria-hidden="true">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('kaneism-img-sm'); ?>
                                <?php endif; ?>
                            </a>
                            <h4 class="sizes-L"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                            <?php if (has_excerpt()) : ?>
                                <div><?php the_excerpt(); ?></div>
                            <?php endif; ?>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                } else {
                    echo '<p>' . esc_html__('No featured projects found', 'kaneism') . '</p>';
                }
            } else {
                echo '<p>' . esc_html__('Work post type is not available', 'kaneism') . '</p>';
            }
            ?>
        </div>
    </section>

</main><?php /* #main */ ?>
<?php
get_footer();
