<?php
/**
 * The template used for displaying page content in template-homepage.php
 *
 * @package kaneism
 */

?>
<?php
$featured_image = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?> data-featured-image="<?php echo esc_url( $featured_image ); ?>">
	<div class="col-full">
		<?php
		/**
		 * Functions hooked in to kaneism_page add_action
		 *
		 * @hooked kaneism_homepage_header      - 10
		 * @hooked kaneism_page_content         - 20
		 */
		do_action( 'kaneism_homepage' );
		?>
	</div>
</div><!-- #post-## -->
