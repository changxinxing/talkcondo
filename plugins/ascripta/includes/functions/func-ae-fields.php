<?php

/**
 * AE Fields
 *
 * @version		1.1.9
 * @package		AE/Functions
 * @category	Helper
 * @author 		Ascripta
 */

if( !function_exists('get_image_field') ){

	/**
	 * Returns markup for a responsive image.
	 *
	 * @param  integrer $id   The ID of the ACF field.
	 * @param  array    $args The array of settings for the returned object.
	 * @return string         The generated responsive image markup.
	 */

	function get_image_field( $field, $size = 'full', $icon = false, $attr = '' ) {
		
		if( function_exists( 'get_field' ) ) {
			$image = get_field( $field );
			if( !empty($image) ) {
				return wp_get_attachment_image( $image['id'], $size, $icon, $attr );
			}
		}
		
	}

}


if( !function_exists('the_image_field') ){
	
	/**
	 * Output the markup for a responsive image.
	 * 
	 * @param  integrer $id   The ID of the ACF field.
	 * @param  array    $args The array of settings for the returned object.
	 */

	function the_image_field( $field, $size = 'full', $icon = false, $attr = '' ) {
		echo get_image_field( $field, $size, $icon, $attr );
	}

}

if( !function_exists('get_image_sub_field') ){

	/**
	 * Returns markup for a responsive image.
	 *
	 * @param  integrer $id   The ID of the ACF repeater field.
	 * @param  array    $args The array of settings for the returned object.
	 * @return string         The generated responsive image markup.
	 */

	function get_image_sub_field( $field, $size = 'full', $icon = false, $attr = '' ) {

		if( function_exists( 'get_sub_field' ) ) {
			$image = get_sub_field( $field );
			if( !empty($image) ) {
				return wp_get_attachment_image( $image['id'], $size, $icon, $attr );
			}
		}

	}

}


if( !function_exists('the_image_sub_field') ){

	/**
	 * Output the markup for a responsive image.
	 * 
	 * @param  integrer $id   The ID of the ACF repeater field.
	 * @param  array    $args The array of settings for the returned object.
	 */

	function the_image_sub_field( $field, $size = 'full', $icon = false, $attr = '' ) {
		echo get_image_sub_field( $field, $size, $icon, $attr );
	}

}
