<?php

/**
 * AE Updates
 * 
 * @version		1.5.0
 * @package		AE/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Updates' ) ){

	class AE_Updates {

		private static $instance = null;
		private $updater;

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
		 * Initializes the plugin by setting localization, hooks, filters, and administrative functions.
		 */
		public function __construct() {

			// Load the required libraries, classes and option fields.
			$this->load_libraries();
			$this->load_classes();

			// Setup the plugin updater.
			$this->updater = new PluginUpdateChecker_2_0( base64_decode( 'aHR0cHM6Ly9rZXJubC51cy9hcGkvdjEvdXBkYXRlcy81NmMwZjNkMjMyMGY2YzY1MDNjOGY2MTA/Y29kZT0=' ), ASCRIPTA_ENGINE_FILE, 'ascripta', 1 );

		}

		/**
		 * Load the required libraries to make this class work.
		 */
		private function load_libraries() {

			// Load the update checker library.
			require_once ASCRIPTA_ENGINE_LIBRARIES_PATH . 'class-update-check.php';

		}

		/**
		 * Load the required classes to make this class work.
		 */
		private function load_classes() {

			require_once ASCRIPTA_ENGINE_ADMIN_PATH . 'class-ae-settings.php';

		}

	}

}

return AE_Updates::instance();
