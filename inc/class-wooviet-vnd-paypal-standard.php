<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to add the support for VND when using the PayPal Standard gateway
 *
 * @see https://docs.woocommerce.com/document/paypal-standard/
 *
 * Reference: https://gist.github.com/vinefruit/3eb76c85707dcd254841
 *
 * @author   htdat
 * @since    1.0
 *
 */
class WooViet_VND_PayPal_Standard {

	/**
	 * @var int
	 */
	protected $exchange_rate_to_vnd;
	/**
	 * @var string the curreny will be used
	 */
	protected $paypal_currency = 'USD';

	//@todo - declare two vars 

	/**
	 * WooViet_VND_PayPal_Standard constructor.
	 *
	 * @param int $exchange_rate_to_vnd
	 */
	public function __construct( $exchange_rate_to_vnd = 22770, $paypal_currency ) {

		$this->exchange_rate_to_vnd = (int) $exchange_rate_to_vnd;
		$this->paypal_currency      = $paypal_currency;

		// Add VND to the PayPal supported currencies
		add_filter( 'woocommerce_paypal_supported_currencies', array( $this, 'add_vnd_paypal_valid_currency' ) );

		// Covert VND prices to the selected currency (by default, it's USD) prices before checking out with PayPal Standard
		add_filter( 'woocommerce_paypal_args', array( $this, 'convert_prices' ), 11 );

		// Add the exchange rate info for this gateway in the checkout page before proceeding in the PayPal pages
		add_filter( 'option_woocommerce_paypal_settings', array( $this, 'add_exchange_rate_info' ), 11 );

		// Match currency and amount between Paypal and WC Order
		add_action( 'valid-paypal-standard-ipn-request', array( $this, 'match_order_currency_and_amount' ), 5 );

		// Restore currency and amount for WC Order
		add_action( 'valid-paypal-standard-ipn-request', array( $this, 'restore_order_currency_and_amount' ), 15 );
		
	}

	/**
	 * @param $currencies
	 *
	 * @return mixed
	 */
	public function add_vnd_paypal_valid_currency( $currencies ) {
		array_push( $currencies, 'VND' );

		return $currencies;
	}

	/**
	 * @param $paypal_args
	 *
	 * @return mixed
	 */
	public function convert_prices( $paypal_args ) {
		if ( $paypal_args['currency_code'] == 'VND' ) {
			$paypal_args['currency_code'] = $this->paypal_currency;

			$i = 1;

			while ( isset( $paypal_args[ 'amount_' . $i ] ) ) {
				$paypal_args[ 'amount_' . $i ] = round( $paypal_args[ 'amount_' . $i ] / $this->exchange_rate_to_vnd, 2 );
				++ $i;
			}
			if ( $paypal_args['shipping_1'] > 0 ) {
				$paypal_args['shipping_1'] = round( $paypal_args['shipping_1'] / $this->exchange_rate_to_vnd, 2 );
			}

			if ( $paypal_args['discount_amount_cart'] > 0 ) {
				$paypal_args['discount_amount_cart'] = round( $paypal_args['discount_amount_cart'] / $this->exchange_rate_to_vnd, 2 );
			}
			if ( $paypal_args['tax_cart'] > 0 ) {
				$paypal_args['tax_cart'] = round( $paypal_args['tax_cart'] / $this->exchange_rate_to_vnd, 2 );
			}
		}

		return $paypal_args;
	}


	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public function add_exchange_rate_info( $value ) {
		if ( ! is_admin() ) {

			$value['description'] .= '<br />';
			$value['description'] .=
				sprintf( __( 'The prices will be converted to %1$s in the PayPal pages with the exchange rate %2$s.', 'woo-viet' ),
					"<span style='color:red'> $this->paypal_currency</span>",
					"<span style='color:red'> $this->paypal_currency / VND = $this->exchange_rate_to_vnd</span>"
				);

		}

		return $value;
	}

	/*	
	* Match currency and amount from Paypal IPN with the order
	* 
	* Topic https://wordpress.org/support/topic/loi-order-bi-on-hold/
	*
	* @author 	htdat
	* @since 	1.4.3
	*/
	public function match_order_currency_and_amount($posted) {

		// @test-fix
		// === Current way === 
		// hook before valid_response() - remove this filter, replace the check with a class similar to WC_Gateway_Paypal_IPN_Handler but valid_response is re-written. 
		//
		// === A much better way === 
		// hook before check_response, then do quite the same way above. 

       remove_all_filters('valid-paypal-standard-ipn-request'); // maybe add priority 
		
		WC_Gateway_Paypal::log( 'before calling Woo_Viet_WC_Gateway_Paypal_IPN_Handler' );

       require_once dirname( __FILE__ ) . '/paypal-standard/Woo_Viet_WC_Gateway_Paypal_IPN_Handler.php';

		$handler = new Woo_Viet_WC_Gateway_Paypal_IPN_Handler( true, '' ); //needs to handle the correct variables

		$handler->check_response(); 
		
		WC_Gateway_Paypal::log( 'after calling Woo_Viet_WC_Gateway_Paypal_IPN_Handler' );


/*
		$order = ! empty( $posted['custom'] ) ? $this->get_paypal_order( $posted['custom'] ) : false;

		if ( $order ) {
			$this->original_order_currency = $order->get_currency();
			$this->original_order_total = $order->get_total();

			$order->set_currency( $posted['mc_currency'] );
			$order->set_total( $posted['mc_gross'] );

			$order->save();
		}
*/		
		// @test-fix-end
	}

	/*	
	* Restore currency and amount of the order after the 'match_order_currency_and_amount' action
	*
	* @author 	htdat
	* @since 	1.4.3
	*/
	public function restore_order_currency_and_amount($posted) {

		$order = ! empty( $posted['custom'] ) ? $this->get_paypal_order( $posted['custom'] ) : false;

		if ( $order ) {

			$order->set_currency( $this->original_order_currency );
			$order->set_total( $this->original_order_total );

			$order->save();
		}

	}


	/**
	 * @see Grab this code from - can not call it directly https://github.com/woocommerce/woocommerce/blob/f5c2f89af6a9421af8edc2a4aa20d372e5be40f8/includes/gateways/paypal/includes/class-wc-gateway-paypal-response.php#L30 
	 * 
	 * @since 1.4.3 
	 * @author htdat
	 */
	protected function get_paypal_order( $raw_custom ) {
		// We have the data in the correct format, so get the order.
		$custom = json_decode( $raw_custom );
		if ( $custom && is_object( $custom ) ) {
			$order_id  = $custom->order_id;
			$order_key = $custom->order_key;
		} else {
			// Nothing was found.
			WC_Gateway_Paypal::log( 'Order ID and key were not found in "custom".', 'error' );
			return false;
		}
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			// We have an invalid $order_id, probably because invoice_prefix has changed.
			$order_id = wc_get_order_id_by_order_key( $order_key );
			$order    = wc_get_order( $order_id );
		}
		if ( ! $order || $order->get_order_key() !== $order_key ) {
			WC_Gateway_Paypal::log( 'Order Keys do not match.', 'error' );
			return false;
		}
		return $order;
	}	

}

