<?php

/**
 * AE Admin Settings
 *
 * @class 		AE_Admin_Settings
 * @version		1.4.2
 * @package		AE/Admin/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Admin_Settings' ) ){

	class AE_Admin_Settings {

		private static $instance = null;
		public $api;

		/**
		 * Instance
		 *
		 * Ensures only one instance of the plugin is loaded or can be loaded.
		 */
		public static function instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;

		}

		/**
		 * Construct the class.
		 */
		public function __construct() {

			$this->load_libraries();
			$this->api = new WeDevs_Settings_API();

			// Register settings pages.
			add_action( 'admin_menu', array( $this, 'register_pages' ) );

			// Register sections and fields.
			add_action( 'admin_init', array( $this, 'register_sections_and_fields' ) );

		}

		/**
		 * Load the required libraries to make this class work.
		 */
		private function load_libraries() {

			// Load the settings API.
			require_once ASCRIPTA_ENGINE_LIBRARIES_PATH . 'class-settings-api.php';

		}

		/**
		 * Register the options page for the plugin.
		 */
		public function register_pages() {

			if ( is_admin() ) {

				// Register the framework menu page.
				add_menu_page( __( 'Ascripta', 'ascripta' ), 'Ascripta', 'manage_options', 'ae_engine', null, 'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2aWV3Qm94PSIwIDAgMjAgMTcuOTUiPjxkZWZzPjxzdHlsZT4uY2xzLTF7ZmlsbDpub25lO30uY2xzLTJ7Y2xpcC1wYXRoOnVybCgjY2xpcC1wYXRoKTt9LmNscy0ze2ZpbGw6IzllYTNhODt9PC9zdHlsZT48Y2xpcFBhdGggaWQ9ImNsaXAtcGF0aCI+PHJlY3QgaWQ9Il9DbGlwcGluZ19QYXRoXyIgZGF0YS1uYW1lPSImbHQ7Q2xpcHBpbmcgUGF0aCZndDsiIGNsYXNzPSJjbHMtMSIgd2lkdGg9IjIwIiBoZWlnaHQ9IjE3Ljk1Ii8+PC9jbGlwUGF0aD48L2RlZnM+PHRpdGxlPmljb248L3RpdGxlPjxnIGlkPSJfR3JvdXBfIiBkYXRhLW5hbWU9IiZsdDtHcm91cCZndDsiPjxnIGlkPSJPcmlnYW1pIj48ZyBjbGFzcz0iY2xzLTIiPjxwb2x5Z29uIGlkPSJfUGF0aF8iIGRhdGEtbmFtZT0iJmx0O1BhdGgmZ3Q7IiBjbGFzcz0iY2xzLTMiIHBvaW50cz0iNy4zMyAwIDAgMCA3LjMzIDYuMzcgNy4zMyAwIi8+PHBvbHlnb24gaWQ9Il9QYXRoXzIiIGRhdGEtbmFtZT0iJmx0O1BhdGgmZ3Q7IiBjbGFzcz0iY2xzLTMiIHBvaW50cz0iMTYuNjcgNC4wNSAyMCAxLjE2IDE2LjY3IDEuMTYgMTYuNjcgNC4wNSIvPjxwb2x5Z29uIGlkPSJfUGF0aF8zIiBkYXRhLW5hbWU9IiZsdDtQYXRoJmd0OyIgY2xhc3M9ImNscy0zIiBwb2ludHM9IjE2IDQuNjMgMTYgMS4xNiAxMi44MSAzLjkzIDE2IDYuNyAxNiA0LjYzIi8+PHBvbHlnb24gaWQ9Il9QYXRoXzQiIGRhdGEtbmFtZT0iJmx0O1BhdGgmZ3Q7IiBjbGFzcz0iY2xzLTMiIHBvaW50cz0iMTIuMzQgNC4zNCAxMi4zMyA0LjM0IDggMC41OCA4IDE0LjQ4IDE2IDcuNTMgMTYgNy41MiAxMi4zNCA0LjM0Ii8+PHBvbHlnb24gaWQ9Il9QYXRoXzUiIGRhdGEtbmFtZT0iJmx0O1BhdGgmZ3Q7IiBjbGFzcz0iY2xzLTMiIHBvaW50cz0iNy4zMyAxNC4yMSA3LjMzIDguNjkgNC4xNSAxMS40NSA3LjMzIDE0LjIxIi8+PHBvbHlnb24gaWQ9Il9QYXRoXzYiIGRhdGEtbmFtZT0iJmx0O1BhdGgmZ3Q7IiBjbGFzcz0iY2xzLTMiIHBvaW50cz0iNCAxMi4xNCA0IDE3Ljk1IDcuMzMgMTUuMDYgNy4zMyAxNS4wMyA0IDEyLjE0Ii8+PC9nPjwvZz48L2c+PC9zdmc+', 100 );

				// Register the "General" submenu page.
				add_submenu_page( 'ae_engine', 'General', 'General', 'manage_options', 'ae_engine', array(
					$this,
					'register_subpage'
				) );

				// Register the "Tools" submenu page.
				add_submenu_page( 'ae_engine', 'Tools', 'Tools', 'manage_options', 'ae_tools', array(
					$this,
					'register_subpage'
				));

				// Register advanced submenu pages.
				if ( $this->get_option( 'client', 'ae_engine', 'off' ) == 'off' ) {

					// Register the "Advanced" submenu page.
					add_submenu_page( 'ae_engine', 'Advanced', 'Advanced', 'manage_options', 'ae_advanced', array(
						$this,
						'register_subpage'
					));
					
				}

			}

		}

		/**
		 * Display a submenu page.
		 */
		public function register_subpage() {

			echo '<div class="wrap">';

				echo '<h2>' . get_admin_page_title() . '</h2>';

				$this->api->show_navigation();
				$this->api->show_forms();

			echo '</div>';

		}

		/**
		 * Register the framework settings section and fields.
		 */
		public function register_sections_and_fields() {

			// Sections
			$sections = array(

				/**
				 * General
				 *
				 * Various settings that can be adjusted by the user.
				 */

				array(
					'id' => 'ae_engine',
					'title' => 'General',
					'page' => 'ae_engine'
				),

				/**
				 * Advanced
				 *
				 * Manage advanced functionality like dependencies and compatibility layers.
				 */

				// Compatibility
				array(
					'id' => 'ae_advanced_compatibility',
					'title' => __( 'Compatibility', 'ascripta' ),
					'page' => 'ae_advanced'
				),

				// Scripts
				array(
					'id' => 'ae_advanced_scripts',
					'title' => __( 'Libraries', 'ascripta' ),
					'page' => 'ae_advanced'
				),

				// Tweaks
				array(
					'id' => 'ae_advanced_tweaks',
					'title' => __( 'Tweaks', 'ascripta' ),
					'page' => 'ae_advanced'
				),

				// Administration
				array(
					'id' => 'ae_advanced_admin',
					'title' => __( 'Dashboard', 'ascripta' ),
					'page' => 'ae_advanced'
				),

				/**
				 * Tools
				 *
				 * Various tools used for development and deployment.
				 */

				array(
					'id' => 'ae_tools_maintenance',
					'title' => __( 'Maintenance', 'ascripta' ),
					'page' => 'ae_tools'
				),

				array(
					'id' => 'ae_tools_installer',
					'title' => __( 'Installer', 'ascripta' ),
					'page' => 'ae_tools'
				),

				array(
					'id' => 'ae_tools_retina',
					'title' => __( 'Sharpener', 'ascripta' ),
					'page' => 'ae_tools'
				),

			);

			// Fields
			$fields = array(

				/**
				 * General
				 *
				 * Various settings that can be adjusted by the user.
				 */

				'ae_engine' => array(
					array(
						'name' => 'ae_engine_autocomplete',
						'label' => __( 'Autocomplete', 'ascripta' ),
						'desc' => __( 'Enable predictive results for the default Wordpress search.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_engine_svg',
						'label' => __( 'SVG Support', 'ascripta' ),
						'desc' => __( 'Enable SVG support and upload capabilities to administrators.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_engine_ssl',
						'label' => __( 'SSL Support', 'ascripta' ),
						'desc' => __( 'Enable SSL support and force traffic through HTTPS. <br><small>Note: You must enter the HTTPS version of your URL under Settings > General.</small>', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'off'
					),
					array(
						'name' => 'ae_engine_client',
						'label' => __( 'Client Mode', 'ascripta' ),
						'desc' => __( 'Prevent access to the framework advanced settings.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'off'
					),
				),
				
				/**
				 * Tools
				 *
				 * Various tools used for development and deployment.
				 */

				// Maintenance
				'ae_tools_maintenance' => array(
					array(
						'name' => 'ae_tools_maintenance_mode',
						'label' => __( 'Maintenance Mode', 'ascripta' ),
						'desc' => __( 'Enable maintenance mode to prevent public access.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'off'
					),
					array(
						'name' => 'ae_tools_maintenance_title',
						'label' => __( 'Title', 'ascripta' ),
						'desc' => __( 'Enter the title for the header area.', 'ascripta' ),
						'type' => 'text',
						'default' => esc_html__( 'Temporarily Down for Maintenance', 'ascripta' )
					),
					array(
						'name' => 'ae_tools_maintenance_content',
						'label' => __( 'Content', 'ascripta' ),
						'desc' => __( 'Enter the paragraph text for the area below the title.', 'ascripta' ),
						'type' => 'text',
						'default' => esc_html__( 'We are performing scheduled maintenance and will be back online shortly!', 'ascripta' ),
					),
					array(
						'name' => 'ae_tools_maintenance_logo',
						'label' => __( 'Show Logo', 'ascripta' ),
						'desc' => __( 'Insert the site logo in the maintenance page header.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_tools_maintenance_social',
						'label' => __( 'Show Social Icons', 'ascripta' ),
						'desc' => __( 'Insert the social media sites in the maintenance page footer.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_tools_maintenance_style',
						'label' => __( 'Style', 'ascripta' ),
						'desc' => __( 'Select the style used for the maintenance page.', 'ascripta' ),
						'options' => array(
							'light' => __( 'Light', 'ascripta' ),
							'dark'  => __( 'Dark', 'ascripta' )
						),
						'type' => 'radio',
						'default' => 'light'
					),
				),

				/**
				 * Advanced
				 *
				 * Manage advanced functionality like dependencies and compatibility layers.
				 */

				// Compatibility
				'ae_advanced_compatibility' => array(
					array(
						'name' => 'ae_advanced_compatibility_bootstrap',
						'label' => __( 'Bootstrap', 'ascripta' ),
						'desc' => __( 'Enable the Bootstrap compatibility layer.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_advanced_compatibility_gravityforms',
						'label' => __( 'Gravity Forms', 'ascripta' ),
						'desc' => __( 'Enable the Gravity Forms compatibility layer.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_advanced_compatibility_woocommerce',
						'label' => __( 'WooCommerce', 'ascripta' ),
						'desc' => __( 'Enable the WooCommerce compatibility layer.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_advanced_compatibility_jetpack',
						'label' => __( 'Jetpack', 'ascripta' ),
						'desc' => __( 'Enable the Jetpack compatibility layer.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_advanced_compatibility_unyson',
						'label' => __( 'Unyson', 'ascripta' ),
						'desc' => __( 'Enable the Unyson compatibility layer.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					)
				),

				// Tweaks
				'ae_advanced_tweaks' => array(
					array(
						'name' => 'ae_advanced_tweaks_clean_versions',
						'label' => __( 'Asset Version Cleaning', 'ascripta' ),
						'desc' => __( 'Remove the version number from CSS/JS asset URLs.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'off'
					),
				),

				// Scripts
				'ae_advanced_scripts' => array(
					array(
						'name' => 'ae_advanced_scripts_default',
						'label' => __( 'Framework', 'ascripta' ),
						'desc' => __( 'Enable the default framework optimizations.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_advanced_scripts_comments',
						'label' => __( 'Comments Reply', 'ascripta' ),
						'desc' => __( 'Enable the comments reply functionality.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_advanced_scripts_slick',
						'label' => __( 'Slick', 'ascripta' ),
						'desc' => __( 'Enqueue the Slick library.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_advanced_scripts_font_awesome',
						'label' => __( 'Font Awesome', 'ascripta' ),
						'desc' => __( 'Enqueue the Font Awesome library.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_advanced_scripts_modernizr',
						'label' => __( 'Modernizr', 'ascripta' ),
						'desc' => __( 'Enqueue the Modernizr library.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_advanced_scripts_jquery',
						'label' => __( 'jQuery', 'ascripta' ),
						'desc' => __( 'Enqueue the version of jQuery included with Wordpress by default.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_advanced_scripts_jquery_migrate',
						'label' => __( 'jQuery Migrate', 'ascripta' ),
						'desc' => __( 'Enqueue the version of jQuery Migrate included with Wordpress by default.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'off'
					),
				),

				// Administration
				'ae_advanced_admin' => array(
					array(
						'name' => 'ae_advanced_admin_logo',
						'label' => __( 'Login Logo', 'ascripta' ),
						'desc' => __( 'Replace the default login logo with the site logo.', 'ascripta' ),
						'type' => 'checkbox',
						'default' => 'on'
					),
					array(
						'name' => 'ae_advanced_admin_footer',
						'label' => __( 'Footer Text', 'ascripta' ),
						'desc' => __( 'Replace the default admin footer text.', 'ascripta' ),
						'type' => 'text',
						'default' => 'Proudly powered by <a href="//ascripta.com" target="_blank">Ascripta</a>.'
					)
				)

			);

			// Initialize
			$this->api->set_sections( $sections );
			$this->api->set_fields( $fields );
			$this->api->admin_init();

		}


		/**
		 * Get the value of a settings field
		 *
		 * @param string  $option  settings field name
		 * @param string  $section the section name this field belongs to
		 * @param string  $default default text if it's not found
		 * @return mixed
		 */
		public static function get_option( $option, $section, $default = '' ) {

			$options = get_option( $section );

			if ( isset( $options[$section . '_' . $option] ) ) {
				return $options[$section . '_' . $option];
			}

			return $default;

		}

	}

}

return AE_Admin_Settings::instance();
