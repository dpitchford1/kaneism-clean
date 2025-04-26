<?php
/**
 * WooCommerce Template Functions.
 *
 * @package kaneism
 */

if ( ! function_exists( 'kaneism_woo_cart_available' ) ) {
	/**
	 * Validates whether the Woo Cart instance is available in the request
	 *
	 * @since 2.6.0
	 * @return bool
	 */
	function kaneism_woo_cart_available() {
		$woo = WC();
		return $woo instanceof \WooCommerce && $woo->cart instanceof \WC_Cart;
	}
}

if ( ! function_exists( 'kaneism_before_content' ) ) {
	/**
	 * Before Content
	 * Wraps all WooCommerce content in wrappers which match the theme markup
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function kaneism_before_content() {
		?>
		<div id="primary" class="content-area">
			<main id="main-content" class="site-main" role="main">
		<?php
	}
}

if ( ! function_exists( 'kaneism_after_content' ) ) {
	/**
	 * After Content
	 * Closes the wrapping divs
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function kaneism_after_content() {
		?>
			</main><!-- #main -->
		</div><!-- #primary -->

		<?php
		do_action( 'kaneism_sidebar' );
	}
}

if ( ! function_exists( 'kaneism_cart_link_fragment' ) ) {
	/**
	 * Cart Fragments
	 * Ensure cart contents update when products are added to the cart via AJAX
	 *
	 * @param  array $fragments Fragments to refresh via AJAX.
	 * @return array            Fragments to refresh via AJAX
	 */
	function kaneism_cart_link_fragment( $fragments ) {
		global $woocommerce;

		ob_start();
		kaneism_cart_link();
		$fragments['a.cart-contents'] = ob_get_clean();

		ob_start();
		kaneism_handheld_footer_bar_cart_link();
		$fragments['a.footer-cart-contents'] = ob_get_clean();

		return $fragments;
	}
}

if ( ! function_exists( 'kaneism_cart_link' ) ) {
	/**
	 * Cart Link
	 * Displayed a link to the cart including the number of items present and the cart total
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function kaneism_cart_link() {
		if ( ! kaneism_woo_cart_available() ) {
			return;
		}
		?>
			<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'kaneism' ); ?>">
				<?php /* translators: %d: number of items in cart */ ?>
				<?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?> <span class="count"><?php echo wp_kses_data( sprintf( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'kaneism' ), WC()->cart->get_cart_contents_count() ) ); ?></span>
			</a>
		<?php
	}
}

if ( ! function_exists( 'kaneism_product_search' ) ) {
	/**
	 * Display Product Search
	 *
	 * @since  1.0.0
	 * @uses  kaneism_is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function kaneism_product_search() {
		if ( kaneism_is_woocommerce_activated() ) {
			?>
			<?php the_widget( 'WC_Widget_Product_Search', 'title=' ); ?>
			<?php
		}
	}
}

if ( ! function_exists( 'kaneism_header_cart' ) ) {
	/**
	 * Display Header Cart
	 *
	 * @since  1.0.0
	 * @uses  kaneism_is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function kaneism_header_cart() {
		if ( kaneism_is_woocommerce_activated() ) {
			if ( is_cart() ) {
				$class = 'current-menu-item';
			} else {
				$class = '';
			}
			?>
		<ul id="site-header-cart" class="site-header-cart menu">
			<li class="<?php echo esc_attr( $class ); ?>">
				<?php kaneism_cart_link(); ?>
			</li>
			<li>
				<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
			</li>
		</ul>
			<?php
		}
	}
}

if ( ! function_exists( 'kaneism_upsell_display' ) ) {
	/**
	 * Upsells
	 * Replace the default upsell function with our own which displays the correct number product columns
	 *
	 * @since   1.0.0
	 * @return  void
	 * @uses    woocommerce_upsell_display()
	 */
	function kaneism_upsell_display() {
		$columns = apply_filters( 'kaneism_upsells_columns', 3 );
		woocommerce_upsell_display( -1, $columns );
	}
}

