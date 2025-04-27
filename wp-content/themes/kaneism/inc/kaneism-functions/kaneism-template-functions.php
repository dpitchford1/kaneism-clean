<?php
/**
 * Kaneism template functions.
 *
 * @package kaneism
 */

 if ( ! function_exists( 'kaneism_page_content' ) ) {
	/**
	 * Display the post content
	 *
	 * @since 1.0.0
	 */
	function kaneism_page_content() {
		?>
		<div class="entry-content">
			<?php the_content(); ?>
			<?php
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . __( 'Pages:', 'kaneism' ),
						'after'  => '</div>',
					)
				);
			?>
		</div><!-- .entry-content -->
		<?php
	}
}


if ( ! function_exists( 'kaneism_post_taxonomy' ) ) {
	/**
	 * Display the post taxonomies
	 *
	 * @since 2.4.0
	 */
	function kaneism_post_taxonomy() {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( __( ', ', 'kaneism' ) );

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', __( ', ', 'kaneism' ) );
		?>

		<aside class="entry-taxonomy">
			<?php if ( $categories_list ) : ?>
			<div class="cat-links">
				<?php echo esc_html( _n( 'Category:', 'Categories:', count( get_the_category() ), 'kaneism' ) ); ?> <?php echo wp_kses_post( $categories_list ); ?>
			</div>
			<?php endif; ?>

			<?php if ( $tags_list ) : ?>
			<div class="tags-links">
				<?php echo esc_html( _n( 'Tag:', 'Tags:', count( get_the_tags() ), 'kaneism' ) ); ?> <?php echo wp_kses_post( $tags_list ); ?>
			</div>
			<?php endif; ?>
		</aside>

		<?php
	}
}

if ( ! function_exists( 'kaneism_post_thumbnail' ) ) {
	/**
	 * Display post thumbnail
	 *
	 * @var $size thumbnail size. thumbnail|medium|large|full|$custom
	 * @uses has_post_thumbnail()
	 * @uses the_post_thumbnail
	 * @param string $size the post thumbnail size.
	 * @since 1.5.0
	 */
	function kaneism_post_thumbnail( $size = 'full' ) {
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( $size );
		}
	}
}

