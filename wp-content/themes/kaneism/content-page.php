<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package kaneism
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	/**
	 * Functions hooked in to kaneism_page add_action
	 *
	 * @hooked kaneism_page_header          - 10
	 * @hooked kaneism_page_content         - 20
	 */
	do_action( 'kaneism_page' );
	?>
</article><!-- #post-## -->
