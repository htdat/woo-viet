<?php 

if ( class_exists( 'WC_Gateway_Paypal_IPN_Handler' ) ) {

	class Woo_Viet_WC_Gateway_Paypal_IPN_Handler extends WC_Gateway_Paypal_IPN_Handler {

		/**
		 * Check currency from IPN matches the order.
		 *
		 * @param WC_Order $order    Order object.
		 * @param string   $currency Currency code.
		 */
		protected function validate_currency( $order, $currency ) {

			WC_Gateway_Paypal::log( 'validate_currency in ' . __CLASS_ );
		}

		/**
		 * Check payment amount from IPN matches the order.
		 *
		 * @param WC_Order $order  Order object.
		 * @param int      $amount Amount to validate.
		 */
		protected function validate_amount( $order, $amount ) {

		}

		protected function validate_receiver_email( $order, $receiver_email ) {

		}

	}

}	