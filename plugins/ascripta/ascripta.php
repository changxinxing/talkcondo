<?php

/*
	Plugin Name: Ascripta
	Plugin URI: http://ascripta.com
	Description: Ascripta is a premium framework used to accurately develop high-end Wordpress software.
	Version: 1.5.0
	Author: Ascripta
	Author URI: http://ascripta.com
	License: GPL-3.0
*/

if( ! defined( 'ABSPATH' ) ){
	exit;
}

if( ! class_exists('AE_Plugin') ) :

class AE_Plugin {

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
	 * Setup the plugin, define constants and require libraries.
	 */
	public function __construct() {

		// Define the constants.
		$this->constants();

		// Require the includes.
		$this->includes();

		// Load the scripts and stylesheets.
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts_and_styles' ) );

		// Load the translations.
		load_plugin_textdomain( 'ascripta', false, ASCRIPTA_ENGINE_PATH . 'languages' );

	}

	/**
	 * Constants
	 *
	 * Declare constants to be used across the framework.
	 */
	private function constants() {

		define( 'ASCRIPTA_ENGINE_EXISTS'             , true );
		define( 'ASCRIPTA_ENGINE_VERSION'            , '1.5.0' );
		define( 'ASCRIPTA_ENGINE_FILE'               , __FILE__ );
		define( 'ASCRIPTA_ENGINE_BASE'               , plugin_basename( ASCRIPTA_ENGINE_FILE ) );

		/*
		 * Assets
		 */

		define( 'ASCRIPTA_ENGINE_URL'                , plugin_dir_url( ASCRIPTA_ENGINE_FILE ) );
		define( 'ASCRIPTA_ENGINE_ASSETS_URL'         , ASCRIPTA_ENGINE_URL . 'assets/' );
		define( 'ASCRIPTA_ENGINE_CSS_URL'            , ASCRIPTA_ENGINE_ASSETS_URL . 'css/' );
		define( 'ASCRIPTA_ENGINE_INC_URL'            , ASCRIPTA_ENGINE_ASSETS_URL . 'inc/' );
		define( 'ASCRIPTA_ENGINE_JS_URL'             , ASCRIPTA_ENGINE_ASSETS_URL . 'js/' );
		define( 'ASCRIPTA_ENGINE_SASS_URL'           , ASCRIPTA_ENGINE_ASSETS_URL . 'sass/' );

		/*
		 * Includes
		 */

		define( 'ASCRIPTA_ENGINE_PATH'               , realpath( plugin_dir_path( ASCRIPTA_ENGINE_FILE ) ) . '/' );
		define( 'ASCRIPTA_ENGINE_INC_PATH'           , realpath( ASCRIPTA_ENGINE_PATH . 'includes/' ) . '/' );
		define( 'ASCRIPTA_ENGINE_ADMIN_PATH'         , realpath( ASCRIPTA_ENGINE_INC_PATH . 'admin/' ) . '/' );
		define( 'ASCRIPTA_ENGINE_COMPATIBILITY_PATH' , realpath( ASCRIPTA_ENGINE_INC_PATH . 'compatibility/' ) . '/' );
		define( 'ASCRIPTA_ENGINE_FUNCTIONS_PATH'     , realpath( ASCRIPTA_ENGINE_INC_PATH . 'functions/' ) . '/' );
		define( 'ASCRIPTA_ENGINE_LIBRARIES_PATH'     , realpath( ASCRIPTA_ENGINE_INC_PATH . 'libraries/' ) . '/' );
		define( 'ASCRIPTA_ENGINE_MODULES_PATH'       , realpath( ASCRIPTA_ENGINE_INC_PATH . 'modules/' ) . '/' );

		/*
		 * Theme
		 */

		define( 'ASCRIPTA_PARENT_THEME_PATH'         , get_template_directory() );
		define( 'ASCRIPTA_CHILD_THEME_PATH'          , get_stylesheet_directory() );

	}

