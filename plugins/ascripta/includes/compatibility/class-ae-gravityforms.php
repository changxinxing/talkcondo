<?php

/**
 * AE Gravity Forms
 *
 * @class 		AE_GForms
 * @version		1.2.0
 * @package		AE/Compatibility/Classes
 * @category	Class
 * @author 		Ascripta
 */

if( ! defined( 'ABSPATH' ) ){
	exit;
}

if ( !class_exists( 'AE_GForms' ) && in_array( 'gravityforms/gravityforms.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) :

class AE_GForms {

	/**
	 * Construct the class.
	 */
	public function __construct(){

		if( AE_Admin_Settings::get_option( 'gravityforms', 'ae_advanced_compatibility', 'on' ) == 'on' ){
			add_action( 'plugins_loaded', array( $this, 'initialize' ) );
		}

	}

	/**
	 * Initialize the class.
	 */
	public function initialize(){

		// Generate Bootstrap compatible form.
		add_filter( 'gform_get_form_filter', array( $this, 'form_markup' ), 10, 4 );
		add_filter( 'gform_progress_bar', array( $this, 'progress_markup' ), 10, 4 );

		// Generate Botostrap compatible fields.
		add_filter( 'gform_field_content', array( $this, 'field_markup' ), 10, 5 );

		// Prevent jumping to hash when submitting a form.
		add_filter( 'gform_confirmation_anchor', '__return_false' );

		// Enable the 'Hide Label' functionality in the form editor.
		add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );

		// Register stylesheet.
		add_action( 'gform_enqueue_scripts', array( $this, 'register_style' ) , 10, 2 );

	}

	/**
	 * Replace various Gravity Forms classes with Bootstrap equivalents.
	 *
	 * @param string  $content The form markup, including the init scripts (unless the gform_init_scripts_footer filter was used to move them to the footer).
	 * @return string The processed form markup.
	 */
	public function form_markup( $content ){

		// Validation
		$content = str_replace( '<div class=\'validation_error\'>', '<div class=\'alert alert-danger validation_error_alt\'>', $content );
		$content = str_replace( 'gfield_error', 'gfield_error has-error', $content );

		// Fields
		$content = str_replace( 'clear-multi', 'clearfix', $content );

		// Save and Continue
		$content = str_replace( 'form_saved_message', 'form_saved_message well', $content );

		// Buttons
		$content = str_replace( 'gform_button button', 'gform_button btn btn-primary', $content );
		$content = str_replace( 'gform_previous_button button', 'gform_previous_button btn btn-default', $content );
		$content = str_replace( 'gform_next_button button', 'gform_next_button btn btn-primary', $content );
		$content = str_replace( 'gform_save_link', 'gform_save_link btn btn-link', $content );

		// Footer
		$content = str_replace( 'gform_footer', 'gform_footer clearfix', $content );
		$content = str_replace( 'gform_page_footer', 'gform_page_footer clearfix', $content );

		return $content;

	}

	/**
	 * Add Bootstrap's form-control class to various field types.
	 *
	 * @param string  $content The field content to be filtered.
	 * @param object  $field   The field that this input tag applies to.
	 * @param string  $value   The default/initial value that the field should be pre-populated with.
	 * @param integer $lead_id When executed from the entry detail screen, $lead_id will be populated with the Entry ID. Otherwise, it will be 0.
	 * @param integer $form_id The current Form ID.
	 * @return string The filtered content.
	 */
	function field_markup( $content, $field, $value, $lead_id, $form_id ) {

		// Default
		if ( $field["type"] != 'hidden' && $field["type"] != 'list' && $field["type"] != 'checkbox' && $field["type"] != 'fileupload' && $field["type"] != 'date' && $field["type"] != 'html' && $field["type"] != 'address' ) {
			$content = str_replace( 'class=\'small', 'class=\'form-control small', $content );
			$content = str_replace( 'class=\'medium', 'class=\'form-control medium', $content );
			$content = str_replace( 'class=\'large', 'class=\'form-control large', $content );
		}

		// Email
		if ( $field["type"] == 'email' ) {
			$content = str_replace( '<input class', '<input class=\'form-control\'', $content );
		}

		// File Upload
		if ( $field["type"] == 'fileupload' ) {
			$content = str_replace( 'class=\'button', 'class=\'btn btn-default', $content );
		}

		// Date
		if ( $field["type"] == 'date' ) {
			$content = str_replace( 'class=\'datepicker', 'class=\'form-control datepicker ', $content );
			$content = str_replace( 'maxlength', 'class=\'form-control\' maxlength', $content );
			$content = str_replace( '<select ', '<select class=\'form-control\' ', $content );
		}

		// Name, Password, Address and List
		if ( $field["type"] == 'name' || $field["type"] == 'password' || $field["type"] == 'address' || $field["type"] == 'list' ) {
			$content = str_replace( '<input ', '<input class=\'form-control\' ', $content );
		}

		// Textarea, Post Content, Post Excerpt
		if ( $field["type"] == 'textarea' || $field["type"] == 'post_content' || $field["type"] == 'post_excerpt' ) {
			$content = str_replace( 'class=\'textarea', 'class=\'form-control textarea', $content );
		}

		// Checkbox
		if ( $field["type"] == 'checkbox' ) {
			$content = str_replace( 'li class=\'', 'li class=\'checkbox ', $content );
			$content = str_replace( '<input ', '<input style=\'margin-left:1px;\' ', $content );
		}

		// Radiobox
		if ( $field["type"] == 'radio' ) {
			$content = str_replace( 'li class=\'', 'li class=\'radio ', $content );
			$content = str_replace( '<input ', '<input style=\'margin-left:1px;\' ', $content );
		}

		// Time
		if ( $field["type"] == 'time' ) {
			$content = str_replace( '<input ', '<input class=\'form-control\' ', $content );
			$content = str_replace( '<select ', '<select class=\'form-control\' ', $content );
		}

		// Address
		if ( $field["type"] == 'address' ) {
			$content = str_replace( '<select ', '<select class=\'form-control\' ', $content );
		}

		// Credit Card
		if( $field["type"] == 'creditcard' ) {
			$content = str_replace( '<input ', '<input class=\'form-control\' ', $content );
			$content = str_replace( 'ginput_card_expiration ', 'ginput_card_expiration form-control ', $content );
		}

		return $content;

	}

	/**
	 * Replace the progress bars.
	 *
	 * @param string  $content The form markup, including the init scripts (unless the gform_init_scripts_footer filter was used to move them to the footer).
	 * @return string The processed form markup.
	 */

	public function progress_markup( $content ) {

		$content = str_replace( 'class=\'gf_progressbar\'', 'class=\'progress\'', $content );
		$content = str_replace( 'gf_progressbar_percentage', 'progress-bar', $content );
		$content = str_replace( 'percentbar_blue', '', $content );
		$content = str_replace( 'percentbar_red', 'progress-bar-danger', $content );
		$content = str_replace( 'percentbar_green', 'progress-bar-success', $content );
		$content = str_replace( 'percentbar_orange', 'progress-bar-warning', $content );
		$content = str_replace( 'percentbar_gray', 'progress-bar-gray', $content );
		$content = str_replace( 'percentbar_custom', '', $content );

		return $content;

	}

	/**
	 * Register the engine compatibility stylesheet.
	 */
	function register_style(){

		wp_enqueue_style( 'asc-gravityforms', ASCRIPTA_ENGINE_CSS_URL . 'compatibility/asc-gravityforms.min.css', array(), ASCRIPTA_ENGINE_VERSION, 'all' );

	}

}

return new AE_GForms();

endif;
