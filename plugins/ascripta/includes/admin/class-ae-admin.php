<?php

/**
 * AE Admin
 *
 * @class 		AE_Admin
 * @version		1.3.5
 * @package		AE/Admin/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Admin' ) ) {

	class AE_Admin {

		/**
		 * Construct the class.
		 */
		public function __construct() {

			// Customize the login page.
			if( AE_Admin_Settings::get_option( 'logo', 'ae_advanced_admin', 'on' ) == 'on' ) {
				add_filter( 'login_head', array( $this, 'login_head' ) );
			}

			if( is_admin() ) {

				// Register custom sections.
				add_action( 'admin_init', array( $this, 'register_custom_sections' ) );

				// Load scripts and stylesheets.
				add_action( 'admin_enqueue_scripts', array( $this, 'scripts_and_styles' ) );

				// Replace the default Wordpress footer text.
				add_filter( 'admin_footer_text', array( $this, 'admin_footer' ) );

			}

		}


		/**
		 * Register sections using custom markup.
		 */
		public function register_custom_sections() {

			/**
			 * Tools
			 *
			 * Various tools used for development and deployment.
			 */

			// Installer
			require_once ASCRIPTA_ENGINE_ADMIN_PATH . 'sections/section-tools-installer.php';

			// Retina
			require_once ASCRIPTA_ENGINE_ADMIN_PATH . 'sections/section-tools-hidpi.php';

		}


		/**
		 * Scripts and Styles
		 *
		 * Enqueues the framework scripts and stylesheets for the backend.
		 */
		public function scripts_and_styles() {

			// Stylesheets
			wp_enqueue_style( 'asc-admin', ASCRIPTA_ENGINE_CSS_URL . 'admin/asc-admin.min.css', array(), ASCRIPTA_ENGINE_VERSION, 'all' );

		}


		/**
		 * Replace the default Wordpress message in the admin dashboard footer.
		 */
		public function admin_footer() {

			echo '<span id="footer-thankyou">' . AE_Admin_Settings::get_option( 'footer', 'ae_advanced_admin', 'Proudly powered by <a href="//ascripta.com" target="_blank">Ascripta</a>.' ) . '</span>';

		}


		/**
		 * Return an image's brightness into a float value. 
		 */
		public function image_brightness( $gdHandle ) {
			$width = imagesx( $gdHandle );
			$height = imagesy( $gdHandle );

			$totalBrightness = 0;

			for ($x = 0; $x < $width; $x++) {
				for ($y = 0; $y < $height; $y++) {
					$rgb = imagecolorat($gdHandle, $x, $y);

					$red = ($rgb >> 16) & 0xFF;
					$green = ($rgb >> 8) & 0xFF;
					$blue = $rgb & 0xFF;

					$totalBrightness += (max($red, $green, $blue) + min($red, $green, $blue)) / 2;
				}
			}

			imagedestroy($gdHandle);

			return ($totalBrightness / ($width * $height)) / 2.55;
		}
		
		
		/**
		 * Get the image type by extension.
		 */
		public function get_image_type( $filename ) {
		    
            $img = getimagesize( $filename );
            
            if ( !empty( $img[2] ) ){
                return image_type_to_mime_type( $img[2] );
            }
            
            return false;
            
        }


		/**	
		 * Add custom markup to the login page head.
		 */
		public function login_head() { 

			// Get the custom logo from the Customizer.			
			$logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ) , 'full' );

			// Make sure the logo is PNG.
			if( $logo && $this->get_image_type( $logo[0] ) === 'image/png' ) {

				$logo['brightness'] = $this->image_brightness( imagecreatefrompng( $logo[0] ) ); ?>
			
				<style type="text/css">
					
					<?php 
					// If the logo is too bright, change the body background color.  
					if( $logo['brightness'] >= apply_filters( 'ascripta_login_threshold', 30.00 ) ) { ?>
					body {
						background-color: #222;
					}
					<?php } ?>

					<?php // If we are using a custom logo, change the default login one. 
					if( $logo ): ?>
					.login h1 a {
						margin-bottom: 2em;
						width: <?php echo $logo[1] . 'px'; ?>;
						height: <?php echo $logo[2] . 'px'; ?>;
						background-image: url( '<?php echo $logo[0]; ?>' );
						background-size: <?php echo $logo[1] . 'px'; ?> <?php echo $logo[2] . 'px'; ?>
					}
					<?php endif; ?>

				</style>

			<?php } ?>

		<?php }

	}

}

return new AE_Admin();
