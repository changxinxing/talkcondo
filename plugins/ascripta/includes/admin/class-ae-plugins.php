<?php

/**
 * AE Admin Plugins
 *
 * @class 		AE_Admin_Plugins
 * @version		1.2.0
 * @package		AE/Admin/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Admin_Plugins' ) ) {

	class AE_Admin_Plugins {

		/**
		 * Construct the class.
		 */
		public function __construct() {

			if( is_admin() ){

				// Advanced Mode
				if ( AE_Admin_Settings::get_option( 'client', 'ae_engine', 'off' ) == 'on' ) {

					add_action( 'plugins_loaded', array( $this, 'plugin_filters' ) );

					// Protect various required plugins.
					add_filter( 'plugin_action_links', array( $this, 'protect' ), 10, 4 );

					// Hide pages and subpages from the admin area.
					add_action( 'admin_menu', array( $this, 'hide_pages' ), 999 );

					// Add the extra action links.
					add_filter( 'plugin_action_links_stream/stream.php', array( $this, 'action_links_stream' ) );

				}

				// Add the extra action links.
				add_filter( 'plugin_action_links_' . ASCRIPTA_ENGINE_BASE, array( $this, 'action_links' ) );
				add_filter( 'plugin_action_links_unyson/unyson.php', array( $this, 'action_links_unyson' ) );
				add_filter( 'plugin_action_links_wp-rocket/wp-rocket.php', array( $this, 'action_links_wprocket' ) );
				add_filter( 'plugin_action_links_all-in-one-wp-migration/all-in-one-wp-migration.php', array( $this, 'action_links_aiomigration' ) );

				// Enable the white label rewriting.
				add_action( 'admin_init', array( $this, 'white_label' ) );

				// Execute pre-plugin filters.
				add_filter( 'wp_stream_admin_menu_title',    function(){ return esc_html__( 'Logs', 'ascripta' ); } );
				add_filter( 'wp_stream_admin_page_title',    function(){ return esc_html__( 'Activity Logs', 'ascripta' ); } );
				add_filter( 'wp_stream_settings_form_title', function(){ return esc_html__( 'Activity Settings', 'ascripta' ); } );
				add_filter( 'wp_stream_menu_position',       function(){ return 100; } );
				
			}

		}

		/**
		 * Add a link to the settings page on the plugins list.
		 *
		 * @param  array $defaults The default Wordpress links.
		 * @return array The default links combined with the extras.
		 */
		public function action_links( $defaults ) {

			$links = array(
				'<a href="' . admin_url( 'admin.php?page=ae_tools' ) . '">Tools</a>',
				'<a href="' . admin_url( 'admin.php?page=ae_engine' ) . '">Settings</a>'
			);

			foreach ( $links as $link ) {
				array_unshift( $defaults, $link );
			}

			return $defaults;

		}

		/**	
		 * Add the original name to Unyson
		 *
		 * @param  array $defaults The default plugin links.
		 * @return array The updated plugin links. 
		 */

		public function action_links_unyson( $defaults ) {

			$links = array(
				'<a href="' . admin_url( 'admin.php?page=fw-extensions' ) . '">Settings</a>',
				'<span style="color: #bbb">' . __( 'Unyson', 'ascripta' ) . '</span>'
			);

			foreach ( $links as $link ) {
				array_unshift( $defaults, $link );
			}

			return $defaults;

		}

		/**	
		 * Add the original name to WP Rocket
		 *
		 * @param  array $defaults The default plugin links.
		 * @return array The updated plugin links. 
		 */

		public function action_links_wprocket( $defaults ) {

			array_unshift( $defaults, '<span style="color: #bbb">' . __( 'WP Rocket', 'ascripta' ) . '</span>' );

			return $defaults;

		}

		/**	
		 * Add the original name to AIO Migration
		 *
		 * @param  array $defaults The default plugin links.
		 * @return array The updated plugin links. 
		 */

		public function action_links_aiomigration( $defaults ) {

			$links = array(
				'<span style="color: #bbb">' . __( 'All in One Migration', 'ascripta' ) . '</span>'
			);

			foreach ( $links as $link ) {
				array_unshift( $defaults, $link );
			}

			return $defaults;

		}

		/**	
		 * Remove the uninstall link from Stream.
		 *
		 * @param  array $defaults The default plugin links.
		 * @return array The updated plugin links. 
		 */

		public function action_links_stream( $defaults ) {

			unset( $defaults[1] );
			
			return $defaults;

		}

		/**
		 * Filter certain plugin functions.
		 */
		public function plugin_filters() {

			// Hide ACF from the admin area.
			if ( class_exists( 'ACF' ) ) {
				add_filter( 'acf/settings/show_admin', '__return_false' );
			}

		}

		/**
		 * Protect various plugins from being deactivated.
		 *
		 * @param  object $actions     The original action links.
		 * @param  object $plugin_file The plugin file.
		 * @param  object $plugin_data The plugin data.
		 * @param  object $context     The plugin context.
		 * @return object The processed action links.
		 */
		public function protect( $actions, $plugin_file, $plugin_data, $context ) {

			$plugins = array(
				ASCRIPTA_ENGINE_BASE,
				'gravityforms/gravityforms.php',
				'advanced-custom-fields-pro/acf.php',
				'stream/stream.php',
				'unyson/unyson.php'
			);

			if ( in_array( $plugin_file, $plugins ) ) {

				if ( array_key_exists( 'deactivate', $actions ) ) {

					// Add the protection message.
					add_filter( 'plugin_action_links_' . plugin_basename( $plugin_file ), array(
						$this,
						'protect_message'
					) );

					// Remove the deactivate action link.
					unset( $actions['deactivate'] );

				}

			}

			return $actions;

		}

		/**
		 * Hide subpages from the admin area.
		 */
		public function hide_pages() {

			// Pages
			$page = remove_menu_page( 'wp_stream' );

			// Subpages
			$page = remove_submenu_page( 'themes.php', 'theme-editor.php' );
			$page = remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
			$page = remove_submenu_page( 'options-general.php', 'performance' );

		}

		/**
		 * Point out which plugins are protected in the admin area.
		 *
		 * @param  array $links The original list of actions.
		 * @return array The processed list of actions.
		 */
		public function protect_message( $links ) {

			array_unshift( $links, '<span style="color: #bbb">' . __( 'Required', 'ascripta' ) . '</span>' );

			return $links;

		}

		/**
		 * Rename and tweak various plugins for standardized output.
		 *
		 * @version 1.1.2
		 */
		public function white_label(){

			/*
			 * Apply the white label names to the plugins screen.
			 */
			add_filter( 'all_plugins', array( $this, 'white_label_plugins' ) );

		}

		/**
		 * Check if a plugin is installed, whether active or inactive.
		 *
		 * @param  string $plugin The plugin to be checked.
		 * @return boolean  Whether it is installed or not.
		 */
		static public function is_installed_plugin( $plugin ) {

			return ( file_exists( WP_CONTENT_DIR . '/plugins/' . $plugin ) ) ? true : false;

		}

		/**
		 * Rename various plugins on the Wordpress plugins page.
		 *
		 * @version 1.1.2
		 */
		public function white_label_plugins( $plugins ){

			// Unyson
			if( $this->is_installed_plugin( '/unyson/unyson.php' ) ){
				$plugins['unyson/unyson.php']['Name'] = 'Extensions';
				$plugins['unyson/unyson.php']['Description'] = 'A set of various extensions used to implement special functionality to the framework.';
			}

			// Gravity Forms
			if( $this->is_installed_plugin( '/gravityforms/gravityforms.php' ) ){
				$plugins['gravityforms/gravityforms.php']['Author'] = 'Carl Hancock';
			}

			// Advanced Custom Fields
			if( $this->is_installed_plugin( '/advanced-custom-fields-pro/acf.php' ) ){
				$plugins['advanced-custom-fields-pro/acf.php']['Name'] = 'Advanced Custom Fields';
				$plugins['advanced-custom-fields-pro/acf.php']['Author'] = 'Elliot Condon';
			}

			// Advanced Custom Fields: Theme Code
			if( $this->is_installed_plugin( '/acf-theme-code-pro/acf_theme_code_pro.php' ) ){
				$plugins['acf-theme-code-pro/acf_theme_code_pro.php']['Name'] = 'Advanced Custom Fields: Theme Code';
				$plugins['acf-theme-code-pro/acf_theme_code_pro.php']['Author'] = 'Hookturn';
			}

			// Stream
			if( $this->is_installed_plugin( '/stream/stream.php' ) ){
				$plugins['stream/stream.php']['Description'] = 'Planes have a black box, WordPress has Stream. When something goes wrong, you need to know how it happened.';
			}

			// WP Rocket
			if( $this->is_installed_plugin( '/wp-rocket/wp-rocket.php' ) ){
				$plugins['wp-rocket/wp-rocket.php']['Name'] = 'Performance';
			}

			// All in One Migration
			if( $this->is_installed_plugin( '/all-in-one-wp-migration/all-in-one-wp-migration.php' ) ){
				$plugins['all-in-one-wp-migration/all-in-one-wp-migration.php']['Name'] = 'Migration';
			}

			return $plugins;

		}

	}

}

return new AE_Admin_Plugins();
