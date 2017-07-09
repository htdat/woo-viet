<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to handle the domestic OnePay gateway https://mtf.onepay.vn/developer/?page=modul_noidia
 *
 *
 * @author   htdat
 * @since    1.3
 *
 */
class WooViet_OnePay_Domestic extends WC_Payment_Gateway {
	/** @var bool Whether or not logging is enabled */
	public static $log_enabled = false;

	/** @var WC_Logger Logger instance */
	public static $log = false;

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                 = strtolower( __CLASS__ );
		$this->has_fields         = false;
		$this->order_button_text  = __( 'Proceed to OnePay', 'woo-viet' );
		$this->method_title       = __( 'OnePay Domestic Gateway (by Woo Viet)', 'woo-viet' );
		$this->method_description = __( 'OnePay supports all major bank ATMs in Vietnam.', 'woo-viet' );
		$this->supports           = array(
			'products',
		);

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title         = $this->get_option( 'title' );
		$this->description   = $this->get_option( 'description' ) . sprintf( '<br/><div align="center"><img src="%s"></div>', WOO_VIET_URL . 'assets/onepay_domestic.png' );

		$this->testmode      = 'yes' === $this->get_option( 'testmode', 'no' );
		$this->merchant_id   = $this->get_option( 'merchant_id' );
		$this->access_code   = $this->get_option( 'access_code' );
		$this->secure_secret = $this->get_option( 'secure_secret' );
		$this->user          = $this->get_option( 'user' );
		$this->password      = $this->get_option( 'password' );
		$this->debug          = 'yes' === $this->get_option( 'debug', 'no' );

		self::$log_enabled    = $this->debug;

