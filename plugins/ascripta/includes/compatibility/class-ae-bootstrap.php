<?php

/**
 * AE Bootstrap
 *
 * @class 		AE_Bootstrap
 * @version		1.0.0
 * @package		AE/Compatibility/Classes
 * @category	Class
 * @author 		Ascripta
 */

if( ! defined( 'ABSPATH' ) ){
	exit;
}

if( !class_exists( 'AE_Bootstrap' ) ):

class AE_Bootstrap {

	/**
	 * Construct the class.
	 */
	public function __construct(){

		add_action( 'init', array( $this, 'initialize' ) );

	}

	/**
	 * Load the required libraries to make this class work.
	 */
	public function initialize(){

		if( AE_Admin_Settings::get_option( 'bootstrap', 'ae_advanced_compatibility', 'on' ) == 'on' ){
			$this->load_libraries();
		}

	}

	/**
	 * Load the required libraries to make this class work.
	 */
	public function load_libraries(){

		if( defined( 'THEME_CURRENT_VERSION' ) && version_compare( THEME_CURRENT_VERSION, '1.2.2', '<' ) ) {
			require_once ASCRIPTA_ENGINE_LIBRARIES_PATH . 'class-bootstrap-navwalker.php';
		}

	}

}

return new AE_Bootstrap();

endif;
