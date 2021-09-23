<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="woocommerce-order">

	<?php if ( $order ) : ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<div class="alert alert-danger woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
				<?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?>
			</div>

			<div class="alert alert-warning woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</div>

		<?php else : ?>

			<div class="alert alert-success woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
				<?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); ?>
			</div>

			<table class="table table-bordered table-striped woocommerce-order-overview woocommerce-thankyou-order-details order_details">

				<thead>

					<th class="woocommerce-order-overview__order order">
						<?php _e( 'Order Number:', 'woocommerce' ); ?>
					</th>

					<th class="woocommerce-order-overview__date date">
						<?php _e( 'Date:', 'woocommerce' ); ?>
					</th>

					<th class="woocommerce-order-overview__total">
						<?php _e( 'Total:', 'woocommerce' ); ?>
					</th>

					<?php if ( $order->get_payment_method_title() ) : ?>
						<th class="woocommerce-order-overview__payment-method method">
							<?php _e( 'Payment Method:', 'woocommerce' ); ?>
						</th>
					<?php endif; ?>

				</thead>

				<tbody>

					<td class="woocommerce-order-overview__order order">
						<strong><?php echo $order->get_order_number(); ?></strong>
					</td>

					<td class="woocommerce-order-overview__date date">
						<strong><?php echo wc_format_datetime( $order->get_date_created() ); ?></strong>
					</td>

					<td class="woocommerce-order-overview__total total">
						<strong><?php echo $order->get_formatted_order_total(); ?></strong>
					</td>

					<?php if ( $order->get_payment_method_title() ) : ?>

						<td class="woocommerce-order-overview__payment-method method">
							<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
						</td>

					<?php endif; ?>

				</tbody>

			</table>

		<?php endif; ?>

		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<p class="alert alert-success woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
			<?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?>
		</p>

	<?php endif; ?>

</div>
