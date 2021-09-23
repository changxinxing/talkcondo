<?php

/**
 * AE Admin Customizer
 *
 * @class 		AE_Admin_Customizer
 * @version		1.1.9
 * @package		AE/Admin/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Admin_Customizer' ) ){

	class AE_Admin_Customizer {

		private static $instance = null;

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

			add_action( 'after_setup_theme', array( $this, 'initialize' ) );

		}

		/**
		 * Initialize the class.
		 */
		public function initialize(){

			add_action( 'customize_register', array( $this, 'controls' ) );

		}

		/**
		 * Add extra fields to the WP Customizer using the default API.
		 *
		 * @param object  $wp_customize An instance of the WP_Customize_Manager, the class object that controls the Theme Customizer screen.
		 */
		public function controls( $wp_customize ) {

			/*
			 * General
			 *
			 * Add the logo control to the default Wordpress General section.
			 */
			
			// Add the site logo field for older framework versions.
			if( version_compare( THEME_CURRENT_VERSION, '1.1.9', '<' ) ) {

				// Add Controls
				$controls[] = array(
					'type' => 'image',
					'setting' => 'logo',
					'label' => __( 'Site Logo', 'ascripta' ),
					'description' => "The site logo represents a graphic representation of your company name, trademark, etc.",
					'section' => 'title_tagline',
					'priority' => 20
				);

			}

			/*
			 * Entries
			 *
			 * Add the blog post layout and components related fields.
			 */

			// Add Section
			$wp_customize->add_section( 'section_posts', array(
				'priority' => 101,
				'title' => __( 'Posts', 'ascripta' ),
				'description' => __( 'This panel changes various settings for blog entries.', 'ascripta' ),
				'active_callback' => 'AE_Helpers::is_blog_single'
			) );

			// Add Controls
			$controls[] = array(
				'type' => 'checkbox',
				'setting' => 'post_breadcrumb',
				'label' => __( 'Enable Breadcrumb', 'ascripta' ),
				'section' => 'section_posts',
				'default' => 1,
				'priority' => 10
			);
			$controls[] = array(
				'type' => 'checkbox',
				'setting' => 'post_featured',
				'label' => __( 'Enable Featured Image', 'ascripta' ),
				'section' => 'section_posts',
				'default' => 1,
				'priority' => 10
			);
			$controls[] = array(
				'type' => 'checkbox',
				'setting' => 'post_author_box',
				'label' => __( 'Enable Author Box', 'ascripta' ),
				'section' => 'section_posts',
				'default' => 1,
				'priority' => 10
			);
			$controls[] = array(
				'type' => 'checkbox',
				'setting' => 'post_pager',
				'label' => __( 'Enable Pager', 'ascripta' ),
				'section' => 'section_posts',
				'default' => 1,
				'priority' => 10
			);
			$controls[] = array(
				'type' => 'checkbox',
				'setting' => 'post_related',
				'label' => __( 'Enable Related Posts', 'ascripta' ),
				'section' => 'section_posts',
				'default' => 1,
				'priority' => 10
			);
			$controls[] = array(
				'type' => 'checkbox',
				'setting' => 'post_comments',
				'label' => __( 'Enable Comments', 'ascripta' ),
				'section' => 'section_posts',
				'default' => 1,
				'priority' => 10
			);
			$controls[] = array(
				'type' => 'radio',
				'setting' => 'post_layout',
				'label' => __( 'Layout', 'ascripta' ),
				'description' => __( 'The layout type for the current template.', 'ascripta' ),
				'section' => 'section_posts',
				'default' => 'right-sidebar',
				'priority' => 10,
				'choices' => array(
					'left-sidebar' => __( 'Left Sidebar', 'ascripta' ),
					'right-sidebar' => __( 'Right Sidebar', 'ascripta' ),
					'full-width' => __( 'Full Width', 'ascripta' )
				)
			);
			$controls[] = array(
				'type' => 'select',
				'setting' => 'post_layout_width',
				'label' => __( 'Layout Width', 'ascripta' ),
				'description' => __( 'The width of the content area.', 'ascripta' ),
				'section' => 'section_posts',
				'default' => 8,
				'priority' => 10,
				'choices' => array(
					6 => __( 'Small', 'ascripta' ),
					7 => __( 'Medium', 'ascripta' ),
					8 => __( 'Large', 'ascripta' ),
				)
			);
			$controls[] = array(
				'type' => 'select',
				'setting' => 'post_breakpoint',
				'label' => __( 'Breakpoint', 'ascripta' ),
				'description' => __( 'The point where the sidebar moves below the content.', 'ascripta' ),
				'section' => 'section_posts',
				'default' => 'md',
				'priority' => 10,
				'choices' => array(
					'sm' => __( 'Mobile', 'ascripta' ),
					'md' => __( 'Tablet', 'ascripta' ),
					'lg' => __( 'Desktop', 'ascripta' ),
				)
			);


			/*
			 * Pages
			 *
			 * Add the page layout and components related fields.
			 */

			// Add Section
			$wp_customize->add_section( 'section_pages', array(
				'priority' => 101,
				'title' => __( 'Pages', 'ascripta' ),
				'description' => __( 'This panel changes various settings for pages.', 'ascripta' ),
				'active_callback' => 'AE_Helpers::is_page'
			) );

			// Add Controls
			$controls[] = array(
				'type' => 'checkbox',
				'setting' => 'page_breadcrumb',
				'label' => __( 'Enable Breadcrumb', 'ascripta' ),
				'section' => 'section_pages',
				'default' => 1,
				'priority' => 10
			);
			$controls[] = array(
				'type' => 'checkbox',
				'setting' => 'page_featured',
				'label' => __( 'Enable Featured Image', 'ascripta' ),
				'section' => 'section_pages',
				'default' => 1,
				'priority' => 10
			);
			$controls[] = array(
				'type' => 'checkbox',
				'setting' => 'page_comments',
				'label' => __( 'Enable Comments', 'ascripta' ),
				'section' => 'section_pages',
				'default' => 0,
				'priority' => 10
			);
			$controls[] = array(
				'type' => 'radio',
				'setting' => 'page_layout',
				'label' => __( 'Layout', 'ascripta' ),
				'description' => __( 'The layout type for the current template.', 'ascripta' ),
				'section' => 'section_pages',
				'default' => 'right-sidebar',
				'priority' => 10,
				'choices' => array(
					'left-sidebar' => __( 'Left Sidebar', 'ascripta' ),
					'right-sidebar' => __( 'Right Sidebar', 'ascripta' ),
					'full-width' => __( 'Full Width', 'ascripta' )
				)
			);
			$controls[] = array(
				'type' => 'select',
				'setting' => 'page_layout_width',
				'label' => __( 'Layout Width', 'ascripta' ),
				'description' => __( 'The width of the content area.', 'ascripta' ),
				'section' => 'section_pages',
				'default' => 8,
				'priority' => 10,
				'choices' => array(
					6 => __( 'Small', 'ascripta' ),
					7 => __( 'Medium', 'ascripta' ),
					8 => __( 'Large', 'ascripta' ),
				)
			);
			$controls[] = array(
				'type' => 'select',
				'setting' => 'page_breakpoint',
				'label' => __( 'Breakpoint', 'ascripta' ),
				'description' => __( 'The point where the sidebar moves below the content.', 'ascripta' ),
				'section' => 'section_pages',
				'default' => 'md',
				'priority' => 10,
				'choices' => array(
					'sm' => __( 'Mobile', 'ascripta' ),
					'md' => __( 'Tablet', 'ascripta' ),
					'lg' => __( 'Desktop', 'ascripta' ),
				)
			);


			/*
			 * Archives
			 *
			 * Add the blog archives layout and components related fields.
			 */

			// Add Section
			$wp_customize->add_section( 'section_archives', array(
				'priority' => 101,
				'title' => __( 'Archives', 'ascripta' ),
				'description' => __( 'This panel changes various settings for blog archives.', 'ascripta' ),
				'active_callback' => 'AE_Helpers::is_blog_archive',
			) );

			// Add Controls
			$controls[] = array(
				'type' => 'checkbox',
				'setting' => 'archive_breadcrumb',
				'label' => __( 'Enable Breadcrumb', 'ascripta' ),
				'section' => 'section_archives',
				'default' => 1,
				'priority' => 10,
			);
			$controls[] = array(
				'type' => 'checkbox',
				'setting' => 'archive_featured',
				'label' => __( 'Enable Featured Image', 'ascripta' ),
				'section' => 'section_archives',
				'default' => 1,
				'priority' => 10
			);
			$controls[] = array(
				'type' => 'radio',
				'setting' => 'archive_layout',
				'label' => __( 'Layout', 'ascripta' ),
				'description' => __( 'The layout type for the current template.', 'ascripta' ),
				'section' => 'section_archives',
				'default' => 'right-sidebar',
				'priority' => 10,
				'choices' => array(
					'left-sidebar' => __( 'Left Sidebar', 'ascripta' ),
					'right-sidebar' => __( 'Right Sidebar', 'ascripta' ),
					'full-width' => __( 'Full Width', 'ascripta' )
				)
			);
			$controls[] = array(
				'type' => 'select',
				'setting' => 'archive_layout_width',
				'label' => __( 'Layout Width', 'ascripta' ),
				'description' => __( 'The width of the content area.', 'ascripta' ),
				'section' => 'section_archives',
				'default' => 8,
				'priority' => 10,
				'choices' => array(
					6 => __( 'Small', 'ascripta' ),
					7 => __( 'Medium', 'ascripta' ),
					8 => __( 'Large', 'ascripta' ),
				)
			);
			$controls[] = array(
				'type' => 'select',
				'setting' => 'archive_breakpoint',
				'label' => __( 'Breakpoint', 'ascripta' ),
				'description' => __( 'The point where the sidebar moves below the content.', 'ascripta' ),
				'section' => 'section_archives',
				'default' => 'md',
				'priority' => 10,
				'choices' => array(
					'sm' => __( 'Mobile', 'ascripta' ),
					'md' => __( 'Tablet', 'ascripta' ),
					'lg' => __( 'Desktop', 'ascripta' ),
				)
			);


			/*
			 * Contact Details
			 *
			 * Add contact information such as name, email, phone, etc.
			 */

			// Add Panel
			$wp_customize->add_panel( 'panel_contact', array(
				'priority' => 120,
				'title' => __( 'Contact', 'ascripta' ),
				'description' => __( 'This panel contains contact information.', 'ascripta' )
			) );

			// Add Sections and Controls
			foreach ( array(
				'primary',
				'secondary',
				'third',
				'fourth'
			) as $name ) {

				// Add Section
				$wp_customize->add_section( 'section_' . $name . '_contact', array(
					'title' => ucfirst( $name ),
					'priority' => 10,
					'panel' => 'panel_contact',
					'description' => __( 'This information is shown across the site and is used to contact you.', 'ascripta' )
				) );

				// Add Controls
				$controls[] = array(
					'type' => 'text',
					'setting' => $name . '_contact_name',
					'label' => __( 'Name', 'ascripta' ),
					'section' => 'section_' . $name . '_contact',
					'priority' => 10
				);
				$controls[] = array(
					'type' => 'text',
					'setting' => $name . '_contact_email',
					'label' => __( 'Email Address', 'ascripta' ),
					'section' => 'section_' . $name . '_contact',
					'priority' => 10
				);
				$controls[] = array(
					'type' => 'text',
					'setting' => $name . '_contact_phone',
					'label' => __( 'Phone Number', 'ascripta' ),
					'section' => 'section_' . $name . '_contact',
					'priority' => 10
				);
				$controls[] = array(
					'type' => 'text',
					'setting' => $name . '_contact_fax',
					'label' => __( 'Fax Number', 'ascripta' ),
					'section' => 'section_' . $name . '_contact',
					'priority' => 10
				);
				if( version_compare( THEME_CURRENT_VERSION, '1.4.2', '<' ) ) {
					$controls[] = array(
						'type' => 'text',
						'setting' => $name . '_contact_address_line_1',
						'label' => __( 'Address Line 1', 'ascripta' ),
						'section' => 'section_' . $name . '_contact',
						'priority' => 10
					);
					$controls[] = array(
						'type' => 'text',
						'setting' => $name . '_contact_address_line_2',
						'label' => __( 'Address Line 2', 'ascripta' ),
						'section' => 'section_' . $name . '_contact',
						'priority' => 10
					);
				} else {
					$controls[] = array(
						'type' => 'text',
						'setting' => $name . '_contact_street',
						'label' => __( 'Street Address', 'ascripta' ),
						'section' => 'section_' . $name . '_contact',
						'priority' => 10
					);
					$controls[] = array(
						'type' => 'text',
						'setting' => $name . '_contact_city',
						'label' => __( 'City', 'ascripta' ),
						'section' => 'section_' . $name . '_contact',
						'priority' => 10
					);
					$controls[] = array(
						'type' => 'text',
						'setting' => $name . '_contact_state',
						'label' => __( 'State', 'ascripta' ),
						'section' => 'section_' . $name . '_contact',
						'priority' => 10
					);
				}
				$controls[] = array(
					'type' => 'text',
					'setting' => $name . '_contact_zip',
					'label' => __( 'Zip Code', 'ascripta' ),
					'section' => 'section_' . $name . '_contact',
					'priority' => 10
				);
				$controls[] = array(
					'type' => 'text',
					'setting' => $name . '_contact_map_url',
					'label' => __( 'Map URL', 'ascripta' ),
					'section' => 'section_' . $name . '_contact',
					'priority' => 10
				);
				$controls[] = array(
					'type' => 'text',
					'setting' => $name . '_contact_map_embed',
					'label' => __( 'Map Embed', 'ascripta' ),
					'section' => 'section_' . $name . '_contact',
					'priority' => 10
				);
				$controls[] = array(
					'type' => 'textarea',
					'setting' => $name . '_contact_hours',
					'label' => __( 'Opening Hours', 'ascripta' ),
					'section' => 'section_' . $name . '_contact',
					'priority' => 10
				);

			}

			/*
			 * Social Networks
			 *
			 * Add URL fields for the most common social networks.
			 */

			// Add Section
			$wp_customize->add_section( 'section_social_networks', array(
				'title' => __( 'Social Networks', 'ascripta' ),
				'priority' => 10,
				'panel' => 'panel_contact',
				'description' => __( 'The following URL addresses are used to link to your social networks across the site.', 'ascripta' )
			) );

			// Add Controls
			foreach ( $this->social() as $network ) {
				$controls[] = array(
					'type' => 'text',
					'setting' => 'social_' . $network['id'],
					'label' => sprintf( __( '%1$s', 'ascripta' ), ucwords( str_replace( '_', ' ', $network['id'] ) ) ),
					'section' => 'section_social_networks',
					'default' => $network['url'],
					'priority' => 10
				);
			}

			// Register controls
			$this->register( $wp_customize, $controls );

			// Rename default sections
			$wp_customize->get_section('title_tagline')->title = 'General';
			$wp_customize->get_section('static_front_page')->title = 'Front Page';

			return $wp_customize;

		}

		/**
		 * Processes an array of custom controls and generates their setting equivalents.
		 *
		 * @param object  $wp_customize An instance of the WP_Customize_Manager, the class object that controls the Theme Customizer screen.
		 * @param array   $controls     The array of controls to be processed.
		 */
		public function register( $wp_customize, $controls ) {

			foreach ( $controls as $control ) {

				// If key exists but it's empty
				if ( !isset( $control['default'] ) )
					$control['default'] = null;

				// Handle Registration
				switch ( $control['type'] ) {

					case 'image':

						$wp_customize->add_setting( $control['setting'] );
						$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $control['setting'], $control ) );
						break;

					default:

						$wp_customize->add_setting( $control['setting'], array( 'default' => $control['default'] ) );
						$wp_customize->add_control( $control['setting'], $control );
						break;

				}

			}

		}

		/**
		 * Returns the array of supported social media sites.
		 */
		public function social() {

			return array(
				array(
					'id' => 'facebook',
					'url' => 'https://facebook.com/'
				),
				array(
					'id' => 'twitter',
					'url' => 'https://twitter.com/'
				),
				array(
					'id' => 'google_plus',
					'url' => 'https://plus.google.com/'
				),
				array(
					'id' => 'linkedin',
					'url' => 'https://linkedin.com/'
				),
				array(
					'id' => 'pinterest',
					'url' => 'https://pinterest.com/'
				),
				array(
					'id' => 'instagram',
					'url' => 'https://instagram.com/'
				),
				array(
					'id' => 'yelp',
					'url' => 'https://yelp.com/'
				),
				array(
					'id' => 'youtube',
					'url' => 'https://youtube.com/'
				),
				array(
					'id' => 'houzz',
					'url' => 'https://houzz.com/'
				),
				array(
					'id' => 'soundcloud',
					'url' => 'https://soundcloud.com/'
				),
				array(
					'id' => 'tumblr',
					'url' => 'https://tumblr.com/'
				)
			);

		}

	}

}

return AE_Admin_Customizer::instance();
