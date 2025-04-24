<?php
/**
 * The template for displaying Work archives
 *
 * @package Work
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * work_before_archive_header hook
 * 
 * @hooked - Add any custom content before the header
 */
do_action('work_before_archive_header');

get_header();
?>

<?php if (function_exists('work_breadcrumb')) : ?>
    <?php work_breadcrumb(); ?>
<?php endif; ?>

<?php if (function_exists('work_category_navigation')) : ?>
    <?php work_category_navigation(); ?>
<?php endif; ?>
       

<main id="main-content" class="site-main" role="main">
    <?php
    /**
     * work_before_archive_content hook
     * 
     * @hooked - Add any custom content before the main archive content
     */
    do_action('work_before_archive_content');
    ?>
        <?php
        /**
         * work_before_archive_title hook
         */
        do_action('work_before_archive_title');
        ?>
        <h2 class="sizes-XLG w-ul"><?php echo esc_html__('Work', 'work'); ?> </h2>
        <?php
        /**
         * work_after_archive_title hook
         */
        do_action('work_after_archive_title');

        ?>

    <?php if (have_posts()) : ?>
        <section class="list-of-features" id="murals">
            <h3 class="hide-text">Listing of Projects</h3>
            <?php
            /**
             * work_before_loop hook
             */
            do_action('work_before_loop');

            if (have_posts()) :
                // Save the original query
                global $wp_query;
                $original_query = $wp_query;
                $featured_posts = array();
                $regular_posts = array();
                
                // Separate featured and regular posts
                while (have_posts()) : the_post();
                    $is_featured = work_is_featured(get_the_ID());
                    if ($is_featured) {
                        $featured_posts[] = get_the_ID();
                    } else {
                        $regular_posts[] = get_the_ID();
                    }
                endwhile;
                
                // Reset the post data
                wp_reset_postdata();
            ?>
            <article role="article" class="feature is--mainFeature" itemscope itemtype="http://schema.org/Article">
                <?php
                // Query just the first featured post
                $featured_query = new WP_Query(array(
                    'post_type' => 'work',
                    'p' => $featured_posts[0],
                    'posts_per_page' => 1
                ));
                
                if ($featured_query->have_posts()) :  
                    $featured_query->the_post();
                ?>
                <?php if (has_post_thumbnail()) : ?>
                    <a class="feature-img img--isMainFeature" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                        <?php 
                        // Use wp_get_attachment_image instead of the_post_thumbnail to control loading attribute
                        echo wp_get_attachment_image(
                            get_post_thumbnail_id(),
                            'kaneism-img-m',
                            false,
                            array(
                                'loading' => 'eager', // Main feature should load eagerly
                                //'class' => 'wp-post-image'
                            )
                        );
                        ?>
                    </a>
                <?php endif; ?>
                
                <div class="feature-body">
                    <h3 class="feature-label">featured:</h3>
                    <h4 class="sizes-LG" itemprop="headline"><a href="<?php the_permalink(); ?>" itemprop="url"><?php the_title(); ?></a></h4>
                    <?php 
                    if (has_excerpt()) {
                        the_excerpt();
                    } else {
                        echo '<p class="summary" itemprop="description">' . wp_trim_words(get_the_content(), 30, '...') . '</p>';
                    }
                    ?>
                    <a href="<?php the_permalink(); ?>" class="work-featured-item-link">View Project</a>
                </div>
                <meta itemprop="datePublished" content="<?php echo get_the_date('Y-m-d'); ?>">
                <span itemprop="author" itemscope itemtype="http://schema.org/Person"><meta itemprop="name" content="Kaneism"></span>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>
            </article>
            <?php endif; ?>
            
            <div class="work-grid">
                <?php
                // Get all posts, exclude the featured post
                $args = array(
                    'post_type' => 'work',
                    'posts_per_page' => -1,
                );
                
                // Exclude the featured post if we have one
                if (!empty($featured_posts)) {
                    $args['post__not_in'] = $featured_posts;
                }
                
                $work_query = new WP_Query($args);
                
                if ($work_query->have_posts()) :
                    while ($work_query->have_posts()) : $work_query->the_post();
                ?>
                    <article role="article" class="feature is--promo" itemscope itemtype="http://schema.org/Article">
                        <a href="<?php the_permalink(); ?>" class="feature-img img--isPromo" tabindex="-1" aria-hidden="true">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php 
                                // Use wp_get_attachment_image instead of the_post_thumbnail for lazy loading
                                echo wp_get_attachment_image(
                                    get_post_thumbnail_id(),
                                    'kaneism-img-sm',
                                    false,
                                    array(
                                        'loading' => 'lazy', // Lazy load non-featured images
                                        //'class' => 'wp-post-image'
                                    )
                                );
                                ?>
                            <?php endif; ?>
                        </a>
                        <h3 class="sizes-L" itemprop="headline"><a href="<?php the_permalink(); ?>" itemprop="url"><?php the_title(); ?></a></h3>
                        <?php if (has_excerpt()) : ?>
                            <div itemprop="description"><?php the_excerpt(); ?></div>
                        <?php endif; ?>
                        <meta itemprop="datePublished" content="<?php echo get_the_date('Y-m-d'); ?>">
                        <span itemprop="author" itemscope itemtype="http://schema.org/Person"><meta itemprop="name" content="Kaneism"></span>
                    </article>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            
            <div class="work-pagination">
                <?php
                // Restore the original query for pagination
                $wp_query = $original_query;
                
                the_posts_pagination(array(
                    'prev_text' => __('Previous', 'work'),
                    'next_text' => __('Next', 'work'),
                    'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'work') . ' </span>',
                ));
                ?>
            </div>
        </section>
    <?php else : ?>
        <p class="work-no-results">No work items found.</p>
    <?php endif; ?>

    <?php
    /**
     * work_after_archive_content hook
     * 
     * @hooked - Add any custom content after the main archive content
     */
    do_action('work_after_archive_content');
    ?>
</main>

<?php
/**
 * work_before_archive_footer hook
 */
do_action('work_before_archive_footer');

get_footer();

/**
 * work_after_archive_footer hook
 */
do_action('work_after_archive_footer'); 