if ( ! function_exists( 'kaneism_sorting_wrapper' ) ) {
	/**
	 * Sorting wrapper
	 *
	 * @since   1.4.3
	 * @return  void
	 */
	function kaneism_sorting_wrapper() {
		echo '<div class="kaneism-sorting">';
	}
}

if ( ! function_exists( 'kaneism_sorting_wrapper_close' ) ) {
	/**
	 * Sorting wrapper close
	 *
	 * @since   1.4.3
	 * @return  void
	 */
	function kaneism_sorting_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'kaneism_product_columns_wrapper' ) ) {
	/**
	 * Product columns wrapper
	 *
	 * @since   2.2.0
	 * @return  void
	 */
	function kaneism_product_columns_wrapper() {
		$columns = kaneism_loop_columns();
		echo '<div class="columns-' . absint( $columns ) . '">';
	}
}

if ( ! function_exists( 'kaneism_loop_columns' ) ) {
	/**
	 * Default loop columns on product archives
	 *
	 * @return integer products per row
	 * @since  1.0.0
	 */
	function kaneism_loop_columns() {
		$columns = 3; // 3 products per row

		if ( function_exists( 'wc_get_default_products_per_row' ) ) {
			$columns = wc_get_default_products_per_row();
		}

		return apply_filters( 'kaneism_loop_columns', $columns );
	}
}

if ( ! function_exists( 'kaneism_product_columns_wrapper_close' ) ) {
	/**
	 * Product columns wrapper close
	 *
	 * @since   2.2.0
	 * @return  void
	 */
	function kaneism_product_columns_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'kaneism_shop_messages' ) ) {
	/**
	 * Kaneism shop messages
	 *
	 * @since   1.4.4
	 * @uses    kaneism_do_shortcode
	 */
	function kaneism_shop_messages() {
		if ( ! is_checkout() ) {
			$kses_defaults = wp_kses_allowed_html( 'post' );

			$svg_args = array(
				'svg'   => array(
					'class'           => true,
					'aria-hidden'     => true,
					'aria-labelledby' => true,
					'role'            => true,
					'xmlns'           => true,
					'width'           => true,
					'height'          => true,
					'viewbox'         => true,
				),
				'g'     => array(
					'fill' => true,
				),
				'title' => array(
					'title' => true,
				),
				'path'  => array(
					'd'    => true,
					'fill' => true,
				),
			);

			$allowed_tags = array_merge( $kses_defaults, $svg_args );

			echo wp_kses( kaneism_do_shortcode( 'woocommerce_messages' ), $allowed_tags );
		}
	}
}

if ( ! function_exists( 'kaneism_woocommerce_pagination' ) ) {
	/**
	 * Kaneism WooCommerce Pagination
	 * WooCommerce disables the product pagination inside the woocommerce_product_subcategories() function
	 * but since Kaneism adds pagination before that function is excuted we need a separate function to
	 * determine whether or not to display the pagination.
	 *
	 * @since 1.4.4
	 */
	function kaneism_woocommerce_pagination() {
		if ( woocommerce_products_will_display() ) {
			woocommerce_pagination();
		}
	}
}

if ( ! function_exists( 'kaneism_product_categories' ) ) {
	/**
	 * Display Product Categories
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function kaneism_product_categories( $args ) {
		$args = apply_filters(
			'kaneism_product_categories_args',
			array(
				'limit'            => 3,
				'columns'          => 3,
				'child_categories' => 0,
				'orderby'          => 'menu_order',
				'title'            => __( 'Shop by Category', 'kaneism' ),
			)
		);

		$shortcode_content = kaneism_do_shortcode(
			'product_categories',
			apply_filters(
				'kaneism_product_categories_shortcode_args',
				array(
					'number'  => intval( $args['limit'] ),
					'columns' => intval( $args['columns'] ),
					'orderby' => esc_attr( $args['orderby'] ),
					'parent'  => esc_attr( $args['child_categories'] ),
				)
			)
		);

		/**
		 * Only display the section if the shortcode returns product categories
		 */
		if ( false !== strpos( $shortcode_content, 'product-category' ) ) {
			echo '<section class="kaneism-product-section kaneism-product-categories" aria-label="' . esc_attr__( 'Product Categories', 'kaneism' ) . '">';

			do_action( 'kaneism_homepage_before_product_categories' );

			echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

			do_action( 'kaneism_homepage_after_product_categories_title' );

			echo $shortcode_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			do_action( 'kaneism_homepage_after_product_categories' );

			echo '</section>';
		}
	}
}

