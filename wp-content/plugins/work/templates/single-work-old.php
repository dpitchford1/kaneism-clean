<?php
/**
 * Template for displaying single work items
 *
 * @package Work
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * work_before_single_work_header hook
 * 
 * @hooked - Add any custom content before the header
 */
do_action('work_before_single_work_header');

// Use get_header without any modifications to ensure theme's header components are loaded
get_header();
?>

<main id="main" class="site-main" role="main">
    <?php 
    /**
     * work_before_single_work_content hook
     * 
     * @hooked - Add any custom content before the main work content
     */
    do_action('work_before_single_work_content');
    
    while (have_posts()) : the_post(); ?>

    <?php 
    /**
     * work_before_breadcrumb hook
     */
    do_action('work_before_breadcrumb');
    
    work_breadcrumb(); 
    
    /**
     * work_after_breadcrumb hook
     */
    do_action('work_after_breadcrumb');
    ?>
    
<div class="work-category-nav">
    <nav class="work-category-navigation" aria-label="Work Categories">
        <ul class="work-category-menu">
            <?php work_category_navigation(); ?>
        </ul>
    </nav>
</div>

    <article id="post-<?php the_ID(); ?>" <?php post_class('work-single'); ?> itemscope itemtype="http://schema.org/Article">
            <?php
            /**
             * work_before_entry_title hook
             */
            do_action('work_before_entry_title');
            ?>
            <h2 class="entry-title" itemprop="headline"><?php the_title(); ?></h2>
            <?php
            /**
             * work_after_entry_title hook
             */
            do_action('work_after_entry_title');
            ?>

        <div class="entry-content" itemprop="articleBody">
            <?php
            /**
             * work_before_gallery hook
             */
            do_action('work_before_gallery');
            
            // Display the gallery if available
            if (function_exists('work_display_gallery')) {
                echo apply_filters('work_gallery_output', work_display_gallery(get_the_ID()));
            } elseif (has_post_thumbnail()) {
            ?>
                <figure class="featured-image">
                    <?php the_post_thumbnail('large', array('itemprop' => 'image')); ?>
                </figure>
            <?php 
            }
            
            /**
             * work_after_gallery hook
             */
            do_action('work_after_gallery');
            ?>

            <div class="work-description">
                <?php 
                /**
                 * work_before_description hook
                 */
                do_action('work_before_description');
                
                if (function_exists('work_get_description')) {
                    echo apply_filters('work_description_output', work_get_description(get_the_ID()));
                } else {
                    the_content();
                }
                
                /**
                 * work_after_description hook
                 */
                do_action('work_after_description');
                ?>
            </div>

            <?php if (function_exists('work_get_project_details')) { ?>
                <div class="work-project-details">
                    <?php 
                    /**
                     * work_before_project_details hook
                     */
                    do_action('work_before_project_details');
                    
                    echo apply_filters('work_project_details_output', work_get_project_details(get_the_ID()));
                    
                    /**
                     * work_after_project_details hook
                     */
                    do_action('work_after_project_details');
                    ?>
                </div>
            <?php } ?>
        </div><!-- .entry-content -->

        <?php
        /**
         * work_after_entry_content hook
         * 
         * @hooked kane_post_nav - 10 (in theme)
         */
        do_action('work_after_entry_content');
        ?>
    </article>

    <?php endwhile; ?>

    <?php
    /**
     * work_after_single_work_content hook
     * 
     * @hooked - Add any custom content after the main work content
     */
    do_action('work_after_single_work_content');
    ?>
</main><!-- #main -->

<?php
/**
 * work_before_single_work_footer hook
 */
do_action('work_before_single_work_footer');

get_footer();

/**
 * work_after_single_work_footer hook
 */
do_action('work_after_single_work_footer'); 