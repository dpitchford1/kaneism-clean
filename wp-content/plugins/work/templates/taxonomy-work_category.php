<?php
/**
 * Template for displaying Work category archives
 *
 * @package Work
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Get the current term object
$term = get_queried_object();

/**
 * work_before_category_header hook
 * 
 * @hooked - Add any custom content before the header
 */
do_action('work_before_category_header');

get_header();
?>

<?php
// if ($term && $term->slug === 'canvases') {
//     include plugin_dir_path(__FILE__) . 'gallery-listing.php';
// } elseif ($term && $term->slug === 'designs') {
//     include plugin_dir_path(__FILE__) . 'design-listing.php';
// } else {
    // Default markup for all other categories (no need to echo, just write HTML/PHP as usual)
    ?>
    <!-- <section class="list-of-features" id="murals"> -->
        <!-- ...existing default content... -->
    <!-- </section> -->
    <?php
//}
?>

<!-- if ($term && $term->slug === 'canvases') {
    get_template_part('path/to/gallery-listing');
} else {
    // ...default content...
} -->

<main id="main-content" class="site-main" role="main">
<?php if (function_exists('work_breadcrumb')) : ?>
    <?php work_breadcrumb(); ?>
<?php endif; ?>

<?php if (function_exists('work_category_navigation')) : ?>
    <?php work_category_navigation(); ?>
<?php endif; ?>

	<?php
		echo '<h2 class="sizes-XLG w-ul">' . esc_html($term->name) . '</h2>';
		if (!empty($term->description)) {
			echo '<p class="work-archive-description hide-text">' . wp_kses_post($term->description) . '</p>';
		}
	?>
    <?php
    // Conditional template loading for specific categories
    if ($term && $term->slug === 'canvases') {
        include plugin_dir_path(__FILE__) . 'gallery-listing.php';
        return;
    } elseif ($term && $term->slug === 'designs') {
        include plugin_dir_path(__FILE__) . 'design-listing.php';
        return;
    } else {
        // Default content for other categories
        echo '<p class="work-archive-description hide-text">' . esc_html__('Browse our projects below.', 'work') . '</p>';
    }
    ?>
	<section class="list-of-features" id="murals">
		<h3 class="hide-text">Browse Projects</h3>
		<?php if (have_posts()) : ?>
			<?php
			// Save the original query
			global $wp_query;
			$original_query = $wp_query;
			$featured_posts = array();
			$regular_posts = array();
			
			// Separate featured and regular posts
			while (have_posts()) : the_post();
				$is_featured = function_exists('work_is_featured') ? work_is_featured() : get_post_meta(get_the_ID(), '_work_is_featured', true);
				if ($is_featured) {
					$featured_posts[] = get_the_ID();
				} else {
					$regular_posts[] = get_the_ID();
				}
			endwhile;
			
			// Reset the post data
			wp_reset_postdata();
			
			// Display featured posts if any
			if (!empty($featured_posts)) :
			?>
		<article role="article" class="feature is--mainFeature">
			<?php
			// Query just the first featured post
			$featured_query = new WP_Query(array(
				'post_type' => 'work',
				'p' => $featured_posts[0], // Only get the first featured post
				'posts_per_page' => 1,
				'no_found_rows' => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => true
			));
			
			if ($featured_query->have_posts()) : 
				$featured_query->the_post();
			?>

		<?php if (has_post_thumbnail()) : ?>
			<a class="feature-img img--isMainFeature" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail('large'); ?></a>
		<?php endif; ?>
			
			<div class="feature-body">
				<h3 class="feature-label">featured:</h3>
				<h4 class="sizes-LG"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
				<?php 
				if (has_excerpt()) {
					the_excerpt();
				} else {
					echo '<p class="summary">' . wp_trim_words(get_the_content(), 30, '...') . '</p>';
				}
				?>
				<a href="<?php the_permalink(); ?>" class="work-featured-item-link">
					<?php _e('View Project', 'work'); ?>
				</a>
			</div>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
					
		</article>
		<?php endif; ?>
			
		<?php if (!empty($regular_posts)) : ?>

		<div class="work-grid">
			<?php
			// Query regular posts
			$regular_query = new WP_Query(array(
				'post_type' => 'work',
				'post__in' => $regular_posts,
				'orderby' => 'date',
				'order' => 'DESC',
				'posts_per_page' => -1,
				'no_found_rows' => true, // Skip counting query for performance
				'update_post_meta_cache' => true, // Prefetch post meta
				'update_post_term_cache' => true // Prefetch terms
			));
			
			while ($regular_query->have_posts()) : $regular_query->the_post();
			?>
				<article role="article" class="feature is--promo">
                <h3>tax doc</h3>
					<a href="<?php the_permalink(); ?>" class="feature-img img--isPromo" tabindex="-1" aria-hidden="true">
						<?php if (has_post_thumbnail()) : ?>
							<?php the_post_thumbnail('medium_large'); ?>
						<?php endif; ?>
					</a>
					<h3 class="sizes-L"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<?php if (has_excerpt()) : ?>
						<div><?php the_excerpt(); ?></div>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>

		<?php endif; ?>
			
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
		<?php else : ?>
			<p class="work-no-results"><?php _e('Nothing to see here yet. Add some content to see it.', 'work'); ?></p>
		<?php endif; ?>
	</section>

</main><!-- #main -->


<?php
/**
 * work_before_category_footer hook
 */
do_action('work_before_category_footer');

get_footer();

/**
 * work_after_category_footer hook
 */
do_action('work_after_category_footer');