if ( ! function_exists( 'kaneism_recent_products' ) ) {
	/**
	 * Display Recent Products
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function kaneism_recent_products( $args ) {
		$args = apply_filters(
			'kaneism_recent_products_args',
			array(
				'limit'   => 4,
				'columns' => 4,
				'orderby' => 'date',
				'order'   => 'desc',
				'title'   => __( 'New In', 'kaneism' ),
			)
		);

		$shortcode_content = kaneism_do_shortcode(
			'products',
			apply_filters(
				'kaneism_recent_products_shortcode_args',
				array(
					'orderby'  => esc_attr( $args['orderby'] ),
					'order'    => esc_attr( $args['order'] ),
					'per_page' => intval( $args['limit'] ),
					'columns'  => intval( $args['columns'] ),
				)
			)
		);

		/**
		 * Only display the section if the shortcode returns products
		 */
		if ( false !== strpos( $shortcode_content, 'product' ) ) {
			echo '<section class="kaneism-product-section kaneism-recent-products" aria-label="' . esc_attr__( 'Recent Products', 'kaneism' ) . '">';

			do_action( 'kaneism_homepage_before_recent_products' );

			echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

			do_action( 'kaneism_homepage_after_recent_products_title' );

			echo $shortcode_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			do_action( 'kaneism_homepage_after_recent_products' );

			echo '</section>';
		}
	}
}

if ( ! function_exists( 'kaneism_featured_products' ) ) {
	/**
	 * Display Featured Products
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function kaneism_featured_products( $args ) {
		$args = apply_filters(
			'kaneism_featured_products_args',
			array(
				'limit'      => 4,
				'columns'    => 4,
				'orderby'    => 'date',
				'order'      => 'desc',
				'visibility' => 'featured',
				'title'      => __( 'We Recommend', 'kaneism' ),
			)
		);

		$shortcode_content = kaneism_do_shortcode(
			'products',
			apply_filters(
				'kaneism_featured_products_shortcode_args',
				array(
					'per_page'   => intval( $args['limit'] ),
					'columns'    => intval( $args['columns'] ),
					'orderby'    => esc_attr( $args['orderby'] ),
					'order'      => esc_attr( $args['order'] ),
					'visibility' => esc_attr( $args['visibility'] ),
				)
			)
		);

		/**
		 * Only display the section if the shortcode returns products
		 */
		if ( false !== strpos( $shortcode_content, 'product' ) ) {
			echo '<section class="kaneism-product-section kaneism-featured-products" aria-label="' . esc_attr__( 'Featured Products', 'kaneism' ) . '">';

			do_action( 'kaneism_homepage_before_featured_products' );

			echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

			do_action( 'kaneism_homepage_after_featured_products_title' );

			echo $shortcode_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			do_action( 'kaneism_homepage_after_featured_products' );

			echo '</section>';
		}
	}
}

