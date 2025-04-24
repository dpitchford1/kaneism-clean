<?php
/**
 * Template used to display post content on single pages.
 *
 * @package kaneism
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	do_action( 'kaneism_single_post_top' );

	/**
	 * Functions hooked into kaneism_single_post add_action
	 *
	 * @hooked kaneism_post_header          - 10
	 * @hooked kaneism_post_content         - 30
	 */
	do_action( 'kaneism_single_post' );

	/**
	 * Functions hooked in to kaneism_single_post_bottom action
	 *
	 * @hooked kaneism_post_nav         - 10
	 * @hooked kaneism_display_comments - 20
	 */
	//do_action( 'kaneism_single_post_bottom' );
	?>

</article><!-- #post-## -->
