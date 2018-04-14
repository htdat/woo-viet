<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to add the support for VND when using the WooCommerce PayPal Express Checkout gateway
 *
 * @see https://docs.woocommerce.com/document/paypal-express-checkout/
 *
 * @author   Longkt
 * @since    1.4
 *
 */
class WooViet_VND_PayPal_Express_Checkout {

	/**
	 * @var int
	 */
	protected $ppec_exchange_rate;
	/**
	 * @var str
	 */
	protected $ppec_currency;

	/**
	 * WooViet_VND_PayPal_Express_Checkout constructor
	 *
	 * @param int $ppec_exchange_rate
	 * @param str $ppec_currency
	 */
	public function __construct( $ppec_exchange_rate = 22770, $ppec_currency = 'USD' ) {

		$this->ppec_exchange_rate = (int) $ppec_exchange_rate;
		$this->ppec_currency      = $ppec_currency;

		// Match response currency of PPEC with local order
		add_action( 'woocommerce_paypal_express_checkout_valid_ipn_request', array( $this, 'ppec_match_currency_order' ) );

		// Add exchange rate before send request to PayPal
		add_filter( 'woocommerce_paypal_express_checkout_request_body', array( $this, 'ppec_convert_prices' ) );

	}

	/**
	 * Match response currency from PPEC with the order
	 * 
	 * @param $posted_data
	 */
	public function ppec_match_currency_order( $posted_data ) {

		if( $posted_data['mc_currency'] ) {
			$posted_data['mc_currency'] = $order->get_currency();
		}

	}

	/**
	 * @param  $params
	 * @return mixed
	 */
	public function ppec_convert_prices( $params ) {

			if( isset( $params['PAYMENTREQUEST_0_CURRENCYCODE'] ) ) {
				$params['PAYMENTREQUEST_0_CURRENCYCODE'] = $this->ppec_currency;

				if( isset( $params['PAYMENTREQUEST_0_AMT'] ) ) {
					$params['PAYMENTREQUEST_0_AMT'] = round( $params['PAYMENTREQUEST_0_AMT'] / $this->ppec_exchange_rate, 2 );
				}

				if( isset( $params['PAYMENTREQUEST_0_ITEMAMT'] ) ) {
					$params['PAYMENTREQUEST_0_ITEMAMT'] = round( $params['PAYMENTREQUEST_0_ITEMAMT'] / $this->ppec_exchange_rate, 2 );
				}
				
				if( isset( $params['PAYMENTREQUEST_0_SHIPPINGAMT'] ) ) {
					$params['PAYMENTREQUEST_0_SHIPPINGAMT'] = round( $params['PAYMENTREQUEST_0_SHIPPINGAMT'] / $this->ppec_exchange_rate, 2 );
				}

				if( isset( $params['PAYMENTREQUEST_0_TAXAMT'] ) ) {
					$params['PAYMENTREQUEST_0_TAXAMT'] = round( $params['PAYMENTREQUEST_0_TAXAMT'] / $this->ppec_exchange_rate, 2 );
				}

				if( isset( $params['PAYMENTREQUEST_0_SHIPDISCAMT'] ) ) {
					$params['PAYMENTREQUEST_0_SHIPDISCAMT'] = round( $params['PAYMENTREQUEST_0_SHIPDISCAMT'] / $this->ppec_exchange_rate, 2 );
				}

				$count = 0;

				while( isset( $params['L_PAYMENTREQUEST_0_AMT' . $count] ) ) {
					$params['L_PAYMENTREQUEST_0_AMT' . $count] = round( $params['L_PAYMENTREQUEST_0_AMT' . $count] / $this->ppec_exchange_rate, 2 );
					$count++;
				}
			}

		return $params;

	}

}