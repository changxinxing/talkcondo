<?php

/**
 * AE HiDPI
 *
 * @class 		AE_HiDPI
 * @version		1.0.0
 * @package		AE/Modules/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_HiDPI' ) ) {

	class AE_HiDPI {

		/**
		 * Construct the class.
		 */
		public function __construct() {

			if ( AE_Admin_Settings::get_option( 'retina', 'ae_engine', 'on' ) == 'on' ) {

				$this->load_libraries();

				add_action( 'init', array( $this, 'initialize' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_script' ) );
				add_filter( 'wp_generate_attachment_metadata', array( $this, 'generate_metadata' ) );
				add_filter( 'delete_attachment', array( $this, 'delete_retina_attachment' ) );

				remove_image_size( 'medium_large' );
				add_filter( 'image_size_names_choose', array( $this, 'unset_medium_large' ) );
				add_filter( 'intermediate_image_sizes_advanced', array( $this, 'unset_medium_large' ) );

			}

		}

		/**
		 * Load the required libraries to make this class work.
		 */
		private function load_libraries() {

			require_once ASCRIPTA_ENGINE_LIBRARIES_PATH . '/class-html-dom.php';

		}

		/**
		 * Initialize the required actions to start the retina algorithm.
		 */
		public function initialize() {

			add_action( 'wp_head', array(
				$this,
				'buffer_start'
			) );
			add_action( 'wp_footer', array(
				$this,
				'buffer_end'
			) );

		}

		/**
		 * Unset the Wordpress ML Size in 4.4
		 *
		 * @param  array $sizes The attachment sizes.
		 * @return array The attachment sizes without the medium_large.
		 */
		public function unset_medium_large( $sizes ) {

			unset( $sizes['medium_large'] );
			return $sizes;

		}

		/**
		 * Enqueue the Picturefill library.
		 */
		public function enqueue_script() {

			wp_enqueue_script( 'picturefill', ASCRIPTA_ENGINE_INC_URL . 'picturefill/picturefill.min.js', array(), ASCRIPTA_ENGINE_VERSION, false );

		}

		/**
		 * Start the buffer by calling in the rewrite algorithm.
		 */
		public function buffer_start() {

			ob_start( array(
				$this,
				'rewrite_picture'
			) );

		}

		/**
		 * End the buffer by turning off the output.
		 */
		public function buffer_end() {

			ob_end_flush();

		}

		/**
		 * Replace all images that have a retina version uploaded with the <picture> element.
		 *
		 * @param string  $buffer The DOM content to be processed.
		 * @return string The processed DOM content.
		 */
		public function rewrite_picture( $buffer ) {

			// Create Buffer
			if ( !isset( $buffer ) || trim( $buffer ) === '' )
				return $buffer;

			// Prepare Buffer
			$html           = str_get_html( $buffer );
			$nodes_count    = 0;
			$nodes_replaced = 0;

			// Generate Picture Elements
			foreach ( $html->find( 'img' ) as $element ) {

				$nodes_count++;
				$parent = $element->parent();

				if ( $parent->tag == "picture" ) {
					continue;
				} else {
					$img_path_info    = $this->get_pathinfo_from_image_src( $element->src );
					$file_path        = trailingslashit( ABSPATH ) . $img_path_info;
					$potential_retina = $this->get_file( $img_path_info );
					$from             = substr( $element, 0 );
					if ( $potential_retina != null ) {
						$retina_url      = $this->from_system_to_url( $potential_retina );
						$img_url         = trailingslashit( get_site_url() ) . $img_path_info;
						$element->srcset = "$img_url, $retina_url 2x";
						$to              = $element;
						$buffer          = str_replace( trim( $from, "</> " ), trim( $to, "</> " ), $buffer );
						$nodes_replaced++;
					}
				}

			}

			// Return Buffer
			return $buffer;

		}

		/**
		 * Get the image pathinfo from the image 'src' attribute.
		 *
		 * @param string  $image_src The image source attribute to be processed.
		 * @return string The result pathinfo.
		 */
		public function get_pathinfo_from_image_src( $image_src ) {

			$site_url = trailingslashit( site_url() );

			if ( strpos( $image_src, $site_url ) === 0 ) {
				return substr( $image_src, strlen( $site_url ) );
			} else {
				$img_info = parse_url( $image_src );
				return ltrim( $img_info['path'], '/' );
			}

		}

		/**
		 * Get the retina file path for a certain asset.
		 *
		 * @param object  $file The file for which the retina version is scanned.
		 * @return string The retina version path for the input image.
		 */
		public function get_file( $file ) {

			$pathinfo    = pathinfo( $file );
			$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . '@2x.' . $pathinfo['extension'];

			if ( file_exists( $retina_file ) ) {
				return $retina_file;
			} else {
				return null;
			}

		}

		/**
		 * Convert a file system path to an URL.
		 *
		 * @param string  $file The file system path on the server.
		 * @return string   The URL for the file.
		 */
		public function from_system_to_url( $file ) {

			if ( empty( $file ) )
				return "";

			$retina_pathinfo = ltrim( str_replace( ABSPATH, "", $file ), '/' );
			$url             = trailingslashit( get_site_url() ) . $retina_pathinfo;

			return $url;

		}

		/**
		 * Get the available Wordpress image sizes.
		 */
		public function get_image_sizes() {

			$sizes = array();
			global $_wp_additional_image_sizes;

			foreach ( get_intermediate_image_sizes() as $s ) {
				$crop = false;
				if ( isset( $_wp_additional_image_sizes[$s] ) ) {
					$width  = intval( $_wp_additional_image_sizes[$s]['width'] );
					$height = intval( $_wp_additional_image_sizes[$s]['height'] );
					$crop   = $_wp_additional_image_sizes[$s]['crop'];
				} else {
					$width  = get_option( $s . '_size_w' );
					$height = get_option( $s . '_size_h' );
					$crop   = get_option( $s . '_crop' );
				}
				$sizes[$s] = array(
					'width' => $width,
					'height' => $height,
					'crop' => $crop
				);
			}

			unset( $sizes['medium_large'] );

			return $sizes;

		}

		/**
		 * Configurator Image Sizes
		 *
		 * Returns an array with the image sizes.
		 */
		public function get_image_sizes_array() {

			foreach ( $this->get_image_sizes() as $name => $attr ) {
				$sizes[$name] = $name;
			}

			return $sizes;

		}

		/**
		 * Generate metadata through the 'wp_generate_attachment_metadata' filter hook.
		 *
		 * @param object  $meta The original image meta object.
		 * @return object The processed image meta object.
		 */
		public function generate_metadata( $meta ) {

			// Check if the meta exists and if it is valid.
			if ( !isset( $meta ) || !isset( $meta['sizes'] ) || !isset( $meta['width'], $meta['height'] ) || !isset( $meta['file'] ) ) {
				return $meta;
			}

			// Start the retina generation process.
			global $_wp_additional_image_sizes;

			$original_file     = $meta['file'];
			$original_pathinfo = pathinfo( $original_file );
			$original_basename = $original_pathinfo['basename'];

			$uploads  = wp_upload_dir();
			$basepath = trailingslashit( $uploads['basedir'] ) . $original_pathinfo['dirname'];
			// $ignore = framework_get_option( 'exceptions', 'retina' );

			// Process Image Sizes
			$sizes = $this->get_image_sizes();
			foreach ( $sizes as $name => $attr ) {

				/*if ( $ignore != NULL ) {
				if ( in_array( $name, $ignore ) ) {
				continue;
				}
				}
				*/

				$pathinfo    = null;
				$normal_file = null;
				$retina_file = null;

				// Check for already existing files.
				if ( isset( $meta['sizes'][$name] ) && isset( $meta['sizes'][$name]['file'] ) ) {
					$normal_file = trailingslashit( $basepath ) . $meta['sizes'][$name]['file'];
					$pathinfo    = pathinfo( $normal_file );
					$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . '@2x.' . $pathinfo['extension'];
				}

				// If the retina file already exists, continue.
				if ( $retina_file && file_exists( $retina_file ) ) {
					continue;
				}

				// Generate Retina Asset
				if ( $retina_file ) {
					$originalfile = trailingslashit( $pathinfo['dirname'] ) . $original_basename;

					// If the original file doesn't exist, return the $meta.
					if ( !file_exists( $originalfile ) ) {
						return $meta;
					}

					// If the original image and the retina image have the exactly same size, create a copy of it.
					if ( $meta['sizes'][$name]['width'] * 2 == $meta['width'] && $meta['sizes'][$name]['height'] * 2 == $meta['height'] ) {
						copy( $originalfile, $retina_file );
					}

					// Otherwise let's resize (if the original size is big enough).
					else if ( $this->check_dimensions( $meta['width'], $meta['height'], $meta['sizes'][$name]['width'] * 2, $meta['sizes'][$name]['height'] * 2 ) ) {

						// Change proposed by Nicscott01, slighlty modified by Jordy (+isset)
						// https://wordpress.org/support/topic/issue-with-crop-position?replies=4#post-6200271
						$crop        = isset( $_wp_additional_image_sizes[$name] ) ? $_wp_additional_image_sizes[$name]['crop'] : true;
						$crom_custom = null;

						// Support for Manual Image Crop
						// If the size of the image was manually cropped, let's keep it.
						if ( class_exists( 'ManualImageCrop' ) && isset( $meta['micSelectedArea'] ) && isset( $meta['micSelectedArea'][$name] ) && isset( $meta['micSelectedArea'][$name]['scale'] ) ) {
							$crom_custom = $meta['micSelectedArea'][$name];
						}

						// Resize Image to Retina
						$image = $this->retina_resize( $originalfile, $meta['sizes'][$name]['width'] * 2, $meta['sizes'][$name]['height'] * 2, $crop, $retina_file, $crom_custom );

					}

				}

			}

			return $meta;

		}

		/**
		 * Compare two images dimensions (resolutions) against each while accepting a margin error.
		 *
		 * @param integer $width         The non-retina image width.
		 * @param integer $height        The non-retina image height.
		 * @param integer $retina_width  The retina image width.
		 * @param integer $retina_height The retina image height.
		 * @return bool Returns true if the dimensions are ok or false if they are not.
		 */
		public function check_dimensions( $width, $height, $retina_width, $retina_height ) {

			$w_margin = $width - $retina_width;
			$h_margin = $height - $retina_height;
			return $w_margin >= -2 && $h_margin >= -2;

		}

		/**
		 * Resize the input file and procesess various settings like crop and quality.
		 *
		 * @param object  $file_path The image file object.
		 * @param integer $width     The width of the image.
		 * @param integer $height    The height of the image.
		 * @param integer $crop      The crop parameters.
		 * @param object  $file_new  The new image file object.
		 * @param boolean [$crop_custom = false] The custom crop enabler.
		 * @return array The url, width and height of the resized image.
		 */
		public function retina_resize( $file_path, $width, $height, $crop, $file_new, $crop_custom = false ) {

			// Set Required Variables
			$crop_params         = $crop == '1' ? true : $crop;
			$image_size_original = getimagesize( $file_path );
			$image_src[0]        = $file_path;
			$image_src[1]        = $image_size_original[0];
			$image_src[2]        = $image_size_original[1];
			$file_info           = pathinfo( $file_path );
			$file_info_new       = pathinfo( $file_new );
			$extension           = '.' . $file_info_new['extension'];
			$no_ext_path         = $file_info['dirname'] . '/' . $file_info['filename'];
			$cropped_img_path    = $no_ext_path . '-' . $width . 'x' . $height . "-tmp" . $extension;
			$image               = wp_get_image_editor( $file_path );

			// Resize or use Custom Crop
			if ( !$crop_custom ) {
				$image->resize( $width, $height, $crop_params );
			} else {
				$image->crop( $crop_custom['x'] * $crop_custom['scale'], $crop_custom['y'] * $crop_custom['scale'], $crop_custom['w'] * $crop_custom['scale'], $crop_custom['h'] * $crop_custom['scale'], $width, $height, false );
			}

			// Image Quality
			$quality = 90;
			$image->set_quality( intval( $quality ) );

			// Save the processed image to the attachment folder.
			$saved = $image->save( $cropped_img_path );
			if ( rename( $saved['path'], $file_new ) ) {
				$cropped_img_path = $file_new;
			}
			$image_size = getimagesize( $cropped_img_path );
			$image_url  = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );

			// Return Values
			return array(
				'url' => $image_url,
				'width' => $image_size[0],
				'height' => $image_size[1]
			);

		}

		/**
		 * Delete retina images from the media library.
		 *
		 * @param integer $attachment_id The image attachment id.
		 */
		public function delete_retina_attachment( $attachment_id ) {

			$meta       = wp_get_attachment_metadata( $attachment_id );
			$upload_dir = wp_upload_dir();

			if ( isset( $meta['file'] ) ) {

				$path = pathinfo( $meta['file'] );

				foreach ( $meta as $key => $value ) {
					if ( 'sizes' === $key ) {
						foreach ( $value as $sizes => $size ) {
							$original_filename = $upload_dir['basedir'] . '/' . $path['dirname'] . '/' . $size['file'];
							$retina_filename   = substr_replace( $original_filename, '@2x.', strrpos( $original_filename, '.' ), strlen( '.' ) );
							if ( file_exists( $retina_filename ) )
								unlink( $retina_filename );
						}
					}
				}

			}

		}

	}

}

return new AE_HiDPI();
