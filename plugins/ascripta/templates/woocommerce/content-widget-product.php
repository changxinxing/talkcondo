<?php
/**
 * The template for displaying product widget entries
 *
 * @see 	http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product; ?>

<div class="media">
	<div class="media-left">
		<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>" class="thumbnail">
			<div class="media-object">
				<?php echo $product->get_image(); ?>
			</div>
		</a>
	</div>
	<div class="media-body media-middle">
		<div class="media-heading">
			<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>">
				<span class="product-title"><?php echo $product->get_title(); ?></span>
			</a>
		</div>

		<div class="media-content">
			<?php if ( ! empty( $show_rating ) ) : ?>
				<?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
			<?php endif; ?>
			<?php echo $product->get_price_html(); ?>
		</div>
	</div>
</div>
