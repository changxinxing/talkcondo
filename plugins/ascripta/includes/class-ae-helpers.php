<?php

/**
 * AE Helpers
 *
 * @class 		AE_Helpers
 * @version		1.1.8
 * @package		AE/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Helpers' ) ) {

	class AE_Helpers {

		private static $instance = null;

		/**
		 * Instance
		 *
		 * Ensures only one instance of the plugin is loaded or can be loaded.
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;

		}

		/**
		 * Check if the current template is a blog.
		 *
		 * @return boolean
		 */
		public static function is_blog() {

			if ( self::is_blog_single() || self::is_blog_archive() ) {
				return true;
			}

			return false;

		}

		/**
		 * Check if the current template is a blog single page.
		 *
		 * @return boolean
		 */
		public static function is_blog_single() {

			if ( is_singular( 'post' ) ) {
				return true;
			}

			return false;

		}

		/**
		 * Check if the current template is a blog archive.
		 *
		 * @return boolean
		 */
		public static function is_blog_archive() {

			if( is_home() || ( is_front_page() && is_home() ) || ( is_archive() && !is_post_type_archive() && !is_tax() ) || is_search() ) {
				return true;
			}

			return false;

		}

		/**
		 * Check if the current template is a WooCommerce page.
		 *
		 * @return boolean
		 */
		public static function is_woocommerce() {

			if ( function_exists( 'is_woocommerce' ) ) {
				if ( is_woocommerce() || is_checkout() || is_order_received_page() || is_cart() || is_account_page() ) {
					return true;
				}
				return false;
			}

			return false;

		}

		/**
		 * Check if the current template is a page.
		 *
		 * @return boolean
		 */
		public static function is_page() {

			if ( !self::is_woocommerce() && !is_front_page() ) {
				return is_page();
			}

		}

	}

}

return new AE_Helpers();
