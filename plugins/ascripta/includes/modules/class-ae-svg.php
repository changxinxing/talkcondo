<?php

/**
 * AE SVG Support
 *
 * @class 		AE_SVG_Support
 * @version		1.4.0
 * @package		AE/Modules/Classes
 * @category	Class
 * @author 		Ascripta
 */

if( !in_array( 'svg-support/svg-support.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){

	class AE_SVG_Support {

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

			if( AE_Admin_Settings::get_option( 'svg', 'ae_engine', 'on' ) == 'on' ){

				// Add the svg mime types to the Wordpress upload mimes.
				add_filter( 'upload_mimes', array( $this, 'svg_upload_mimes' ) );

				// Attachment media response for SVG assets
				add_filter( 'wp_prepare_attachment_for_js', array( $this, 'media_response' ), 10, 3 );

				// Automatically insert the .svg class to the matching assets.
				add_filter( 'image_send_to_editor', array( $this, 'auto_insert_class' ), 10 );

			}

		}

		/**
		 * Mime Types
		 * 
		 * Add the svg mime types to the Wordpress upload mimes.
		 */

		public function svg_upload_mimes( $mimes = array() ) {

			if ( current_user_can( 'administrator' ) ) {

				$mimes['svg'] = 'image/svg+xml';
				$mimes['svgz'] = 'image/svg+xml';

				return $mimes;

			} else {

				return $mimes;

			}

		}


		/**
		 * Media Response
		 * 
		 * Attachment media response for SVG assets.
		 */

		public function media_response( $response, $attachment, $meta ) {

			if ( $response['mime'] == 'image/svg+xml' && empty( $response['sizes'] ) ) {

				$svg = get_attached_file( $attachment->ID );

				if ( !file_exists( $svg ) ) {
					$svg = $response[ 'url' ];
				}

				$dimensions = $this->get_dimensions( $svg );

				$response[ 'sizes' ] = array(
					'full' => array(
						'url' => $response[ 'url' ],
						'width' => $dimensions->width,
						'height' => $dimensions->height,
						'orientation' => $dimensions->width > $dimensions->height ? 'landscape' : 'portrait'
					) 
				);

			}

			return $response;

		}


		/**
		 * Get Dimensions
		 * 
		 * Return the width and height of a svg.
		 */

		public function get_dimensions( $svg ) {

			$svg = simplexml_load_file( $svg );

			if ( $svg === FALSE ) {

				$width = '0';
				$height = '0';

			} else {

				$attributes = $svg->attributes();
				$width = (string) $attributes->width;
				$height = (string) $attributes->height;
			}

			return (object) array( 'width' => $width, 'height' => $height );

		}


		/**
		 * Insert Class
		 * 
		 * Automatically insert the .svg class to the matching assets.
		 */

		public function auto_insert_class( $html, $alt = '' ) {

			if ( strpos( $html, '.svg' ) !== FALSE ) {
				$html = str_replace( 'class="', 'class="svg ', $html );
			}

			return $html;

		}

	}

	return new AE_SVG_Support;

}