<?php

/**
 * AE Unyson
 *
 * @class 		AE_Unyson
 * @version		1.3.0
 * @package		AE/Compatibility/Classes
 * @category	Class
 * @author 		Ascripta
 */

if( ! defined( 'ABSPATH' ) ){
	exit;
}

if( !class_exists( 'AE_Unyson' ) ):

class AE_Unyson {

	/**
	 * Construct the class.
	 */
	public function __construct(){

		if( AE_Admin_Settings::get_option( 'unyson', 'ae_advanced_compatibility', 'on' ) == 'on' ){
			
			// Disable Brizzy by default.
			add_action( 'admin_init', array( $this, '_thz_deactivate_plugins' ) );

			// Move the default extensions page under Ascripta.
			add_action( 'fw_backend_add_custom_extensions_menu', array( $this, '_action_theme_custom_fw_settings_mensu' ) );

			// Relative path of the customizations directory located in theme.
			add_filter( 'fw_framework_customizations_dir_rel_path', array( $this, '_filter_theme_fw_customizations_dir_rel_path' ) );

			// Register stylesheet.
			add_action( 'wp_enqueue_scripts', array( $this, 'register_style' ), 99 );

			// Disable the blog extension functionality.
			add_action( 'init', array( $this, '_action_disable_blog_extension_functionality' ) );

		}

	}

	/**
	 * Disable plugins
	 */
	public function _thz_deactivate_plugins() {

		deactivate_plugins(array(
			'brizy/brizy.php'
		));

	}

	/**
	 * Move the default extensions page under Ascripta.
	 * 
	 * @param array $data The data array for the extensions page.
	 * @return void
	 */
	public function _action_theme_custom_fw_settings_mensu( $data ) {
		add_submenu_page(
			'ae_engine',
			__( 'Extensions', 'ascripta' ),
			__( 'Extensions', 'ascripta' ),
			$data['capability'],
			$data['slug'],
			$data['content_callback']
		);
	}

	/**
	 * Relative path of the customizations directory located in theme.
	 * 
	 * @param string $rel_path
	 * @return string The updated framework path on the theme side.
	 */
	public function _filter_theme_fw_customizations_dir_rel_path( $rel_path ) {
		return '/library/framework';
	}

	/**
	 * Register the engine compatibility stylesheet.
	 * 
	 * @return void
	 */
	public function register_style(){
		wp_enqueue_style( 'asc-unyson', ASCRIPTA_ENGINE_CSS_URL . 'compatibility/asc-unyson.min.css', array(), ASCRIPTA_ENGINE_VERSION, 'all' );
	}

	/**
	 * Disable the blog extension functionality.
	 *
	 * @return void
	 */
	public function _action_disable_blog_extension_functionality() {
		if ( ! function_exists( 'fw_ext' ) || ( $blog = fw_ext( 'blog' ) ) == null ) {
			return;
		}

		if ( is_admin() ) {
			remove_action( 'admin_menu', array( $blog, '_admin_action_rename_post_menu' ) );
			remove_action( 'init', array( $blog, '_admin_action_change_post_labels' ), 999 );
		} else {
			remove_action( 'init', array( $blog, '_theme_action_change_post_labels' ), 999 );
		}
	}

}

return new AE_Unyson();

endif;
