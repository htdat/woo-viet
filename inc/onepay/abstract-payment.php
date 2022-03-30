<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract class to handle the domestic and international OnePay gateways
 *
 *
 * @author   htdat
 * @since    1.5.0
 *
 */
abstract class WooViet_OnePay_Abstract extends WC_Payment_Gateway {
	/** @var bool Whether or not logging is enabled */
	public static $log_enabled = false;

	/** @var WC_Logger Logger instance */
	public static $log = false;

	/**
	 * Configure $method_title and $method_description
	 */
	abstract public function configure_payment();

	/**
	 * @param bool $testmode
	 */
	abstract public function get_onepay_payment_link( $testmode );

	/**
	 * @param bool $testmode
	 */
	abstract public function get_onepay_querydr_link( $testmode );

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                 = strtolower( get_called_class() );
		$this->has_fields         = false;
		$this->configure_payment();
		$this->supports           = array(
			'products',
		);

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title             = $this->get_option( 'title' );
		$this->description       = $this->get_option( 'description' )
		                           . sprintf( '<br/><div align="center" class="onepay-logo" id="%1$s"><img src="%2$s"></div>',
				$this->id . '_logo',
				apply_filters( $this->id . '_logo', WOO_VIET_URL . "assets/$this->id.png" ) );
		$this->order_button_text = $this->get_option( 'order_button_text' );

		$this->testmode      = 'yes' === $this->get_option( 'testmode', 'no' );
		$this->merchant_id   = $this->get_option( 'merchant_id' );
		$this->access_code   = $this->get_option( 'access_code' );
		$this->secure_secret = $this->get_option( 'secure_secret' );
		$this->user          = $this->get_option( 'user' );
		$this->password      = $this->get_option( 'password' );
		$this->debug         = 'yes' === $this->get_option( 'debug', 'no' );

		self::$log_enabled = $this->debug;