if ( ! function_exists( 'kaneism_popular_products' ) ) {
	/**
	 * Display Popular Products
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function kaneism_popular_products( $args ) {
		$args = apply_filters(
			'kaneism_popular_products_args',
			array(
				'limit'   => 4,
				'columns' => 4,
				'orderby' => 'rating',
				'order'   => 'desc',
				'title'   => __( 'Fan Favorites', 'kaneism' ),
			)
		);

		$shortcode_content = kaneism_do_shortcode(
			'products',
			apply_filters(
				'kaneism_popular_products_shortcode_args',
				array(
					'per_page' => intval( $args['limit'] ),
					'columns'  => intval( $args['columns'] ),
					'orderby'  => esc_attr( $args['orderby'] ),
					'order'    => esc_attr( $args['order'] ),
				)
			)
		);

		/**
		 * Only display the section if the shortcode returns products
		 */
		if ( false !== strpos( $shortcode_content, 'product' ) ) {
			echo '<section class="kaneism-product-section kaneism-popular-products" aria-label="' . esc_attr__( 'Popular Products', 'kaneism' ) . '">';

			do_action( 'kaneism_homepage_before_popular_products' );

			echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

			do_action( 'kaneism_homepage_after_popular_products_title' );

			echo $shortcode_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			do_action( 'kaneism_homepage_after_popular_products' );

			echo '</section>';
		}
	}
}

if ( ! function_exists( 'kaneism_on_sale_products' ) ) {
	/**
	 * Display On Sale Products
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @param array $args the product section args.
	 * @since  1.0.0
	 * @return void
	 */
	function kaneism_on_sale_products( $args ) {
		$args = apply_filters(
			'kaneism_on_sale_products_args',
			array(
				'limit'   => 4,
				'columns' => 4,
				'orderby' => 'date',
				'order'   => 'desc',
				'on_sale' => 'true',
				'title'   => __( 'On Sale', 'kaneism' ),
			)
		);

		$shortcode_content = kaneism_do_shortcode(
			'products',
			apply_filters(
				'kaneism_on_sale_products_shortcode_args',
				array(
					'per_page' => intval( $args['limit'] ),
					'columns'  => intval( $args['columns'] ),
					'orderby'  => esc_attr( $args['orderby'] ),
					'order'    => esc_attr( $args['order'] ),
					'on_sale'  => esc_attr( $args['on_sale'] ),
				)
			)
		);

		/**
		 * Only display the section if the shortcode returns products
		 */
		if ( false !== strpos( $shortcode_content, 'product' ) ) {
			echo '<section class="kaneism-product-section kaneism-on-sale-products" aria-label="' . esc_attr__( 'On Sale Products', 'kaneism' ) . '">';

			do_action( 'kaneism_homepage_before_on_sale_products' );

			echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

			do_action( 'kaneism_homepage_after_on_sale_products_title' );

			echo $shortcode_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			do_action( 'kaneism_homepage_after_on_sale_products' );

			echo '</section>';
		}
	}
}

if ( ! function_exists( 'kaneism_best_selling_products' ) ) {
	/**
	 * Display Best Selling Products
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since 2.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function kaneism_best_selling_products( $args ) {
		$args = apply_filters(
			'kaneism_best_selling_products_args',
			array(
				'limit'   => 4,
				'columns' => 4,
				'orderby' => 'popularity',
				'order'   => 'desc',
				'title'   => esc_attr__( 'Best Sellers', 'kaneism' ),
			)
		);

		$shortcode_content = kaneism_do_shortcode(
			'products',
			apply_filters(
				'kaneism_best_selling_products_shortcode_args',
				array(
					'per_page' => intval( $args['limit'] ),
					'columns'  => intval( $args['columns'] ),
					'orderby'  => esc_attr( $args['orderby'] ),
					'order'    => esc_attr( $args['order'] ),
				)
			)
		);

		/**
		 * Only display the section if the shortcode returns products
		 */
		if ( false !== strpos( $shortcode_content, 'product' ) ) {
			echo '<section class="kaneism-product-section kaneism-best-selling-products" aria-label="' . esc_attr__( 'Best Selling Products', 'kaneism' ) . '">';

			do_action( 'kaneism_homepage_before_best_selling_products' );

			echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

			do_action( 'kaneism_homepage_after_best_selling_products_title' );

			echo $shortcode_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			do_action( 'kaneism_homepage_after_best_selling_products' );

			echo '</section>';
		}
	}
}

