<?php

/**
 * Order tracking form
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

?>

<form action="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" method="post" class="track_order">

	<div class="alert alert-info"><?php _e( 'To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.', 'woocommerce' ); ?></div>

	<div class="row">

		<div class="col-xs-12 col-sm-6">
			<label for="orderid"><?php _e( 'Order ID', 'woocommerce' ); ?></label>
			<input class="form-control" type="text" name="orderid" id="orderid" placeholder="<?php esc_attr_e( 'Found in your order confirmation email.', 'woocommerce' ); ?>" />
		</div>

		<div class="col-xs-12 col-sm-6">
			<label for="order_email"><?php _e( 'Billing Email', 'woocommerce' ); ?></label>
			<input class="form-control" type="text" name="order_email" id="order_email" placeholder="<?php esc_attr_e( 'Email you used during checkout.', 'woocommerce' ); ?>" />
		</div>

	</div>

	<div class="form-group">
		<input type="submit" class="btn btn-primary" name="track" value="<?php esc_attr_e( 'Track', 'woocommerce' ); ?>" />
	</div>

	<?php wp_nonce_field( 'woocommerce-order_tracking' ); ?>

</form>
