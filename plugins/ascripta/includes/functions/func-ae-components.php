<?php

/**
 * AE Components
 *
 * @class 		AE_Components
 * @version		1.0.0
 * @package		AE/Functions
 * @category	Helper
 * @author 		Ascripta
 */

if( !function_exists('get_author_box') ){

	/**
	 * Author Box
	 */
	function get_author_box() {

		AE_Components::author_box();

	}

}

if( !function_exists('get_breadcrumb') ){

	/**
	 * Breadcrumb
	 */
	function get_breadcrumb() {

		AE_Components::breadcrumb();

	}

}

if( !function_exists('get_pagination') ){

	/**
	 * Pagination
	 */
	function get_pagination() {

		AE_Components::pagination();

	}

}

if( !function_exists('get_pager') ){

	/**
	 * Pagination
	 */
	function get_pager() {

		AE_Components::pager();

	}

}

if( !function_exists('get_related_posts') ){

	/**
	 * Pagination
	 */
	function get_related_posts() {

		AE_Components::related_posts();

	}

}