if ( ! function_exists( 'kaneism_promoted_products' ) ) {
	/**
	 * Featured and On-Sale Products
	 * Check for featured products then on-sale products and use the appropiate shortcode.
	 * If neither exist, it can fallback to show recently added products.
	 *
	 * @since  1.5.1
	 * @param integer $per_page total products to display.
	 * @param integer $columns columns to arrange products in to.
	 * @param boolean $recent_fallback Should the function display recent products as a fallback when there are no featured or on-sale products?.
	 * @uses  kaneism_is_woocommerce_activated()
	 * @uses  wc_get_featured_product_ids()
	 * @uses  wc_get_product_ids_on_sale()
	 * @uses  kaneism_do_shortcode()
	 * @return void
	 */
	function kaneism_promoted_products( $per_page = '2', $columns = '2', $recent_fallback = true ) {
		if ( kaneism_is_woocommerce_activated() ) {

			if ( wc_get_featured_product_ids() ) {

				echo '<h2>' . esc_html__( 'Featured Products', 'kaneism' ) . '</h2>';

				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				echo kaneism_do_shortcode(
					'featured_products',
					array(
						'per_page' => $per_page,
						'columns'  => $columns,
					)
				);
				// phpcs:enable
			} elseif ( wc_get_product_ids_on_sale() ) {

				echo '<h2>' . esc_html__( 'On Sale Now', 'kaneism' ) . '</h2>';

				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				echo kaneism_do_shortcode(
					'sale_products',
					array(
						'per_page' => $per_page,
						'columns'  => $columns,
					)
				);
				// phpcs:enable
			} elseif ( $recent_fallback ) {

				echo '<h2>' . esc_html__( 'New In Store', 'kaneism' ) . '</h2>';

				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				echo kaneism_do_shortcode(
					'recent_products',
					array(
						'per_page' => $per_page,
						'columns'  => $columns,
					)
				);
				// phpcs:enable
			}
		}
	}
}

