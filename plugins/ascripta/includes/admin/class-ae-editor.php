<?php

/**
 * AE WYSIWYG Editor
 *
 * @class 		AE_Admin_Editor
 * @version		1.3.3
 * @package		AE/Admin/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Admin_Editor' ) ){

	class AE_Admin_Editor {

		/**
		 * Construct the class.
		 */

		public function __construct() {

			if( is_admin() ){

				// Hooks in the plugin file for TinyMCE as well as the buttons to the editor
				add_action( 'init', array( $this, 'tinymce_main' ) );

				// Adds the CSS required for non-standard dashicons icons and custom icons
				add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ) );

			}

		}

		/*
		 * Hooks in the plugin file for TinyMCE as well as the buttons to the editor
		 */

		public function tinymce_main() {

			// Check the current user's permissions.
			if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
				return;
			}

			// Check for the rich editing preferences.
			if ( get_user_option( 'rich_editing' ) !== 'true' ) {
				return;
			}

			// Check if WYSIWYG is enabled
			add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_buttons' ) );

		}

		/*
		 * Adds the CSS required for non-standard dashicons icons and custom icons
		 */
		 
		public function register_styles() {

			wp_enqueue_style( 'asc-editor', ASCRIPTA_ENGINE_CSS_URL . 'admin/asc-editor.min.css' );

		}

		/**
		 * Adds the TinyMCE plugin from the JavaScript file.
		 */

		public function add_tinymce_plugin( $plugin_array ) {

			$plugin_array['ae_editor_elements_button'] = ASCRIPTA_ENGINE_JS_URL . 'admin/asc-editor.min.js';

			return $plugin_array;

		}

		/*
		 * Adds the buttons defined in our plugin file to the button list on TinyMCE
		 */

		public function register_buttons( $buttons ) {

			array_push( $buttons, 'ae_editor_elements_button' );
		
			return $buttons;

		}

	}

}

return new AE_Admin_Editor();
