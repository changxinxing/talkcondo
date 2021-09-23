<?php

/**
 * AE Admin Tools Installer
 *
 * @class 		AE_Maintenance
 * @version		1.4.9
 * @package		AE/Admin/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Maintenance' ) ) {

	class AE_Maintenance {

		private $fonts;
		private $settings, $customizer;

		/**
		 * Construct the class.
		 */
		public function __construct() {

			// Inherit the other class instances.
			$this->settings = AE_Admin_Settings::instance();
			$this->customizer = AE_Admin_Customizer::instance();
				
			// Enable the maintenance mode.
			if ( $this->settings->get_option( 'mode', 'ae_tools_maintenance', 'off' ) == 'on' ) {
				add_action( 'get_header', array( $this, 'render' ) );
			}

		}

		/**
		 * Render the maintenance page markup.
		 */
		public function render(){
			
			if( !is_user_logged_in() && !current_user_can( 'edit_themes' ) ){
				wp_die(
					'<section>
						<header>' . $this->logo() . $this->title()     . '</header>
						<div>'    . $this->content() . $this->social() . '</div>
						<footer>' . $this->fonts() . $this->styles()   . '</footer>
					</section>
				');
			}
		}

		/**
		 * Render the logo in the header.
		 */
		public function logo() {

			if( $this->settings->get_option( 'logo', 'ae_tools_maintenance', 'on' ) == 'on' ) {
				return get_custom_logo();
			}

		}

		/**
		 * Render the title in the header.
		 */
		public function title() {

			return '<h1>' . $this->settings->get_option( 'title', 'ae_tools_maintenance', esc_html__( 'Temporarily Down for Maintenance', 'ascripta' ) ) . '</h1>';

		}

		/**
		 * Render the content area below the header.
		 */
		public function content() {

			return '<p>' . $this->settings->get_option( 'content', 'ae_tools_maintenance', esc_html__( 'We are performing scheduled maintenance and will be back online shortly!', 'ascripta' ) ) . '</p>';
			
		}

		/**
		 * Render the social icons in the footer.
		 */
		public function social() {

			ob_start();

				if( $this->settings->get_option( 'social', 'ae_tools_maintenance', 'on' ) == 'on' ) { ?>

				<ul>

					<?php foreach ( $this->customizer->social() as $network ) {
						
						if( $network['url'] = get_theme_mod( 'social_' . $network['id'] ) ) { ?>

							<li>
								<a href="<?php echo $network['url']; ?>" target="_blank">
									<i class="fab fa-<?php echo $network['id']; ?>"></i>
								</a>
							</li>

						<?php }

					} ?>

				</ul>

			<?php }

			$markup = ob_get_contents(); ob_end_clean();

			return $markup;

		}

		/**
		 * Render the maintenance page styles.
		 */
		public function styles() {

			ob_start(); ?>
		
			<style>
				body {
					text-align: center;
				}
				h1 {
					padding-bottom: 1em;
					margin-bottom: 1em;
				}
				ul {
					padding-left: 0;
					margin-top: 1.5rem;
					margin-bottom: 0;
				}
				ul > li {
					list-style: none;
					font-size: 1.5rem;
					display: inline-block;
					margin: 0 0.75rem;
				}
				a {
					color: #666;
				}
				a:hover,
				a:focus {
					color: #333;
				}
				#error-page {
					margin-top: 0;
					position: absolute;
					top: 50%;
					left: 50%;
					width: 70%;
					transform: translate( -50%, -50% );
				}
				#error-page p {
					margin-top: 0;
				}
			</style>

			<?php 
			
			if( $this->settings->get_option( 'style', 'ae_tools_maintenance', 'light' ) == 'light' ){
				$this->styles_light();
			} else {
				$this->styles_dark();
			}
			
			$markup = ob_get_contents(); ob_end_clean();

			return $markup;
			
		}

		/**
		 * Render the maintenance page styles.
		 */
		public function styles_light() { ?>
		
			<style>
				a {
					color: #666;
				}
				a:hover,
				a:focus {
					color: #333;
				}
			</style>

		<?php }

		/**
		 * Render the maintenance page styles.
		 */
		public function styles_dark() { ?>
		
			<style>
				html {
					background: #333;
				}
				body {
					background: #444;
    				box-shadow: none;
					color: white;
				}
				h1 {
					color: white;
					border-color: #666;
				}
				a {
					color: #888;
				}
				a:hover,
				a:focus {
					color: #fff;
				}
			</style>
	
		<?php }

		/**
		 * Render the font libraries.
		 */
		public function fonts() {

			return '
				<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/brands.css">
				<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/fontawesome.css">
			';

		}

	}

}

return new AE_Maintenance();