if ( ! function_exists( 'kaneism_handheld_footer_bar' ) ) {
	/**
	 * Display a menu intended for use on handheld devices
	 *
	 * @since 2.0.0
	 */
	function kaneism_handheld_footer_bar() {
		$links = array(
			'my-account' => array(
				'priority' => 10,
				'callback' => 'kaneism_handheld_footer_bar_account_link',
			),
			'search'     => array(
				'priority' => 20,
				'callback' => 'kaneism_handheld_footer_bar_search',
			),
			'cart'       => array(
				'priority' => 30,
				'callback' => 'kaneism_handheld_footer_bar_cart_link',
			),
		);

		if ( did_action( 'woocommerce_blocks_enqueue_cart_block_scripts_after' ) || did_action( 'woocommerce_blocks_enqueue_checkout_block_scripts_after' ) ) {
			return;
		}

		if ( wc_get_page_id( 'myaccount' ) === -1 ) {
			unset( $links['my-account'] );
		}

		if ( wc_get_page_id( 'cart' ) === -1 ) {
			unset( $links['cart'] );
		}

		$links = apply_filters( 'kaneism_handheld_footer_bar_links', $links );
		?>
		<div class="kaneism-handheld-footer-bar">
			<ul class="columns-<?php echo count( $links ); ?>">
				<?php foreach ( $links as $key => $link ) : ?>
					<li class="<?php echo esc_attr( $key ); ?>">
						<?php
						if ( $link['callback'] ) {
							call_user_func( $link['callback'], $key, $link );
						}
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}

if ( ! function_exists( 'kaneism_handheld_footer_bar_search' ) ) {
	/**
	 * The search callback function for the handheld footer bar
	 *
	 * @since 2.0.0
	 */
	function kaneism_handheld_footer_bar_search() {
		echo '<a href="">' . esc_attr__( 'Search', 'kaneism' ) . '</a>';
		kaneism_product_search();
	}
}

if ( ! function_exists( 'kaneism_handheld_footer_bar_cart_link' ) ) {
	/**
	 * The cart callback function for the handheld footer bar
	 *
	 * @since 2.0.0
	 */
	function kaneism_handheld_footer_bar_cart_link() {
		if ( ! kaneism_woo_cart_available() ) {
			return;
		}
		?>
			<a class="footer-cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Cart', 'kaneism' ); ?>
				<span class="count"><?php echo wp_kses_data( WC()->cart->get_cart_contents_count() ); ?></span>
			</a>
		<?php
	}
}

if ( ! function_exists( 'kaneism_handheld_footer_bar_account_link' ) ) {
	/**
	 * The account callback function for the handheld footer bar
	 *
	 * @since 2.0.0
	 */
	function kaneism_handheld_footer_bar_account_link() {
		echo '<a href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '">' . esc_attr__( 'My Account', 'kaneism' ) . '</a>';
	}
}

if ( ! function_exists( 'kaneism_single_product_pagination' ) ) {
	/**
	 * Single Product Pagination
	 *
	 * @since 2.3.0
	 */
	function kaneism_single_product_pagination() {
		if ( class_exists( 'Kaneism_Product_Pagination' ) || true !== get_theme_mod( 'kaneism_product_pagination' ) ) {
			return;
		}

		/**
		 * Show only products in the same category?
		 */
		$in_same_term   = apply_filters( 'kaneism_single_product_pagination_same_category', true );
		$excluded_terms = apply_filters( 'kaneism_single_product_pagination_excluded_terms', '' );
		$taxonomy       = apply_filters( 'kaneism_single_product_pagination_taxonomy', 'product_cat' );

		$previous_product = kaneism_get_previous_product( $in_same_term, $excluded_terms, $taxonomy );
		$next_product     = kaneism_get_next_product( $in_same_term, $excluded_terms, $taxonomy );

		if ( ! $previous_product && ! $next_product ) {
			return;
		}

		?>
		<nav class="kaneism-product-pagination" aria-label="<?php esc_attr_e( 'More products', 'kaneism' ); ?>">
			<?php if ( $previous_product ) : ?>
				<a href="<?php echo esc_url( $previous_product->get_permalink() ); ?>" rel="prev">
					<?php echo wp_kses_post( $previous_product->get_image() ); ?>
					<span class="kaneism-product-pagination__title"><?php echo wp_kses_post( $previous_product->get_name() ); ?></span>
				</a>
			<?php endif; ?>

			<?php if ( $next_product ) : ?>
				<a href="<?php echo esc_url( $next_product->get_permalink() ); ?>" rel="next">
					<?php echo wp_kses_post( $next_product->get_image() ); ?>
					<span class="kaneism-product-pagination__title"><?php echo wp_kses_post( $next_product->get_name() ); ?></span>
				</a>
			<?php endif; ?>
		</nav><!-- .kaneism-product-pagination -->
		<?php
	}
}

if ( ! function_exists( 'kaneism_sticky_single_add_to_cart' ) ) {
	/**
	 * Sticky Add to Cart
	 *
	 * @since 2.3.0
	 */
	function kaneism_sticky_single_add_to_cart() {
		global $product;

		if ( class_exists( 'Kaneism_Sticky_Add_to_Cart' ) || true !== get_theme_mod( 'kaneism_sticky_add_to_cart' ) ) {
			return;
		}

		if ( ! $product || ! is_product() ) {
			return;
		}

		$show = false;

		if ( $product->is_purchasable() && $product->is_in_stock() ) {
			$show = true;
		} elseif ( $product->is_type( 'external' ) ) {
			$show = true;
		}

		if ( ! $show ) {
			return;
		}

		$params = apply_filters(
			'kaneism_sticky_add_to_cart_params',
			array(
				'trigger_class' => 'entry-summary',
			)
		);

		wp_localize_script( 'kaneism-sticky-add-to-cart', 'kaneism_sticky_add_to_cart_params', $params );

		wp_enqueue_script( 'kaneism-sticky-add-to-cart' );
		?>
			<section class="kaneism-sticky-add-to-cart">
				<div class="col-full">
					<div class="kaneism-sticky-add-to-cart__content">
						<?php echo wp_kses_post( woocommerce_get_product_thumbnail() ); ?>
						<div class="kaneism-sticky-add-to-cart__content-product-info">
							<span class="kaneism-sticky-add-to-cart__content-title"><?php esc_html_e( 'You\'re viewing:', 'kaneism' ); ?> <strong><?php the_title(); ?></strong></span>
							<span class="kaneism-sticky-add-to-cart__content-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
							<?php echo wp_kses_post( wc_get_rating_html( $product->get_average_rating() ) ); ?>
						</div>
						<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="kaneism-sticky-add-to-cart__content-button button alt" rel="nofollow">
							<?php echo esc_attr( $product->add_to_cart_text() ); ?>
						</a>
					</div>
				</div>
			</section><!-- .kaneism-sticky-add-to-cart -->
		<?php
	}
}

if ( ! function_exists( 'kaneism_woocommerce_brands_homepage_section' ) ) {
	/**
	 * Display WooCommerce Brands
	 * Hooked into the `homepage` action in the homepage template.
	 * Requires WooCommerce Brands.
	 *
	 * @since  2.3.0
	 * @link   https://woocommerce.com/products/brands/
	 * @uses   apply_filters()
	 * @uses   kaneism_do_shortcode()
	 * @uses   wp_kses_post()
	 * @uses   do_action()
	 * @return void
	 */
	function kaneism_woocommerce_brands_homepage_section() {
		$args = apply_filters(
			'kaneism_woocommerce_brands_args',
			array(
				'number'     => 6,
				'columns'    => 4,
				'orderby'    => 'name',
				'show_empty' => false,
				'title'      => __( 'Shop by Brand', 'kaneism' ),
			)
		);

		$shortcode_content = kaneism_do_shortcode(
			'product_brand_thumbnails',
			apply_filters(
				'kaneism_woocommerce_brands_shortcode_args',
				array(
					'number'     => absint( $args['number'] ),
					'columns'    => absint( $args['columns'] ),
					'orderby'    => esc_attr( $args['orderby'] ),
					'show_empty' => (bool) $args['show_empty'],
				)
			)
		);

		echo '<section class="kaneism-product-section kaneism-woocommerce-brands" aria-label="' . esc_attr__( 'Product Brands', 'kaneism' ) . '">';

		do_action( 'kaneism_homepage_before_woocommerce_brands' );

		echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

		do_action( 'kaneism_homepage_after_woocommerce_brands_title' );

		echo $shortcode_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'kaneism_homepage_after_woocommerce_brands' );

		echo '</section>';
	}
}

