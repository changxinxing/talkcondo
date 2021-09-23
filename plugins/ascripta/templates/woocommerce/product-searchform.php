<?php
/**
 * The template for displaying product search form
 *
 * @see     http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url( home_url( '/'  ) ); ?>">

	<div class="input-group">

		<label class="screen-reader-text" for="woocommerce-product-search-field"><?php _e( 'Search for:', 'woocommerce' ); ?></label>

		<input type="search" id="woocommerce-product-search-field" class="form-control search-field" placeholder="<?php echo esc_attr_x( 'Search Products&hellip;', 'placeholder', 'woocommerce' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'woocommerce' ); ?>" />

		<span class="input-group-btn">
			<input type="hidden" name="post_type" value="product" />
			<button type="submit" class="btn btn-primary">
				<i class="fa fa-search"></i>
			</button>
		</span>

	</div>

</form>