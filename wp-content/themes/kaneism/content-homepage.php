<?php
/**
 * The template used for displaying page content in template-homepage.php
 *
 * @package kaneism
 */

?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php
		/**
		 * Functions hooked in to kaneism_page add_action
		 *
		 * @hooked kaneism_homepage_header      - 10
		 * @hooked kaneism_page_content         - 20
		 */
		do_action( 'kaneism_homepage' );
		?>
</div><?php /* #post-## */ ?>
