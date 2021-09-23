<?php

/**
 * Output a single payment method
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<tr>

	<td class="wc_payment_method payment_method_<?php echo $gateway->id; ?>">

		<div class="radio">
			<label for="payment_method_<?php echo $gateway->id; ?>">
				<input id="payment_method_<?php echo $gateway->id; ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
				<?php echo $gateway->get_title(); ?> <?php echo $gateway->get_icon(); ?>
			</label>
		</div>

		<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
			<div class="payment_box payment_method_<?php echo $gateway->id; ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
				<?php $gateway->payment_fields(); ?>
			</div>
		<?php endif; ?>

	</td>

</tr>
