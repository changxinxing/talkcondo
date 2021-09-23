<?php

/**
 * AE SSL Support
 *
 * @class 		AE_SSL_Support
 * @version		1.4.0
 * @package		AE/Modules/Classes
 * @category	Class
 * @author 		Ascripta
 */

class AE_SSL_Support {

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
	 * Constructor
	 *
	 * Initialize the framework using the class functions.
	 */
	public function __construct() {

		if( AE_Admin_Settings::get_option( 'ssl', 'ae_engine', 'off' ) == 'on' && version_compare( PHP_VERSION, '5.4', '>=' ) ){

			define( 'FORCE_SSL', true );

			if ( defined('FORCE_SSL') ) {
				add_action( 'template_redirect', array( $this, 'force_ssl' ) );
			}

		}

	}

	/**
	 * Force SSL
	 * 
	 * Redirect all traffic via HTTPs.
	 */

	public function force_ssl() {

		if ( FORCE_SSL && !is_ssl() ) {
			wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301);
            exit();
		}

	}

}

return new AE_SSL_Support;