if ( ! function_exists( 'kaneism_woocommerce_brands_archive' ) ) {
	/**
	 * Display brand image on brand archives
	 * Requires WooCommerce Brands.
	 *
	 * @since  2.3.0
	 * @link   https://woocommerce.com/products/brands/
	 * @uses   is_tax()
	 * @uses   wp_kses_post()
	 * @uses   get_brand_thumbnail_image()
	 * @uses   get_queried_object()
	 * @return void
	 */
	function kaneism_woocommerce_brands_archive() {
		if ( is_tax( 'product_brand' ) ) {
			echo wp_kses_post( get_brand_thumbnail_image( get_queried_object() ) );
		}
	}
}

if ( ! function_exists( 'kaneism_woocommerce_brands_single' ) ) {
	/**
	 * Output product brand image for use on single product pages
	 * Requires WooCommerce Brands.
	 *
	 * @since  2.3.0
	 * @link   https://woocommerce.com/products/brands/
	 * @uses   kaneism_do_shortcode()
	 * @uses   wp_kses_post()
	 * @return void
	 */
	function kaneism_woocommerce_brands_single() {
		$brand = kaneism_do_shortcode(
			'product_brand',
			array(
				'class' => '',
			)
		);

		if ( empty( $brand ) ) {
			return;
		}

		?>
		<div class="kaneism-wc-brands-single-product">
			<?php echo wp_kses_post( $brand ); ?>
		</div>
		<?php
	}
}