		// Process the admin options
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
			$this,
			'process_admin_options'
		) );

		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'handle_onepay_return_url' ) );

		add_action( 'woocommerce_api_' . strtolower( __CLASS__ ), array( $this, 'handle_onepay_ipn' ) );

	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = include( 'onepay/domestic-settings.php' );
	}

	/**
	 * Get the IPN URL for OnePay
	 * Format: http://my-site.com/wc-api/WooViet_OnePay_Domestic/
	 */
	static function get_onepay_ipn_url() {
		return WC()->api_request_url( __CLASS__ );
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
		$args = array(
			'Title'           => __( 'OnePay Payment Title', 'woo-viet' ),
			'vpc_Merchant'    => $this->merchant_id,
			'vpc_AccessCode'  => $this->access_code,
			'vpc_MerchTxnRef' => sprintf( '%1$s_%2$s', $order->get_id(), date( 'YmdHis' ) ),
			'vpc_OrderInfo'   => substr(
				sprintf( 'Order #%1$s - %2$s', $order->get_id(), get_home_url() ),
				0,
				32 ), // Limit 32 characters
			'vpc_Amount'      => $order->get_total() * 100, // Multiplying 100 is a requirement from OnePay
			'vpc_ReturnURL'   => $this->get_return_url( $order ),
			'vpc_Version'     => '2',
			'vpc_Command'     => 'pay',
			'vpc_Locale'      => ( 'vi' == get_locale() ) ? 'vn' : 'en',
			'vpc_Currency'    => 'VND',
			'vpc_TicketNo'    => $_SERVER['REMOTE_ADDR'],
		);

		// Set the queryDR cron for this transaction
		$this->set_onepay_querydr_cron( $args['vpc_MerchTxnRef'] );

		// Get the secure hash
		$vpc_SecureHash = $this->create_vpc_SecureHash( $args );

		// Add the secure hash to the args
		$args['vpc_SecureHash'] = $vpc_SecureHash;
		$http_args              = http_build_query( $args, '', '&' );

		// Log data
		$message_log = sprintf('get_pay_url - Order ID: %1$s - http_args: %2$s', $order->get_id(), print_r($args, true) );
		self::log( $message_log);

		if ( $this->testmode ) {
			return 'https://mtf.onepay.vn/onecomm-pay/vpc.op?' . $http_args;
		} else {
			return 'https://onepay.vn/onecomm-pay/vpc.op?' . $http_args;
		}

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
			'wooviet_handle_onepay_querydr',
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

		if ( 'return' == $type OR 'ipn' == $type ) {
			$vpc_SecureHash = $args['vpc_SecureHash'];

			// Remove the parameter "vpc_SecureHash" for validating SecureHash
			unset( $args['vpc_SecureHash'] );

			$is_secure = $this->check_vpc_SecureHash( $args, $vpc_SecureHash );

		} elseif ( 'querydr' == $type ) {
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
			$order_id = substr( $vpc_MerchTxnRef, 0, strrpos( $vpc_MerchTxnRef, '_' ) );

			$order = wc_get_order( $order_id );

			// Add the order note for the reference
			$order_note = sprintf(
				__( 'OnePay Domestic Gateway Info | Code: %1$s | Message: %2$s | MerchantTxnRef: %3$s | Type: %4$s', 'woo-viet' ),
				$vpc_TxnResponseCode,
				$this->OnePay_getResponseDescription( $vpc_TxnResponseCode ),
				$vpc_MerchTxnRef,
				$type
			);
			$order->add_order_note( $order_note );

			// If the payment is successful, update the order
			if ( "0" == $vpc_TxnResponseCode ) {
				$order->payment_complete();
			}
			// Log data
			$message_log = sprintf('process_onepay_response_data - Order ID: %1$s - Order Note: %2$s - http_args: %3$s', $order_id, $order_note, print_r($args, true) );
			self::log( $message_log);

			// Return the info
			switch ( $type ) {

				case 'return':
					wp_redirect( $this->get_return_url( $order ) );
					break;

				case 'ipn':
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
	public function OnePay_getResponseDescription( $responseCode ) {

		switch ( $responseCode ) {
			case "0" :
				$result = "Giao dịch thành công - Approved";
				break;
			case "1" :
				$result = "Ngân hàng từ chối giao dịch - Bank Declined";
				break;
			case "3" :
				$result = "Mã đơn vị không tồn tại - Merchant not exist";
				break;
			case "4" :
				$result = "Không đúng access code - Invalid access code";
				break;
			case "5" :
				$result = "Số tiền không hợp lệ - Invalid amount";
				break;
			case "6" :
				$result = "Mã tiền tệ không tồn tại - Invalid currency code";
				break;
			case "7" :
				$result = "Lỗi không xác định - Unspecified Failure ";
				break;
			case "8" :
				$result = "Số thẻ không đúng - Invalid card Number";
				break;
			case "9" :
				$result = "Tên chủ thẻ không đúng - Invalid card name";
				break;
			case "10" :
				$result = "Thẻ hết hạn/Thẻ bị khóa - Expired Card";
				break;
			case "11" :
				$result = "Thẻ chưa đăng ký sử dụng dịch vụ - Card Not Registed Service(internet banking)";
				break;
			case "12" :
				$result = "Ngày phát hành/Hết hạn không đúng - Invalid card date";
				break;
			case "13" :
				$result = "Vượt quá hạn mức thanh toán - Exist Amount";
				break;
			case "21" :
				$result = "Số tiền không đủ để thanh toán - Insufficient fund";
				break;
			case "99" :
				$result = "Người sủ dụng hủy giao dịch - User cancel";
				break;
			default :
				$result = "Giao dịch thất bại - Failured";
		}

		return $result;
	}

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

		if ( $this->testmode ) {
			$http_link = 'https://mtf.onepay.vn/onecomm-pay/Vpcdps.op?' . $http_args;
		} else {
			$http_link = 'https://onepay.vn/onecomm-pay/Vpcdps.op?' . $http_args;
		}

		// Log data
		$message_log = sprintf('handle_onepay_querydr - http_link: %1$s - http_args: %2$s', $http_link, print_r($args, true) );
		self::log( $message_log);

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
	 * @param string $message Log message.
	 * @param string $level   Optional. Default 'info'.
	 *     emergency|alert|critical|error|warning|notice|info|debug
	 */
	public static function log( $message, $level = 'info' ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {
				self::$log = wc_get_logger();
			}
			self::$log->log( $level, $message, array( 'source' => __CLASS__ ) );
		}
	}

}
