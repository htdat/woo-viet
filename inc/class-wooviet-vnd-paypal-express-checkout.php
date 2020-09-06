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
	 * @var string
	 */
	protected $ppec_currency;

	/**
	 * @var string
	 */
	protected $ppec_description;

	/**
	 * WooViet_VND_PayPal_Express_Checkout constructor
	 *
	 * @param int $ppec_exchange_rate
	 * @param string $ppec_currency
	 */
	public function __construct( $ppec_exchange_rate = 22770, $ppec_currency = 'USD' ) {

		$this->ppec_exchange_rate = (int) $ppec_exchange_rate;
		$this->ppec_currency      = $ppec_currency;

		$this->ppec_description = sprintf( __( 'The prices will be converted to %1$s in the PayPal Express Checkout pages with the exchange rate %2$s.', 'woo-viet' ),
			"<span style='color:red'> $this->ppec_currency</span>",
			"<span style='color:red'> $this->ppec_currency / VND = $this->ppec_exchange_rate</span>"
		);

		// Match response currency of PayPal with local order
		add_action( 'woocommerce_paypal_express_checkout_valid_ipn_request', array(
			$this,
			'ppec_match_currency_order'
		), 20 );

		// Add exchange rate before send request to PayPal
		add_filter( 'woocommerce_paypal_express_checkout_request_body', array( $this, 'ppec_convert_prices' ) );

		/**
		 * Ensure that PayPal Checkout SDK will load with the correct currency
		 * @see https://github.com/woocommerce/woocommerce-gateway-paypal-express-checkout/blob/f1f16de178cbf1d529deeaf574a52aca82a6e093/includes/class-wc-gateway-ppec-cart-handler.php#L553-L556
		 */
		add_filter ('woocommerce_paypal_express_checkout_sdk_script_args', function( $script_args ) {
			$script_args[ 'currency' ] = $this->ppec_currency;
			return $script_args;
		});

		// Load the method to add the exchange rate info for this gateway
		$this->ppec_exchange_rate_info();

	}

	/**
	 * Match response currency from PayPal with the order
	 *
	 * @param $posted_data
	 */
	public function ppec_match_currency_order( $posted_data ) {

		if ( $posted_data['mc_currency'] ) {
			$posted_data['mc_currency'] = $order->get_currency();
		}

	}

	/**
	 * @param  $params
	 *
	 * @return mixed
	 */
	public function ppec_convert_prices( $params ) {

		if ( isset( $params['PAYMENTREQUEST_0_CURRENCYCODE'] ) ) {

			$params['PAYMENTREQUEST_0_CURRENCYCODE'] = $this->ppec_currency;

			if ( isset( $params['PAYMENTREQUEST_0_AMT'] ) ) {
				$params['PAYMENTREQUEST_0_AMT'] = round( $params['PAYMENTREQUEST_0_AMT'] / $this->ppec_exchange_rate, 2 );
			}

			if ( isset( $params['PAYMENTREQUEST_0_ITEMAMT'] ) ) {
				$params['PAYMENTREQUEST_0_ITEMAMT'] = round( $params['PAYMENTREQUEST_0_ITEMAMT'] / $this->ppec_exchange_rate, 2 );
			}

			if ( isset( $params['PAYMENTREQUEST_0_SHIPPINGAMT'] ) ) {
				$params['PAYMENTREQUEST_0_SHIPPINGAMT'] = round( $params['PAYMENTREQUEST_0_SHIPPINGAMT'] / $this->ppec_exchange_rate, 2 );
			}

			if ( isset( $params['PAYMENTREQUEST_0_TAXAMT'] ) ) {
				$params['PAYMENTREQUEST_0_TAXAMT'] = round( $params['PAYMENTREQUEST_0_TAXAMT'] / $this->ppec_exchange_rate, 2 );
			}

			if ( isset( $params['PAYMENTREQUEST_0_SHIPDISCAMT'] ) ) {
				$params['PAYMENTREQUEST_0_SHIPDISCAMT'] = round( $params['PAYMENTREQUEST_0_SHIPDISCAMT'] / $this->ppec_exchange_rate, 2 );
			}

			$count = 0;

			while ( isset( $params[ 'L_PAYMENTREQUEST_0_AMT' . $count ] ) ) {
				$params[ 'L_PAYMENTREQUEST_0_AMT' . $count ] = round( $params[ 'L_PAYMENTREQUEST_0_AMT' . $count ] / $this->ppec_exchange_rate, 2 );
				$count ++;
			}
		}

		return $params;

	}

	/**
	 * Add the exchange rate info in the suitable locations before proceeding in the PayPal pages
	 */
	public function ppec_exchange_rate_info() {

		// Check if "Checkout on cart page" is enabled.
		if ( 'yes' === wc_gateway_ppec()->settings->cart_checkout_enabled ) {
			add_action( 'woocommerce_proceed_to_checkout', array( $this, 'add_ppec_button_exchange_rate_info' ), 30 );
		}

		// Check if "Checkout on Single Product" is enabled.
		if ( 'yes' === wc_gateway_ppec()->settings->checkout_on_single_product_enabled ) {
			add_action( 'woocommerce_after_add_to_cart_form', array(
				$this,
				'add_ppec_button_exchange_rate_info'
			), 30 );
		}

		// Check if "Enable PayPal Credit" is enabled.
		if ( 'yes' === wc_gateway_ppec()->settings->credit_enabled ) {
			add_filter( 'woocommerce_paypal_express_checkout_settings', array(
				$this,
				'add_paypal_credit_exchange_rate_info'
			), 11 );
		}

		// Add the exchange rate info for PPEC in Checkout page
		add_filter( 'option_woocommerce_ppec_paypal_settings', array(
			$this,
			'add_ppec_checkout_exchange_rate_info'
		), 11 );

	}

	/**
	 * Display the exchange rate info of PPEC in Cart and Single Product page
	 */
	public function add_ppec_button_exchange_rate_info() {

		echo '<p class="ppec-exchange-rate-info">' . $this->ppec_description . '</p>';

	}

	/**
	 * Display the exchange rate info of PP Credit in Checkout page
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function add_paypal_credit_exchange_rate_info( $value ) {

		if ( ! is_admin() ) {
			$value['description']['default'] .= '<br />';
			$value['description']['default'] .= $this->ppec_description;
		}

		return $value;
	}

	/**
	 * Display the exchange rate info of PPEC in Checkout page
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function add_ppec_checkout_exchange_rate_info( $value ) {

		if ( ! is_admin() ) {
			$value['description'] .= '<br />';
			$value['description'] .= $this->ppec_description;
		}

		return $value;

	}

}