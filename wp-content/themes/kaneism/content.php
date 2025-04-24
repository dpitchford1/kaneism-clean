<?php
/**
 * Template used to display post content.
 *
 * @package kaneism
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	/**
	 * Functions hooked in to kaneism_loop_post action.
	 *
	 * @hooked kaneism_post_header          - 10
	 * @hooked kaneism_post_content         - 30
	 * @hooked kaneism_post_taxonomy        - 40
	 */
	do_action( 'kaneism_loop_post' );
	?>

</article><!-- #post-## -->
