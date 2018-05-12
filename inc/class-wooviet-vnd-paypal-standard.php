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

}