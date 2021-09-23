<?php

/**
 * AE Sanitize
 *
 * @class 		AE_Sanitize
 * @version		1.0.0
 * @package		AE/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Sanitize' ) ) {

	class AE_Sanitize {

		/**
		 * Construct the class.
		 */
		public function __construct() {

			// Remove Default Actions
			remove_action( 'wp_head', 'feed_links_extra', 3 );
			remove_action( 'wp_head', 'feed_links', 2 );
			remove_action( 'wp_head', 'rsd_link' );
			remove_action( 'wp_head', 'wlwmanifest_link' );
			remove_action( 'wp_head', 'index_rel_link' );
			remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
			remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
			remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
			remove_action( 'wp_head', 'wp_generator' );
			remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
			remove_action( 'wp_head', 'rel_canonical' );
			remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

			// Add Cleaning Filters
			add_filter( 'the_generator', array( $this, 'remove_rss_version' ) );
			add_filter( 'wp_head', array( $this, 'remove_wp_widget_recent_comments_style' ), 1 );
			add_action( 'wp_head', array( $this, 'remove_recent_comments_style' ), 1 );
			add_filter( 'gallery_style', array( $this, 'remove_default_gallery_style' ) );

			if( AE_Admin_Settings::get_option( 'clean_versions', 'ae_advanced_tweaks', 'off' ) == 'on' ){
				add_filter( 'style_loader_src', array( $this, 'remove_wp_ver_css_js' ), 9999 );
				add_filter( 'script_loader_src', array( $this, 'remove_wp_ver_css_js' ), 9999 );
			}

			add_filter( 'the_content', array( $this, 'filter_ptags_on_images' ) );
			add_filter( 'frontpage_template', array( $this, 'front_page_template' ) );
			add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );
			if( AE_Admin_Settings::get_option( 'jquery_migrate', 'ae_advanced_scripts', 'off' ) == 'off' && !is_admin() ){
				add_filter( 'wp_default_scripts', array( $this, 'remove_jquery_migrate' ) );
			}

			// Flush rules after switching the theme.
			add_action( 'after_switch_theme', 'flush_rewrite_rules' );

		}

		/**
		 * Remove Wordpress version from RSS.
		 */
		public function remove_rss_version() {

			return '';

		}

		/**
		 * Remove injected CSS for recent comments widget.
		 */
		public function remove_wp_widget_recent_comments_style() {

			if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
				remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
			}

		}

		/**
		 * Remove injected CSS for recent comments widget.
		 */
		public function remove_recent_comments_style() {

			global $wp_widget_factory;

			if ( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
				remove_action( 'wp_head', array(
					$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
					'recent_comments_style'
				) );
			}

		}

		/**
		 * Remove injected CSS from gallery.
		 *
		 * @param string  $css The original injected gallery CSS.
		 * @return string The processed CSS.
		 */
		public function remove_default_gallery_style( $css ) {

			return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );

		}

		/**
		 * Remove Wordpress version from scripts.
		 *
		 * @param string  $src The original 'src' attribute to be processed.
		 * @return string The processed 'src' attribute.
		 */
		public function remove_wp_ver_css_js( $src ) {

			if ( strpos( $src, 'ver=' ) ) {
				$src = remove_query_arg( 'ver', $src );
			}

			return $src;

		}

		/**
		 * Remove the <p> from around <img> tags.
		 *
		 * @since 1.0.0
		 *
		 * @param string  $content The original Wordpress entry content.
		 * @return string The entry content with filtered image tags.
		 */
		public function filter_ptags_on_images( $content ) {

			return preg_replace( '/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content );

		}

		/**
		 * Bypass the 'front-page.php' template when the Latest Posts option is used.
		 *
		 * @param object  $template The front page Wordpress template.
		 * @return object Nothing if the "Latest Posts" option is used or the front-page.php if not.
		 */
		public function front_page_template( $template ) {

			return is_home() ? '' : $template;

		}

		/**
		 * Changes the Read More markup to a more convenient one.
		 *
		 * @param string  $more The original Wordpress "read more" component.
		 * @return string The modified "read more" component.
		 */
		public function excerpt_more( $more ) {

			global $post;
			return '...  <a class="excerpt-read-more" href="' . get_permalink( $post->ID ) . '" title="' . __( 'Read', 'ascripta' ) . ' ' . get_the_title( $post->ID ) . '">' . __( 'Read more &raquo;', 'ascripta' ) . '</a>';

		}

		/**
		 * Remove the jQuery migrate script from Wordpress.
		 * 
		 * @param object $scripts
		 * @return void
		 */
		public function remove_jquery_migrate( $scripts ) {
			if ( ! empty( $scripts->registered['jquery'] ) ) {
				$scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
			}
		}

	}

}

return new AE_Sanitize();
