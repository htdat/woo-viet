<?php

/**
 * Override/remove two validate functions in the parent class
 *
 * @author   htdat
 * @since    1.4.5
 *
 */
if ( class_exists( 'WC_Gateway_Paypal_IPN_Handler' ) ) {

	class Woo_Viet_WC_Gateway_Paypal_IPN_Handler extends WC_Gateway_Paypal_IPN_Handler {

		protected function validate_currency( $order, $currency ) {
		}

		protected function validate_amount( $order, $amount ) {
		}

	}

}