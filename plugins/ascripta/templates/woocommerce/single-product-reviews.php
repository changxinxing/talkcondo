<?php

/**
 * Display single product reviews (comments)
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.3.2
 */

global $product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! comments_open() ) {
	return;
}

?>
<div id="reviews">
	<div id="comments">

		<h2>
			<?php
			if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && ( $count = $product->get_review_count() ) ){
				printf( _n( '%s review for %s', '%s reviews for %s', $count, 'woocommerce' ), $count, get_the_title() );
			} else {
				_e( 'Reviews', 'woocommerce' );
			} ?>
		</h2>

		<?php if ( have_comments() ) : ?>

		<ol class="media-list commentlist">
			<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
		</ol>

		<?php
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) {
			echo '<nav class="woocommerce-pagination">';
			paginate_comments_links( apply_filters( 'woocommerce_comment_pagination_args', array(
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
				'type'      => 'list',
			) ) );
			echo '</nav>';
		}
		?>

		<?php else : ?>

		<p class="woocommerce-noreviews">
			<?php _e( 'There are no reviews yet.', 'woocommerce' ); ?>
		</p>

		<?php endif; ?>

	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>

	<div id="review_form_wrapper">
		<div id="review_form">
			<?php
			$commenter = wp_get_current_commenter();

			$comment_form = array(
				'title_reply'          => have_comments() ? __( 'Add a review', 'woocommerce' ) : __( 'Be the first to review', 'woocommerce' ) . ' &ldquo;' . get_the_title() . '&rdquo;',
				'title_reply_to'       => __( 'Leave a Reply to %s', 'woocommerce' ),
				'comment_notes_before' => '',
				'comment_notes_after'  => '',
				'fields'               => array(
					'author' => '<div class="form-group comment-form-author">' . '<label for="author">' . __( 'Name', 'woocommerce' ) . ' <span class="required">*</span></label> ' .
					'<input id="author" name="author" class="form-control" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></div>',
					'email'  => '<div class="form-group comment-form-email"><label for="email">' . __( 'Email', 'woocommerce' ) . ' <span class="required">*</span></label> ' .
					'<input id="email" name="email" class="form-control" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></div>',
				),
				'label_submit'  => __( 'Submit', 'woocommerce' ),
				'class_submit'  => 'btn btn-primary',
				'logged_in_as'  => '',
				'comment_field' => '',
			);

			if ( $account_page_url = wc_get_page_permalink( 'myaccount' ) ) {
				$comment_form['must_log_in'] = '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a review.', 'woocommerce' ), esc_url( $account_page_url ) ) . '</p>';
			}

			if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
				$comment_form['comment_field'] = '
					<div class="form-group comment-form-rating">
						<label for="rating">' . __( 'Your Rating', 'woocommerce' ) .'</label>
						<select name="rating" id="rating">
							<option value="">' . __( 'Rate&hellip;', 'woocommerce' ) . '</option>
							<option value="5">' . __( 'Perfect', 'woocommerce' ) . '</option>
							<option value="4">' . __( 'Good', 'woocommerce' ) . '</option>
							<option value="3">' . __( 'Average', 'woocommerce' ) . '</option>
							<option value="2">' . __( 'Not that bad', 'woocommerce' ) . '</option>
							<option value="1">' . __( 'Very Poor', 'woocommerce' ) . '</option>
						</select>
					</div>';
			}

			$comment_form['comment_field'] .= '<div class="form-group comment-form-comment"><label for="comment">' . __( 'Your Review', 'woocommerce' ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" class="form-control" rows="5" aria-hidden="true"></textarea></div>';

			comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
			?>
		</div>
	</div>

	<?php else : ?>

	<div class="alert alert-warning woocommerce-verification-required">
		<?php _e( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ); ?>
	</div>

	<?php endif; ?>

	<div class="clear"></div>
</div>
