<?php

/**
 * AE Admin Fields
 *
 * @class 		AE_Admin_Fields
 * @version		1.4.12
 * @package		AE/Admin/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Admin_Fields' ) ){

	class AE_Admin_Fields {

		private $path = null;

		/**
		 * Construct the class.
		 */
		public function __construct() {

			// Initialize after the theme is setup.
			add_action( 'after_setup_theme', array( $this, 'setup' ) );

		}
		

		/**
		 * Load the required libraries to make this class work.
		 */
		private function load_libraries() {

			// Load the helper functions.
			require_once ASCRIPTA_ENGINE_FUNCTIONS_PATH . 'func-ae-fields.php';

		}


		/**
		 * Setup the fields functionality layer.
		 */

		public function setup() {
			
			// Load the required libraries, classes and option fields.
			$this->load_libraries();

			if ( class_exists( 'ACF' ) ) {

				// Hook to the ACF save and load endpoints.
				$this->json_setup();

				// Connect the framework fields.
				$this->fields();

			}

		}


		/**
		 * Setup ACF to process data using JSON.
		 */
		public function json_setup() {

			// Set the path based on the theme type, child or parent.
			if ( ASCRIPTA_CHILD_THEME_PATH !== ASCRIPTA_PARENT_THEME_PATH ) {
				$this->path = ASCRIPTA_CHILD_THEME_PATH . '/library/fields';
			} elseif( current_theme_supports( 'ascripta-custom-theme' ) ) {
				$this->path = ASCRIPTA_PARENT_THEME_PATH . '/library/fields';
			} else {
				$this->path = WP_CONTENT_DIR . '/fields';
			}

			// If the storage folder does not exist, create it.
			if ( !file_exists( $this->path ) ) {
				mkdir( $this->path, 0777, true );
			}

			// Save to JSON
			add_filter( 'acf/settings/save_json', array( $this, 'save' ) );

			// Load from JSON
			add_filter( 'acf/settings/load_json', array( $this, 'load' ) );

		}

		/**
		 * JSON Save Point
		 *
		 * Store the ACF fields in a theme folder.
		 *
		 * @since 1.0.0
		 *
		 * @param string  $path The path where fields will be saved.
		 * @return string The processed path.
		 */
		public function save( $path ) {

			$path = $this->path;

			return $path;

		}

		/**
		 * JSON Load Point
		 *
		 * Load the ACF fields from a theme folder.
		 *
		 * @since 1.0.0
		 *
		 * @param array   $paths The array of paths where fields will be loaded from.
		 * @return array The processed array of paths.
		 */
		public function load( $paths ) {

			unset( $paths[0] );

			$paths[] = $this->path;

			return $paths;

		}

		/**
		 * Load Framework Fields
		 */

		public function fields() {

			if( function_exists('acf_add_local_field_group') ):

				acf_add_local_field_group(array (
					'key' => 'group_5847fc46bb846',
					'title' => 'Settings',
					'fields' => array (
						array (
							'key' => 'field_5847fd8503452',
							'label' => 'Layout',
							'name' => 'entry_layout_single',
							'type' => 'select',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array (
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'choices' => array (
								'default'       => 'Default',
								'left-sidebar'  => 'Left Sidebar',
								'right-sidebar' => 'Right Sidebar',
								'full-width'    => 'Full Width',
							),
							'default_value' => array (
								0 => 'default',
							),
							'allow_null' => 0,
							'multiple' => 0,
							'ui' => 0,
							'ajax' => 0,
							'placeholder' => '',
							'disabled' => 0,
							'readonly' => 0,
						),
					),
					'location' => array (
						array (
							array (
								'param' => 'post_type',
								'operator' => '==',
								'value' => 'post',
							),
						),
						array (
							array (
								'param' => 'post_type',
								'operator' => '==',
								'value' => 'page',
							),
							array (
								'param' => 'page_template',
								'operator' => '==',
								'value' => 'default',
							),
						),
					),
					'menu_order' => 0,
					'position' => 'side',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => 1,
					'description' => '',
				));

			endif;

		}


	}

}

return new AE_Admin_Fields();
