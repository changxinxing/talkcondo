<?php

/**
 * My Orders
 *
 * Shows recent orders on the account page
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $downloads = WC()->customer->get_downloadable_products() ) : ?>

<?php do_action( 'woocommerce_before_available_downloads' ); ?>

<div class="page-header">
	<h2><?php echo apply_filters( 'woocommerce_my_account_my_downloads_title', __( 'Available Downloads', 'woocommerce' ) ); ?></h2>
</div>

<table class="digital-downloads table table-bordered table-striped">
	<thead>
		<tr>
			<th><?php _e( 'Download Link', 'ascripta' ); ?></th>
			<th><?php _e( '# Remaining', 'ascripta' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $downloads as $download ) : ?>
		<tr>
			<td>
				<?php
				do_action( 'woocommerce_available_download_start', $download );

				echo apply_filters( 'woocommerce_available_download_link', '<a href="' . esc_url( $download['download_url'] ) . '">' . $download['download_name'] . '</a>', $download );
				?>
			</td>
			<td>
				<?php
				if ( is_numeric( $download['downloads_remaining'] ) )
					echo apply_filters( 'woocommerce_available_download_count', '<span class="count">' . sprintf( _n( '%s download remaining', '%s downloads remaining', $download['downloads_remaining'], 'woocommerce' ), $download['downloads_remaining'] ) . '</span> ', $download );

				do_action( 'woocommerce_available_download_end', $download );
				?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php do_action( 'woocommerce_after_available_downloads' ); ?>

<?php endif; ?>
