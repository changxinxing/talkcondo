<?php

/**
 * Login form
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( is_user_logged_in() ) {
	return;
}

?>
<form method="post" class="login form-horizontal" <?php if ( $hidden ) echo 'style="display:none;"'; ?>>

	<?php do_action( 'woocommerce_login_form_start' ); ?>

    <div class="form-group col-sm-12">
	    <?php if ( $message ) echo wpautop( wptexturize( $message ) ); ?>
	</div>

	<div class="form-group">
		<label for="username" class="col-sm-4 control-label"><?php _e( 'Username or email', 'woocommerce' ); ?> <span class="required">*</span></label>
		<div class="col-sm-8">
		    <input type="text" class="form-control" name="username" id="username" />
		</div>
	</div>

	<div class="form-group">
		<label for="password" class="col-sm-4 control-label"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
		<div class="col-sm-8">
		    <input type="password" class="form-control" name="password" id="password" />
		</div>
	</div>

	<?php do_action( 'woocommerce_login_form' ); ?>

    <div class="form-group">
        <div class="col-sm-8 col-sm-offset-4">
            <div class="checkbox">
                <?php wp_nonce_field( 'woocommerce-login' ); ?>
                <input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />
                <label for="rememberme">
                    <input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woocommerce' ); ?>
                </label>
            </div>
        </div>
    </div>

	<div class="form-group">
        <div class="col-sm-8 col-sm-offset-4">
	        <input type="submit" class="btn btn-primary" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>" />
	    </div>
    </div>

	<div class="form-group">
	    <div class="col-sm-8 col-sm-offset-4">
		    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'woocommerce' ); ?></a>
        </div>
	</div>

	<?php do_action( 'woocommerce_login_form_end' ); ?>

</form>
