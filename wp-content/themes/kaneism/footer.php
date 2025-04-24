<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package kaneism
 */

?>
<?php //do_action( 'kaneism_before_footer' ); ?>
<?php
/**
 * Functions hooked in to kaneism_footer action
 *
 * @hooked kaneism_footer_widgets - 10
 * @hooked kaneism_credit         - 20
 */
//do_action( 'kaneism_footer' );
?>
<?php //do_action( 'kaneism_after_footer' ); ?>

</div><!-- .inner-content -->
<?php /* Footer Start */ ?>
<div class="region global-footer cf" id="global-footer">
    <footer class="fluid cf" role="contentinfo">    
        <h3 class="hide-text">Additional Information</h3>
        <div class="footer-grid ra">
            <div class="footer-span-1">
                <div class="footer-logo"></div>
            </div>
            <div class="footer-area footer-span-2" itemscope itemtype="http://www.schema.org/SiteNavigationElement">
                <h4 class="xsm--m footer--heading sizes-LG">About</h4>
                <p>Artist, Designer, Web Developer based in Toronto. Painting murals since the 80's and building the web since 1999.</p>
                <p>For any inquiries please shoot over to the <a href="/contact/">contact page</a> and drop me a note.</p>
                
            </div>
            <div class="footer-area footer-span-3" itemscope itemtype="http://www.schema.org/SiteNavigationElement">
                <h4 class="xsm--m footer--heading sizes-LG">Browse <span class="hide-text">sections</span></h4>
                <?php /* Main Menu - different css */ ?>
                <?php
                    wp_nav_menu( 
                        array(
                            'theme_location'  => 'primary',
                            'menu_class' => 'no-bullet footer-lists',
                            'menu_id' => 'footer-menu',
                            'container' => 'ul'
                        )
                    );
                ?>
            </div>
            <div class="footer-area footer-span-4">
                <h4 class="xsm--m footer--heading sizes-LG">Work</h4>
                <?php
                    wp_nav_menu( 
                        array(
                            'theme_location'  => 'secondary',
                            'menu_class' => 'no-bullet footer-lists',
                            'menu_id' => 'footer-menu',
                            'container' => 'ul'
                        )
                    );
                ?>
                <p><em>*More sections coming soon</em></p>
                <a class="js-BackToTop" href="#page-body" onclick="return false">Top of page</a>
            </div>
            <p class="source-org copyright footer-area footer-span-5">&copy; <?php echo date('Y'); ?> Kaneism Design</p>
        </div>

    </footer>
</div>
<script src="/assets/js/core/base.js" async></script>
<?php wp_footer(); ?>

<?php /* Google tag (gtag.js) */ ?>
<?php if( !in_array( $_SERVER['REMOTE_ADDR'], array( '127.0.0.1', '::1' ) ) ) { ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-RW03VLJX2Y"></script>
<script>window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-RW03VLJX2Y');</script>
<?php } ?>

<?php get_template_part( 'template-parts/development-pilot' ); ?>
</body>
</html>
