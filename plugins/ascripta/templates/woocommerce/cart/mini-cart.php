<?php

/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @see     http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>

<table class="cart_list product_list_widget <?php echo ( WC()->cart->is_empty() ) ? 'table' : 'table table-bordered'; ?> <?php echo $args['list_class']; ?>">

	<tbody>

		<?php if ( ! WC()->cart->is_empty() ) : ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

					$product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
					$thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
					$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
					?>

					<tr>
						<td colspan="2" >
							<div class="media" class="<?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
								<div class="media-left">
									<?php if ( ! $_product->is_visible() ) : ?>
										<div class="media-object">
											<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); ?>
										</div>
									<?php else : ?>
										<a href="<?php echo esc_url( $_product->get_permalink( $cart_item ) ); ?>" class="thumbnail">
											<div class="media-object">
												<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); ?>
											</div>
										</a>
									<?php endif; ?>
								</div>
								<div class="media-body media-middle">
									<h5 class="media-heading"><?php echo $product_name; ?></h5>

									<div class="media-content">
										<?php
										echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
										'<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">' . __( 'Remove this item', 'woocommerce' ) . '</a>',
										esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
										__( 'Remove this item', 'woocommerce' ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() )
										), $cart_item_key );
										?>

										<?php echo WC()->cart->get_item_data( $cart_item ); ?>

										<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
									</div>
								</div>
							</div>
						</td>
					</tr>

				<?php
				}
			}
			?>

		<?php else : ?>

			<tr class="empty"><td><?php _e( 'No products in the cart.', 'woocommerce' ); ?></td></tr>

		<?php endif; ?>

	</tbody>

	<?php if ( ! WC()->cart->is_empty() ) : ?>

		<tfoot>

			<tr>
				<td colspan="2" class="text-center"><span class="total"><strong><?php _e( 'Subtotal', 'woocommerce' ); ?>:</strong> <?php echo WC()->cart->get_cart_subtotal(); ?></span></td>
			</tr>

			<tr>
				<td>
					<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>
					<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn btn-default btn-block wc-forward"><?php _e( 'View Cart', 'woocommerce' ); ?></a>
				</td>
				<td>
					<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-primary btn-block checkout wc-forward"><?php _e( 'Checkout', 'woocommerce' ); ?></a>
				</td>
			</tr>

		</tfoot>

	<?php endif; ?>

</table>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
