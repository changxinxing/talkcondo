<?php

/**
 * AE Jetpack
 *
 * @class 		AE_Jetpack
 * @version		1.0.0
 * @package		AE/Compatibility/Classes
 * @category	Class
 * @author 		Ascripta
 */

if( ! defined( 'ABSPATH' ) ){
	exit;
}

if( !class_exists( 'AE_Jetpack' ) ):

class AE_Jetpack {

	/**
	 * Construct the class.
	 */
	public function __construct(){

		if( AE_Admin_Settings::get_option( 'jetpack', 'ae_advanced_compatibility', 'on' ) == 'on' ){
			add_action( 'plugins_loaded', array( $this, 'initialize' ) );
		}

	}

	/**
	 * Initialize the class.
	 */
	public function initialize(){

		add_action( 'wp_enqueue_scripts', array( $this, 'register_style' ), 99 );

	}

	/**
	 * Load the required APIs to make this class work.
	 */
	public function register_style(){

		wp_enqueue_style( 'asc-jetpack', ASCRIPTA_ENGINE_CSS_URL . 'compatibility/asc-jetpack.min.css', array(), ASCRIPTA_ENGINE_VERSION, 'all' );

	}

}

return new AE_Jetpack();

endif;
