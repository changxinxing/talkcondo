<?php

/**
 * AE Compatibility WooCommerce
 *
 * @class 		AE_Compatibility_WooCommerce
 * @version		1.1.9
 * @package		AE/Compatibility/Classes
 * @category	Class
 * @author 		Ascripta
 */

if( ! defined( 'ABSPATH' ) ){
	exit;
}

if ( !class_exists( 'AE_Compatibility_WooCommerce' ) ) :

class AE_Compatibility_WooCommerce {

	/**
	 * Construct the class.
	 */
	public function __construct(){

		if( AE_Admin_Settings::get_option( 'woocommerce', 'ae_advanced_compatibility', 'on' ) == 'on' ){

			// Initialize the class.
			add_action( 'plugins_loaded', array( $this, 'initialize' ) );

		}

	}

	/**
	 * Initialize the class.
	 */
	public function initialize(){

		if( class_exists( 'WooCommerce' ) ){

			// Prevent the default WooCommerce stylesheets from being loaded.
			if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 ) {
				add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
			} else {
				define( 'WOOCOMMERCE_USE_CSS', false );
			}

			// Register sidebar.
			add_action( 'widgets_init', array( $this, 'register_sidebar' ) );

			// Register stylesheet.
			add_action( 'wp_enqueue_scripts', array( $this, 'register_style' ), 99 );

			// Locate WooCommerce templates.
			add_filter( 'wc_get_template_part', array( $this, 'locate_root_template' ), 10, 3 );
			add_filter( 'woocommerce_locate_template', array( $this, 'locate_template' ), 10, 3 );
			add_filter( 'comments_template', array( $this, 'locate_reviews_template' ), 100, 1);

			// Add classses to the product details.
			remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open' );
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'woocommerce_template_loop_product_link_open' ), 10 );

			// Items per page on loops.
			add_filter( 'loop_shop_per_page', array( $this, 'items_per_page' ), 20 );

			// Items per row on related product sections.
			add_filter( 'woocommerce_output_related_products_args', array( $this, 'items_per_row_related' ), 20 );

			// Items per row on cross-sells.
			add_filter( 'woocommerce_cross_sells_columns', array( $this, 'items_per_row_cross_sells' ), 4 );

			// Remove the default loop ordering feature.
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

			// Hide the "Install the WooThemes Updater" notice.
			remove_action( 'admin_notices', 'woothemes_updater_notice' );

			// Add the Bootstrap class to the checkout order notes field.
			add_filter( 'woocommerce_checkout_fields', array( $this, 'woocommerce_checkout_fields' ) );

			// Add theme support for WooCommerce.
			add_theme_support( 'woocommerce' );	

			// Add support for the v3.0	gallery.
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );

		}

	}

	/**
	 * Add the WooCommerce sidebar to Wordpress and remove incompatible widgets.
	 */
	public function register_sidebar() {

		register_sidebar( array(
			'id'            => 'sidebar-woocommerce',
			'name'          => __( 'Woocommerce Sidebar', 'ascripta' ),
			'description'   => __( 'Used for WooCommerce pages.', 'ascripta' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">', 'after_widget' => '</section>',
			'before_title'  => '<h4 class="widget-title widgettitle">', 'after_title' => '</h4>',
		) );

		unregister_widget( 'WC_Widget_Recent_Reviews' );

	}

	/**
	 * Register the engine compatibility stylesheet.
	 */
	public function register_style() {

		wp_enqueue_style( 'asc-woocommerce', ASCRIPTA_ENGINE_CSS_URL . 'compatibility/asc-woocommerce.min.css', array(), ASCRIPTA_ENGINE_VERSION, 'all' );

	}

	/**
	 * Locate the input template within the plugin.
	 *
	 * @param  string $template      The input template.
	 * @param  string $name          The template name.
	 * @param  string [$path = null] The template path.
	 * @return string The final template location.
	 */
	public function locate_template( $template, $name = null, $path = null ){

		// Store the template
		$_template = $template;

		// Get the template path
		if ( !$path ){
			$path = WC()->template_path();
		}

		// Search for theme template and if it exists, use it.
		$template = locate_template( array( $path . $name, $name ) );

		// Use the plugin template or the default one.
		if ( !$template ){
			$plugin_template = ASCRIPTA_ENGINE_PATH . 'templates/' . $path . $name;
			if( file_exists( $plugin_template ) ){
				$template = $plugin_template;
			} else {
				$template = $_template;
			}
		}

		return $template;

	}

	/**
	 * Locate the reviews template.
	 *
	 * @param  string $template The original template.
	 * @return string The optimized template location.
	 *
	 * @version 1.1.2
	 */
	public function locate_reviews_template( $template ){

		if( get_post_type() == 'product' ){
			return $this->locate_template( $template, 'single-product-reviews.php', null );
		}

	}

	/**
	 * Insert the opening anchor tag for products in the loop.
	 */
	public function woocommerce_template_loop_product_link_open(){
		echo '<a href="' . get_the_permalink() . '" class="thumbnail product-details">';
	}

	/**
	 * Locate templates within the root of the compatibility folder.
	 *
	 * @param  string $template      The input template.
	 * @param  string $name          The template name.
	 * @param  string [$path = null] The template path.
	 * @return string The final template location.
	 */
	public function locate_root_template( $template, $slug, $name ){

		if ( $name ) {
			$name = "{$slug}-{$name}.php";
		} else {
			$name = "{$slug}.php";
		}

		return $this->locate_template( $template, $name );

	}

	/**
	 * The number of items to be displayed per loop page.
	 */
	public function items_per_page(){

		return 12;

	}

	/**
	 * Items per row on cross-sells.
	 */
	public function items_per_row_cross_sells() {

		return 4;

	}
	
	/**
	 * Items per row on related products.
	 */
	public function items_per_row_related( $args ) {

		$args['posts_per_page'] = 3;
		$args['columns'] = 3;

		return $args;

	}

	/**
	 * Add the Bootstrap class to the checkout order notes field.
	 */
	public function woocommerce_checkout_fields( $fields ) {

		$fields['order']['order_comments']['input_class'][] = 'form-control';

		return $fields;

	}

	/**
	 * Get the items for the breadcrumb trail if WooCommerce is installed.
	 */
	public static function breadcrumb( $trail = array() , $args = array() ) {

		global $post, $wp_query;

		$permalinks   = get_option( 'woocommerce_permalinks' );
		$shop_page_id = wc_get_page_id( 'shop' );
		$shop_page    = get_post( $shop_page_id );

		if ( AE_Helpers::is_woocommerce() ) {

			/* Set up a new items */
			$trail = array();

			// Shop Homepage
			if ( !is_shop() ) {
				$trail[] = '<a href="' . get_permalink( wc_get_page_id( 'shop' ) ) . '" title="' . get_the_title( wc_get_page_id( 'shop' ) ) . '">' . get_the_title( wc_get_page_id( 'shop' ) ) . '</a>';
			} else {
				$trail[] = get_the_title( wc_get_page_id( 'shop' ) );
			}

			/* If permalinks contain the shop page in the URI prepend the breadcrumb with shop */
			if ( $shop_page_id && strstr( $permalinks['product_base'],  '/' . $shop_page->post_name ) && get_option( 'page_on_front' ) != $shop_page_id ) {

				if ( is_product_category() || is_product_tag() || is_product() ) {
					$trail[] = '<a href="' . get_permalink( $shop_page ) . '">' . $shop_page->post_title . '</a>';
				} else {
					$trail[] = $shop_page->post_title;
				}

			}

		}

		// If is "My Account" page
		if ( is_account_page() ) {

			$my_account_page = get_option( 'woocommerce_myaccount_page_id' );

			if ( $my_account_page ) {

				// Default Page & Subscriptions
				if ( !is_wc_endpoint_url() ) {

					if( array_key_exists( 'subscriptions', $wp_query->query_vars ) ) {
						$trail[] = '<a href="' . get_permalink( $my_account_page ) . '">' . get_the_title( $my_account_page ) . '</a>';
						$trail[] = wc_get_account_menu_items()['subscriptions'];
					} else {
						$trail[] = get_the_title( $my_account_page );
					}
					
				// Subpages
				} else {

					// Append the "My Account" item.
					$trail[] = '<a href="' . get_permalink( $my_account_page ) . '">' . get_the_title( $my_account_page ) . '</a>';

					// Append the endpoint item.
					if ( $endpoint = WC()->query->get_current_endpoint() ) {

						if( $endpoint == 'edit-address' ) {

							$endhook = $wp_query->query_vars['edit-address'];

							if( $endhook == 'billing' || $endhook == 'shipping' ) {								
								$trail[] = '<a href="' . get_permalink( $my_account_page ) . '/edit-address' . '">' . WC()->query->get_endpoint_title( $endpoint ) . '</a>';
								$trail[] = ucfirst( $endhook );
							} else {
								$trail[] = WC()->query->get_endpoint_title( $endpoint );
							}

						} else {

							$trail[] = WC()->query->get_endpoint_title( $endpoint );

						}

					}

				}

			}

		}

		// If is "Cart" page
		if ( is_cart() || is_checkout() ) {

			$cart_page = get_option( 'woocommerce_cart_page_id' );

			if ( $cart_page ) {

				if ( is_cart() ) {

					$trail[] = get_the_title( $cart_page );

				} else {

					$trail[] = '<a href="' . get_permalink( $cart_page ) . '">' . get_the_title( $cart_page ) . '</a>';

				}

			}

		}

		// If is "Checkout" page
		if ( is_checkout() ) {

			$checkout_page = get_option( 'woocommerce_checkout_page_id' );

			if ( $checkout_page ) {

				$trail[] = get_the_title( $checkout_page );

			}

		}

		/* If is product category archive */
		if ( is_product_category() ) {

			$current_term = $wp_query->get_queried_object();

			$ancestors = array_reverse( get_ancestors( $current_term->term_id, 'product_cat' ) );

			foreach ( $ancestors as $ancestor ) {

				$ancestor = get_term( $ancestor, 'product_cat' );

				$trail[] = '<a href="' . get_term_link( $ancestor->slug, 'product_cat' ) . '">' . esc_html( $ancestor->name ) . '</a>';

			}

			$queried_object = $wp_query->get_queried_object();

			$trail[] = $queried_object->name;

		}

		/* If is product tag archive */
		if ( is_product_tag() ) {

			$queried_object = $wp_query->get_queried_object();

			$trail[] =  __( 'Products tagged &ldquo;', 'woocommerce' ) . $queried_object->name . '&rdquo;' ;

		}

		/* If is single product */
		if ( is_product() ) {

			if ( $terms = wc_get_product_terms( $post->ID, 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) ) ) {

				$main_term = $terms[0];

				$ancestors = get_ancestors( $main_term->term_id, 'product_cat' );

				$ancestors = array_reverse( $ancestors );

				foreach ( $ancestors as $ancestor ) {

					$ancestor = get_term( $ancestor, 'product_cat' );

					if ( ! is_wp_error( $ancestor ) && $ancestor ) {
						$trail[] = '<a href="' . get_term_link( $ancestor ) . '">' . $ancestor->name . '</a>';
					}

				}

				$trail[] = '<a href="' . get_term_link( $main_term ) . '">' . $main_term->name . '</a>';

			}

			$trail[] =  get_the_title() ;

		}

		/* Return the woocommerce breadcrumb trail items. */
		return apply_filters( 'woocommerce_core_breadcrumb_trail_items', $trail, $args );

	}

}

return new AE_Compatibility_WooCommerce();

endif;
