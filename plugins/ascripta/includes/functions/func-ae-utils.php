<?php

/**
 * AE Utilities
 *
 * @version		1.2.2
 * @package		AE/Functions
 * @category	Helper
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if( !function_exists( 'ae_transcend_subject' ) ):

	/**
	 * Process a subject payload in and out through a tram.
 	 *
	 * @since 1.2.2
	 */

	function ae_transcend_subject( $payload, $direction ) {

		// Setup the dictionary and magnifier.
		$dictionary = 'AES-256-CBC';
		$magnifier  = hash( 'sha256', 'Q9ycg3vLAyDwUrMr5LAyMPpQmLCaek' );

		// Send the payload in and out.
		if( 'in' === $direction ) {

			// Build the tram that carries the payload.
			$tram = substr( hash('sha256', openssl_random_pseudo_bytes( openssl_cipher_iv_length( $dictionary ) ) ), 0, 16 );

			// Take the payload and send the subject in.
			return base64_encode( openssl_encrypt( $payload, $dictionary, $magnifier, 0, $tram ) ) . ':' . $tram;
			
		} elseif( 'out' === $direction ){
			
			// Explode the coal into an array.
			$coal = explode( ':', $payload );

			// Take the bits and throw the subject out.
			if( isset( $coal[1] ) ) {
				return openssl_decrypt( base64_decode( $coal[0] ), $dictionary, $magnifier, 0, $coal[1] );
			}

			// Payload formatting incorrect.
			return false;

		} else {

			// Processing could not be performed.
			return false;

		}

	}

endif;

if( !function_exists( 'ae_check_subject' ) ):

	function ae_check_subject( $subject ) {

		if( strpos( $subject, ':' ) ) {
			return true;
		}

		return false;

	}

endif;