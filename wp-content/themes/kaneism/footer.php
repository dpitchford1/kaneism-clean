<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package kaneism
 */

?>

		</div><!-- .col-full --> 
	</div><!-- #content -->

	<?php do_action( 'kaneism_before_footer' ); ?>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="col-full">

			<?php
			/**
			 * Functions hooked in to kaneism_footer action
			 *
			 * @hooked kaneism_footer_widgets - 10
			 * @hooked kaneism_credit         - 20
			 */
			do_action( 'kaneism_footer' );
			?>

		</div><!-- .col-full -->
	</footer><!-- #colophon -->

	<?php do_action( 'kaneism_after_footer' ); ?>

</div><!-- #page -->

<!-- <script src="/assets/js/core/base.min.js" async></script> -->
<?php wp_footer(); ?>

<?php /* Google tag (gtag.js) */ ?>
<?php if( !in_array( $_SERVER['REMOTE_ADDR'], array( '127.0.0.1', '::1' ) ) ) { ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-RW03VLJX2Y"></script>
<script>window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-RW03VLJX2Y');</script>
<?php } ?>

<?php // get_template_part( 'template-parts/development-pilot' ); ?>
</body>
</html>
