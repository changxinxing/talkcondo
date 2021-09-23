<?php

/**
 * AE Development
 *
 * @class 		AE_Development
 * @version		1.0.0
 * @package		AE/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Development' ) ){

	class AE_Development {

		/**
		 * Construct the class.
		 */
		public function __construct() {

			// Client Mode
			if ( AE_Admin_Settings::get_option( 'development', 'ae_advanced', 'on' ) == 'on' ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
			}

		}

		/**
		 * Register styles used for theme development.
		 */
		public function register_scripts() {

			wp_enqueue_script( 'holder', ASCRIPTA_ENGINE_INC_URL . 'holder/holder.min.js', array(), '2.9.0', true );

		}

	}

}

return new AE_Development();
