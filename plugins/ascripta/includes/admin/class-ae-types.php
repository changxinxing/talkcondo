<?php

/**
 * AE Custom Post Types
 *
 * @class 		AE_Admin_Types
 * @version		1.0.0
 * @package		AE/Admin/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Admin_Types' ) ){

	class AE_Admin_Types {

		/**
		 * Construct the class.
		 */
		public function __construct() {

			add_action( 'after_setup_theme', array( $this, 'register_post_types' ) );

		}

		/**
		 * Search for custom post types in the theme library and load them.
		 */
		public function register_post_types() {

			$path = get_template_directory() . '/library/types';

			if ( file_exists( $path ) ) {
				flush_rewrite_rules();

				foreach ( scandir( $path ) as $filename ) {
					$path_file = $path . '/' . $filename;
					if ( is_file( $path_file ) ) {
						include_once $path_file;
					}
				}
			}

		}

	}

}

return new AE_Admin_Types();
