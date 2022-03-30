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


		// Remove checks on currency and gross (total) values
		add_action( 'woocommerce_api_wc_gateway_paypal', array( $this, 'remove_currency_and_total_checks' ), 5 );


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


	/**
	 * Remove currency and total (gross) amount check in class WC_Gateway_Paypal_IPN_Handler
	 *
	 * @since 1.4.5
	 * @author htdat
	 */
	public function remove_currency_and_total_checks() {

		// Remove these filters https://github.com/woocommerce/woocommerce/blob/3.7.1/includes/gateways/paypal/includes/class-wc-gateway-paypal-ipn-handler.php#L34-L35
		remove_all_filters( 'woocommerce_api_wc_gateway_paypal', 10 );
		remove_all_filters( 'valid-paypal-standard-ipn-request', 10 );

		// Get values for PayPal Standard settings
		// Ref: https://github.com/woocommerce/woocommerce/blob/3.7.1/includes/gateways/paypal/class-wc-gateway-paypal.php#L58-L61
		$paypal_options = get_option( 'woocommerce_paypal_settings' );
		$testmode       = 'yes' === $paypal_options['testmode'];
		$receiver_email = is_null( $paypal_options['receiver_email'] ) ? $paypal_options['email'] : $paypal_options['receiver_email'];

		// Replace it by a child class of WC_Gateway_Paypal_IPN_Handler,
		// which overrides/removes validate_currency() and validate_amount()
		require_once dirname( __FILE__ ) . '/paypal-standard/class-woo-viet-wc-gateway-paypal-ipn-handler.php.php';
		$handler = new Woo_Viet_WC_Gateway_Paypal_IPN_Handler( $testmode, $receiver_email ); //@todo needs to handle the correct variables
		$handler->check_response();

	}

}