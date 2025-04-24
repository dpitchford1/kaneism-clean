<?php
/**
 * Kaneism Class
 *
 * @since    2.0.0
 * @package  kaneism
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Kaneism' ) ) :

	/**
	 * The main Kaneism class
	 */
	class Kaneism {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'after_setup_theme', array( $this, 'setup' ) );
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );
			add_filter( 'body_class', array( $this, 'body_classes' ) );
			add_filter( 'wp_page_menu_args', array( $this, 'page_menu_args' ) );
			add_filter( 'navigation_markup_template', array( $this, 'navigation_markup_template' ) );

		}

		/**
		 * Sets up theme defaults and registers support for various WordPress features.
		 *
		 * Note that this function is hooked into the after_setup_theme hook, which
		 * runs before the init hook. The init hook is too late for some features, such
		 * as indicating support for post thumbnails.
		 */
		public function setup() {
			/*
			 * Load Localisation files.
			 *
			 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
			 */

			// Loads wp-content/languages/themes/kaneism-it_IT.mo.
			load_theme_textdomain( 'kaneism', trailingslashit( WP_LANG_DIR ) . 'themes' );

			// Loads wp-content/themes/child-theme-name/languages/it_IT.mo.
			load_theme_textdomain( 'kaneism', get_stylesheet_directory() . '/languages' );

			// Loads wp-content/themes/kaneism/languages/it_IT.mo.
			load_theme_textdomain( 'kaneism', get_template_directory() . '/languages' );

			/*
			 * Enable support for Post Thumbnails on posts and pages.
			 *
			 * @link https://developer.wordpress.org/reference/functions/add_theme_support/#Post_Thumbnails
			 */
			add_theme_support( 'post-thumbnails' );

            /**
			 * Add new image sizes.
			 */
            add_image_size( 'kaneism-img-xl', 1400, 800, false );
            add_image_size( 'kaneism-img-lg', 1280, 720, false );
            add_image_size( 'kaneism-img-m', 980, 560, false );
            add_image_size( 'kaneism-img-sm', 600, 343, false );

            /**
			 * Add support for responsive embedded content.
			 */
			add_theme_support( 'responsive-embeds' );

            /** 
             * Add support for page excerpts.
             */
            add_post_type_support( 'page', 'excerpt' );

			/**
			 * Register menu locations.
			 */
			register_nav_menus(
				apply_filters(
					'kaneism_register_nav_menus',
					array(
						'primary'   => __( 'Primary Menu', 'kaneism' ),
						'secondary' => __( 'Secondary Menu', 'kaneism' )
					)
				)
			);

			/*
			 * Switch default core markup for search form, comment form, comments, galleries, captions and widgets
			 * to output valid HTML5.
			 */
			add_theme_support(
				'html5',
				apply_filters(
					'kaneism_html5_args',
					array(
						'search-form',
						'gallery',
						'caption',
						'widgets',
						'style',
						'script',
					)
				)
			);

			/**
			 * Declare support for title theme feature.
			 */
			add_theme_support( 'title-tag' );

			/**
			 * Add support for editor styles.
			 */
			add_theme_support( 'editor-styles' );

		}

		/**
		 * Register widget area.
		 *
		 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
		 */
		public function widgets_init() {
			$sidebar_args['sidebar'] = array(
				'name'        => __( 'Sidebar', 'kaneism' ),
				'id'          => 'sidebar-1',
				'description' => '',
			);

			$sidebar_args['header'] = array(
				'name'        => __( 'Below Header', 'kaneism' ),
				'id'          => 'header-1',
				'description' => __( 'Widgets added to this region will appear beneath the header and above the main content.', 'kaneism' ),
			);

			$rows    = intval( apply_filters( 'kaneism_footer_widget_rows', 1 ) );
			$regions = intval( apply_filters( 'kaneism_footer_widget_columns', 4 ) );

			for ( $row = 1; $row <= $rows; $row++ ) {
				for ( $region = 1; $region <= $regions; $region++ ) {
					$footer_n = $region + $regions * ( $row - 1 ); // Defines footer sidebar ID.
					$footer   = sprintf( 'footer_%d', $footer_n );

					if ( 1 === $rows ) {
						/* translators: 1: column number */
						$footer_region_name = sprintf( __( 'Footer Column %1$d', 'kaneism' ), $region );

						/* translators: 1: column number */
						$footer_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of the footer.', 'kaneism' ), $region );
					} else {
						/* translators: 1: row number, 2: column number */
						$footer_region_name = sprintf( __( 'Footer Row %1$d - Column %2$d', 'kaneism' ), $row, $region );

						/* translators: 1: column number, 2: row number */
						$footer_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of footer row %2$d.', 'kaneism' ), $region, $row );
					}

					$sidebar_args[ $footer ] = array(
						'name'        => $footer_region_name,
						'id'          => sprintf( 'footer-%d', $footer_n ),
						'description' => $footer_region_description,
					);
				}
			}

			$sidebar_args = apply_filters( 'kaneism_sidebar_args', $sidebar_args );

			foreach ( $sidebar_args as $sidebar => $args ) {
				$widget_tags = array(
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<span class="gamma widget-title">',
					'after_title'   => '</span>',
				);

				/**
				 * Dynamically generated filter hooks. Allow changing widget wrapper and title tags. See the list below.
				 *
				 * 'kaneism_header_widget_tags'
				 * 'kaneism_sidebar_widget_tags'
				 *
				 * 'kaneism_footer_1_widget_tags'
				 * 'kaneism_footer_2_widget_tags'
				 * 'kaneism_footer_3_widget_tags'
				 * 'kaneism_footer_4_widget_tags'
				 */
				$filter_hook = sprintf( 'kaneism_%s_widget_tags', $sidebar );
				$widget_tags = apply_filters( $filter_hook, $widget_tags );

				if ( is_array( $widget_tags ) ) {
					register_sidebar( $args + $widget_tags );
				}
			}
		}

		/**
		 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
		 *
		 * @param array $args Configuration arguments.
		 * @return array
		 */
		public function page_menu_args( $args ) {
			$args['show_home'] = true;
			return $args;
		}

		/**
		 * Adds custom classes to the array of body classes.
		 *
		 * @param array $classes Classes for the body element.
		 * @return array
		 */
		public function body_classes( $classes ) {
			// Adds a class to blogs with more than 1 published author.
			if ( is_multi_author() ) {
				$classes[] = 'group-blog';
			}

			/**
			 * Adds a class when WooCommerce is not active.
			 *
			 * @todo Refactor child themes to remove dependency on this class.
			 */
			$classes[] = 'no-wc-breadcrumb';

			// If our main sidebar doesn't contain widgets, adjust the layout to be full-width.
			if ( ! is_active_sidebar( 'sidebar-1' ) ) {
				$classes[] = 'kaneism-full-width-content';
			}

			// Add class when using homepage template + featured image.
			if ( is_page_template( 'template-homepage.php' ) && has_post_thumbnail() ) {
				$classes[] = 'has--post-thumbnail';
			}

			// Add class when Secondary Navigation is in use.
			if ( has_nav_menu( 'secondary' ) ) {
				$classes[] = 'kaneism-secondary-navigation';
			}

			return $classes;
		}

		/**
		 * Custom navigation markup template hooked into `navigation_markup_template` filter hook.
		 */
		public function navigation_markup_template() {
			$template  = '<nav id="post-navigation" class="navigation %1$s" role="navigation" aria-label="' . esc_html__( 'Post Navigation', 'kaneism' ) . '">';
			$template .= '<h2 class="screen-reader-text">%2$s</h2>';
			$template .= '<div class="nav-links">%3$s</div>';
			$template .= '</nav>';

			return apply_filters( 'kaneism_navigation_markup_template', $template );
		}
	}
endif;

return new Kaneism();