	/**
	 * Includes
	 *
	 * Load the required classes to make the framework work.
	 */
	private function includes() {

		// Utilities
		include_once( ASCRIPTA_ENGINE_FUNCTIONS_PATH . 'func-ae-utils.php' );

		// Updates
		include_once( ASCRIPTA_ENGINE_INC_PATH . 'class-ae-updates.php' );

		// Framework
		include_once( ASCRIPTA_ENGINE_INC_PATH . 'class-ae-sanitize.php' );
		include_once( ASCRIPTA_ENGINE_INC_PATH . 'class-ae-structure.php' );
		include_once( ASCRIPTA_ENGINE_INC_PATH . 'class-ae-components.php' );
		include_once( ASCRIPTA_ENGINE_INC_PATH . 'class-ae-helpers.php' );
		include_once( ASCRIPTA_ENGINE_INC_PATH . 'class-ae-development.php' );

		// Compatibility
		include_once( ASCRIPTA_ENGINE_COMPATIBILITY_PATH . 'class-ae-bootstrap.php' );
		include_once( ASCRIPTA_ENGINE_COMPATIBILITY_PATH . 'class-ae-gravityforms.php' );
		include_once( ASCRIPTA_ENGINE_COMPATIBILITY_PATH . 'class-ae-woocommerce.php' );
		include_once( ASCRIPTA_ENGINE_COMPATIBILITY_PATH . 'class-ae-jetpack.php' );
		include_once( ASCRIPTA_ENGINE_COMPATIBILITY_PATH . 'class-ae-unyson.php' );

		// Administration
		include_once( ASCRIPTA_ENGINE_ADMIN_PATH . 'class-ae-settings.php' );
		include_once( ASCRIPTA_ENGINE_ADMIN_PATH . 'class-ae-admin.php' );
		include_once( ASCRIPTA_ENGINE_ADMIN_PATH . 'class-ae-customizer.php' );
		include_once( ASCRIPTA_ENGINE_ADMIN_PATH . 'class-ae-plugins.php' );
		include_once( ASCRIPTA_ENGINE_ADMIN_PATH . 'class-ae-fields.php' );
		include_once( ASCRIPTA_ENGINE_ADMIN_PATH . 'class-ae-types.php' );
		include_once( ASCRIPTA_ENGINE_ADMIN_PATH . 'class-ae-editor.php' );

		// Modules
		include_once( ASCRIPTA_ENGINE_MODULES_PATH . 'class-ae-hidpi.php' );
		include_once( ASCRIPTA_ENGINE_MODULES_PATH . 'class-ae-autocomplete.php' );
		include_once( ASCRIPTA_ENGINE_MODULES_PATH . 'class-ae-svg.php' );
		include_once( ASCRIPTA_ENGINE_MODULES_PATH . 'class-ae-ssl.php' );
		include_once( ASCRIPTA_ENGINE_MODULES_PATH . 'class-ae-maintenance.php' );

	}

	/**
	 * Scripts and Styles
	 *
	 * Enqueues the framework scripts and stylesheets for the frontend.
	 */
	public function scripts_and_styles() {

		// Modernizr
		if( AE_Admin_Settings::get_option( 'modernizr', 'ae_advanced_scripts', 'on' ) == 'on' ){
			wp_enqueue_script( 'modernizr', ASCRIPTA_ENGINE_INC_URL . 'modernizr/modernizr.min.js', array(), '3.3.1', false );
		}

		// Slick
		if( AE_Admin_Settings::get_option( 'slick', 'ae_advanced_scripts', 'on' ) == 'on' ){
			wp_enqueue_script( 'slick', ASCRIPTA_ENGINE_INC_URL . 'slick/js/slick.min.js', array('jquery'), '1.6.0', true );
			wp_enqueue_style( 'slick', ASCRIPTA_ENGINE_INC_URL . 'slick/css/slick.min.css', array(), '1.6.0', 'all' );
		}

		// Font Awesome
		if( AE_Admin_Settings::get_option( 'font_awesome', 'ae_advanced_scripts', 'on' ) == 'on' ){
			wp_enqueue_style( 'font-awesome', ASCRIPTA_ENGINE_INC_URL . 'font-awesome/css/font-awesome.min.css', array(), '4.7.0', 'all' );
		}

		// Framework
		if( AE_Admin_Settings::get_option( 'default', 'ae_advanced_scripts', 'on' ) == 'on' ){
			wp_enqueue_script( 'ascripta', ASCRIPTA_ENGINE_JS_URL . 'asc-framework.min.js', array('jquery'), ASCRIPTA_ENGINE_VERSION, true );
			wp_enqueue_style( 'ascripta', ASCRIPTA_ENGINE_CSS_URL . 'asc-framework.min.css', array(), ASCRIPTA_ENGINE_VERSION, 'all' );
		}

		// Comment reply script for threaded comments
		if ( AE_Admin_Settings::get_option( 'comments', 'ae_advanced_scripts', 'on' ) == 'on' && is_singular() and comments_open() and ( get_option( 'thread_comments' ) == 1 ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// jQuery
		if( AE_Admin_Settings::get_option( 'jquery', 'ae_advanced_scripts', 'on' ) == 'on' ){
			wp_enqueue_script( 'jquery' );
		}

	}

}

return AE_Plugin::get_instance();

endif; // class_exists check