		// Process the admin options
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
			$this,
			'process_admin_options'
		) );

		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'handle_onepay_return_url' ) );

		add_action( 'woocommerce_api_' . strtolower( get_called_class() ), array( $this, 'handle_onepay_ipn' ) );

	}

	/**
	 * Get the IPN URL for OnePay
	 * Format: http://my-site.com/wc-api/WooViet_OnePay_Domestic/
	 */
	static function get_onepay_ipn_url() {
		return WC()->api_request_url( get_called_class() );
	}

	/**
	 * Process the payment
	 *
	 * @param int $order_id
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		return array(
			'result'   => 'success',
			'redirect' => $this->get_pay_url( $order )
		);
	}

	/**
	 * Get the OnePay pay URL for an order
	 * AND set the queryDR cron for this transaction
	 *
	 * @param  WC_Order $order
	 *
	 * @return string
	 */
	public function get_pay_url( $order ) {
		$home_url_without_scheme = str_replace( 'https://', '', get_home_url() );
		$home_url_without_scheme = str_replace( 'http://', '', $home_url_without_scheme );

		$args = array(
			'Title'           => __( 'OnePay Payment Title', 'woo-viet' ),
			'vpc_Merchant'    => $this->merchant_id,
			'vpc_AccessCode'  => $this->access_code,
			'vpc_MerchTxnRef' => sprintf( '%1$s_%2$s', $order->get_id(), date( 'YmdHis' ) ),
			'vpc_OrderInfo'   => substr(
				sprintf( 'Order #%1$s - %2$s', $order->get_id(), $home_url_without_scheme ),
				0,
				32 ), // Limit 32 characters
			'vpc_Amount'      => $order->get_total() * 100, // Multiplying 100 is a requirement from OnePay
			'vpc_ReturnURL'   => $this->get_return_url( $order ),
			'vpc_Version'     => '2',
			'vpc_Command'     => 'pay',
			'vpc_Locale'      => ( 'vi' == get_locale() ) ? 'vn' : 'en',
			'vpc_Currency'    => 'VND',
			'vpc_TicketNo'    => $_SERVER['REMOTE_ADDR'],
			'AgainLink'       => mb_strimwidth( wc_get_checkout_url(), 0, 64 ),
		);

		// Set the queryDR cron for this transaction
		$this->set_onepay_querydr_cron( $args['vpc_MerchTxnRef'] );

		// Get the secure hash
		$vpc_SecureHash = $this->create_vpc_SecureHash( $args );

		// Add the secure hash to the args
		$args['vpc_SecureHash'] = $vpc_SecureHash;
		$http_args              = http_build_query( $args, '', '&' );

		// Log data
		$message_log = sprintf( 'get_pay_url - Order ID: %1$s - http_args: %2$s', $order->get_id(), print_r( $args, true ) );
		self::log( $message_log );

		return $this->get_onepay_payment_link( $this->testmode ) . '?' . $http_args;

	}

	/**
	 * Set the cron job running queryDR in 20 mintues
	 * Because the OnePay payment timeout is 15 minutes
	 *
	 * @param string $vpc_MerchTxnRef
	 */
	public function set_onepay_querydr_cron( $vpc_MerchTxnRef ) {

		wp_schedule_single_event(
			time() + 20 * 60,
			$this->id . '_handle_querydr', // wooviet_onepay_international_handle_querydr | wooviet_onepay_domestic_handle_querydr
			array( $vpc_MerchTxnRef )
		);

	}

	/**
	 * Create the vpc_SecureHash value.
	 * @see https://mtf.onepay.vn/developer/?page=modul_noidia_php
	 *
	 * @param  array $args
	 *
	 * @return string
	 */
	public function create_vpc_SecureHash( $args ) {
		$stringHashData = "";

		// arrange array data a-z before make a hash
		ksort( $args );

		foreach ( $args as $key => $value ) {

			if ( strlen( $value ) > 0 ) {
				if ( ( strlen( $value ) > 0 ) && ( ( substr( $key, 0, 4 ) == "vpc_" ) || ( substr( $key, 0, 5 ) == "user_" ) ) ) {
					$stringHashData .= $key . "=" . $value . "&";
				}
			}
		}
		//Remove the last character "&"
		$stringHashData = rtrim( $stringHashData, "&" );

		return strtoupper( hash_hmac( 'SHA256', $stringHashData, pack( 'H*', $this->secure_secret ) ) );
	}

	/**
	 * Handle the return URL - GET request from OnePay
	 */
	public function handle_onepay_return_url() {

		if ( isset( $_GET['vpc_SecureHash'] ) ) {

			$this->process_onepay_response_data( $_GET, 'return' );

		}
	}

	/**
	 * Handle the repsonse data from OnePay
	 *
	 * @param string $args the response data from OnePay
	 * @param string $type
	 */
	public function process_onepay_response_data( $args, $type ) {
		$types_accepted = array(
			'return',
			'ipn',
			'querydr',
		);

		// Do nothing if the type is wrong
		if ( ! in_array( $type, $types_accepted ) ) {
			return;
		}

		$is_secure         = false;
		$is_querydr_exists = false;

		// Verify hash for 'return' and 'ipn'
		// And check vpc_DRExists value for 'querydr'
		switch ( $type ) {
			case 'return':
			case 'ipn':
				$vpc_SecureHash = $args['vpc_SecureHash'];

				// Remove the parameter "vpc_SecureHash" for validating SecureHash
				unset( $args['vpc_SecureHash'] );

				$is_secure = $this->check_vpc_SecureHash( $args, $vpc_SecureHash );
				break;
			case 'querydr':
				$is_querydr_exists = ( 'Y' == $args['vpc_DRExists'] );
		}

		// Process the data
		if ( $is_secure OR $is_querydr_exists ) {
			/**
			 * $vpc_MerchTxnRef looks like this "139_20170418101843" or {order_id}_{date_time}
			 * @see $this->get_pay_url();
			 */
			$vpc_MerchTxnRef     = $args['vpc_MerchTxnRef'];
			$vpc_TxnResponseCode = $args['vpc_TxnResponseCode'];

			// Get the order_id part only
			$order_id = explode('_', $vpc_MerchTxnRef)[0];

			$order = wc_get_order( $order_id );

			// Add the order note for the reference
			$order_note = get_called_class() . sprintf(
				__( ' Gateway Info | Code: %1$s | Message: %2$s | MerchantTxnRef: %3$s | Type: %4$s', 'woo-viet' ),
				$vpc_TxnResponseCode,
				$this->OnePay_getResponseDescription( $vpc_TxnResponseCode ),
				$vpc_MerchTxnRef,
				$type
			);
			$order->add_order_note( $order_note );

			// Log data
			$message_log = sprintf( 'process_onepay_response_data - Order ID: %1$s - Order Note: %2$s - http_args: %3$s', $order_id, $order_note, print_r( $args, true ) );
			self::log( $message_log );

			// Do action for the order based on the response code from OnePay
			// This is an intentional DRY switch - refer to #DRY_vpc_TxnResponseCode below
			switch ( $vpc_TxnResponseCode ) {
				case '0':
					// If the payment is successful, update the order
					$order->payment_complete();
					break;
				case '99':
					// If the user cancels payment, cancel the order
					$order->update_status( 'cancelled' );
					break;
				default:
					// For other cases, do nothing. By default, the order status is still "Pending Payment"
					break;
			}

			// Do the last actions based on $type
			switch ( $type ) {

				case 'return': // Add info from OnePay and redirect to the appropriate URLs

					wc_add_notice( __( 'OnePay info: ', 'woo-viet' ) . $this->OnePay_getResponseDescription( $vpc_TxnResponseCode ), 'notice' );

					// This is an intentional DRY switch - refer to #DRY_vpc_TxnResponseCode above
					// I need to make sure that `ipn` case below and message_log can be executed as well.
					switch ( $vpc_TxnResponseCode ) {
						case '0':
							// If the payment is successful, redirect to the order page
							wp_redirect( $this->get_return_url( $order ) );
							break;
						case '99':
							// If the user cancels payment, redirect to the canceled cart page
							wp_redirect( $order->get_cancel_order_url_raw() );
							break;
						default:
							// For other cases, redirect to the payment page
							wp_redirect( $order->get_checkout_payment_url() );
							break;
					}

					break;

				case 'ipn': // Output the data to the page content
					exit( 'responsecode=1&desc=confirm-success' );
					break;

				case 'querydr':
					// Do nothing
					break;
			}

		} else {
			if ( 'ipn' == $type ) {
				exit( 'responsecode=0&desc=confirm-success' );
			}

		}
	}

	/**
	 * Whether or not the arguments and a provided $vpc_SecureHash are the same
	 *
	 * @see https://mtf.onepay.vn/developer/?page=modul_noidia_php
	 *
	 * @param $args
	 * @param $vpc_SecureHash
	 *
	 * @return bool
	 */
	public function check_vpc_SecureHash( $args, $vpc_SecureHash ) {
		// Generate the "vpc_SecureHash" value from $args
		$vpc_SecureHash_from_args = $this->create_vpc_SecureHash( $args );

		if ( $vpc_SecureHash_from_args == $vpc_SecureHash ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get the response description based on the response code
	 * This is code is from OnePay
	 *
	 * @param string $responseCode
	 *
	 * @return string
	 */
	abstract public function OnePay_getResponseDescription( $responseCode );

	/**
	 * Handle the IPN POST request from OnePay
	 */
	public function handle_onepay_ipn() {

		if ( isset( $_REQUEST['vpc_SecureHash'] ) ) {

			$this->process_onepay_response_data( $_REQUEST, 'ipn' );

		}
	}

	/**
	 * Handle the queryDR request
	 *
	 * @param string $vpc_MerchTxnRef
	 */
	public function handle_onepay_querydr( $vpc_MerchTxnRef ) {
		// Build the queryDR link
		$args = array(
			'vpc_Command'     => 'queryDR',
			'vpc_Version'     => '1',
			'vpc_MerchTxnRef' => $vpc_MerchTxnRef,
			'vpc_Merchant'    => $this->merchant_id,
			'vpc_AccessCode'  => $this->access_code,
			'vpc_User'        => $this->user,
			'vpc_Password'    => $this->password,
		);

		$http_args = http_build_query( $args, '', '&' );

		$http_link = $this->get_onepay_querydr_link( $this->testmode ) . '?' . $http_args;

		// Log data
		$message_log = sprintf( 'handle_onepay_querydr - http_link: %1$s - http_args: %2$s', $http_link, print_r( $args, true ) );
		self::log( $message_log );

		// Connect to OnePay to get the queryDR info
		$http_response = wp_remote_get( $http_link );
		parse_str( wp_remote_retrieve_body( $http_response ), $args_response );

		// Process the data
		$this->process_onepay_response_data( $args_response, 'querydr' );

	}

	/**
	 * Logging method. - Copied from the WC_Gateway_Paypal Class
	 *
	 * @since 1.3.1
	 *
	 * @param string $message Log message.
	 * @param string $level Optional. Default 'info'.
	 *     emergency|alert|critical|error|warning|notice|info|debug
	 */
	public static function log( $message, $level = 'info' ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {
				self::$log = wc_get_logger();
			}
			self::$log->log( $level, $message, array( 'source' => get_called_class() ) );
		}
	}

}
