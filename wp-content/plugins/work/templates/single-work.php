<?php
/**
 * The template for displaying single Work posts
 *
 * @package Kane
 */

get_header();
?>

<?php if (function_exists('work_breadcrumb')) : ?>
    <?php work_breadcrumb(); ?>
<?php endif; ?>

<?php if (function_exists('work_category_navigation')) : ?>
    <?php work_category_navigation(); ?>
<?php endif; ?>

<main id="main-content" class="site-main" role="main">
	<section class="region">
	    <h2 class="sizes-XLG"><span class="feature-label">Project:</span> <span class=""><?php echo esc_html(get_the_title()); ?></span></h2>

    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>">
            
            <?php 
            // Get gallery images using the work data function
            $gallery_images = work_get_gallery_images(get_the_ID());
            
            if (!empty($gallery_images)) :
                // Scripts are now loaded in frontend.php
            ?>
            <div class="swiper">
                <ul class="swiper-wrapper">
                <?php foreach ($gallery_images as $index => $image_id) : ?>
                    <?php 
                    $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                    ?>
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
            </div>
        <?php elseif (has_post_thumbnail()) : ?>
            <div class="work-featured-image">
                <?php the_post_thumbnail('full'); ?>
            </div>
        <?php endif; ?>
        </article>

        <aside class="project--details" role="complimentary">
            <h3 class="sizes-LG">Project Details:</h3>
            <div class="details--wrap">
            <?php
            // Get description if function exists
            if (function_exists('work_get_description')) {
                echo '<div class="long-desc">' . wp_kses_post(work_get_description()) . '</div>';
            } else {
                echo '<div class="long-desc">';
                the_content();
                echo '</div>';
            }
            ?>
            
            <?php
            // Get project details if function exists
            if (function_exists('work_get_project_details')) {
                $project_details = work_get_project_details();
                
                // Only display table if we have details
                if (!empty($project_details)) :
                ?>
                <table class="table--general project--table">
                    <caption>Details for <?php echo esc_html(get_the_title()); ?></caption>
                    <thead>
                        <tr>
                            <?php foreach ($project_details as $detail) : ?>
                            <th><?php echo esc_html($detail['label']); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($project_details as $detail) : ?>
                            <td data-title="<?php echo esc_attr($detail['label']); ?>"><?php echo esc_html($detail['value']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
                <?php endif; ?>
            <?php } ?>
            </div>
        </aside>
    <?php endwhile; ?>

	</section>

	<?php
	// Add the Related Projects section
	// Get related works based on the current post's categories
	$current_id = get_the_ID();
	$categories = get_the_terms($current_id, 'work_category'); 
	
	if (!empty($categories) && !is_wp_error($categories)) {
		$category_ids = wp_list_pluck($categories, 'term_id');
		
		$related_args = array(
			'post_type' => 'work',
			'posts_per_page' => 3,
			'post__not_in' => array($current_id),
			'orderby' => 'rand',
			'tax_query' => array(
				array(
					'taxonomy' => 'work_category',
					'field' => 'term_id',
					'terms' => $category_ids,
				),
			),
		);
		
		$related_query = new WP_Query($related_args);
		
    if ($related_query->have_posts()) : 
        ?>
        <section class="kane-related-works">
            <h3 class="sizes-LG">Related Projects</h3>
            <div class="work-grid">
                <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                    <article class="kane-work-item feature is--promo">
                        <a href="<?php the_permalink(); ?>" class="feature-img img--isPromo" tabindex="-1" aria-hidden="true">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php 
                                // Use wp_get_attachment_image instead of the_post_thumbnail for consistency
                                echo wp_get_attachment_image(
                                    get_post_thumbnail_id(),
                                    'medium',
                                    false,
                                    array(
                                        'class' => 'wp-post-image',
                                        'loading' => 'lazy'
                                    )
                                );
                                ?>
                            <?php endif; ?>
                        </a>
                        <h4 class="sizes-L"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <?php if (has_excerpt()) : ?>
                        <div><?php the_excerpt(); ?></div>
                    <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            </div>
        </section>
        <?php
        wp_reset_postdata();
    endif;
	}
	?>

</main><?php /* #main */ ?>
<?php
get_footer();
?>