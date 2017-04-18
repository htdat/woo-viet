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
	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                 = strtolower(__CLASS__);
		$this->has_fields         = false;
		$this->order_button_text  = __( 'Proceed to OnePay', 'woo-viet' );
		$this->method_title       = __( 'OnePay Domestic Gateway (by Woo Viet)', 'woo-viet' );
		$this->method_description =  __( 'OnePay supports all major bank ATMs in Vietnam.', 'woo-viet' );
		$this->supports           = array(
			'products',
		);
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables.
		$this->title          = $this->get_option( 'title' );
		$this->description    = $this->get_option( 'description' );
		$this->testmode       = 'yes' === $this->get_option( 'testmode', 'no' );
		$this->merchant_id          = $this->get_option( 'merchant_id' );
		$this->access_code          = $this->get_option( 'access_code' );
		$this->secure_secret          = $this->get_option( 'secure_secret' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_action('woocommerce_thankyou_' . $this->id, array($this, 'handle_onepay_return_url'));

		add_action('woocommerce_api_' . strtolower(__CLASS__), array($this, 'handle_onepay_ipn' ) );
	}
	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = include( 'onepay/domestic-settings.php' );
	}
	/**
	 * Process the payment.
	 */
	public function process_payment( $order_id ) {
		$order          = wc_get_order( $order_id );
		return array(
			'result'   => 'success',
			'redirect' => $this->get_pay_url( $order )
		);
	}
	/**
	 * Get the OnePay pay URL for an order.
	 * @param  WC_Order $order
	 * @return string
	 */
	public function get_pay_url ( $order ) {
		$args = array(
			'Title' =>  __('OnePay Payment Title', 'woo-viet'),
			'vpc_Merchant' => $this->merchant_id,
			'vpc_AccessCode' => $this->access_code,
			'vpc_MerchTxnRef' => sprintf('%1$s_%2$s', $order->id, date ( 'YmdHis' )),
			'vpc_OrderInfo' => substr(
				sprintf('Order #%1$s - %2$s', $order->id, get_home_url()),
				0,
				32 ), // Limit 32 characters
			'vpc_Amount' => $order->get_total() * 100, // Multiplying 100 is a requirement from OnePay
			'vpc_ReturnURL' => $this->get_return_url($order),
			'vpc_Version' => '2',
			'vpc_Command' => 'pay',
			'vpc_Locale' => ( 'vi' == get_locale() ) ? 'vn' : 'en',
			'vpc_Currency' => 'VND',
			'vpc_TicketNo' => $_SERVER['REMOTE_ADDR'],
		);

		// Get the secure hash
		$vpc_SecureHash = $this->create_vpc_SecureHash( $args );

		// Add the secure hash to the args
		$args['vpc_SecureHash'] = $vpc_SecureHash;
		$http_args = http_build_query( $args, '', '&' );


		if ( $this->testmode ) {
			return 'https://mtf.onepay.vn/onecomm-pay/vpc.op?' . $http_args;
		} else {
			return 'https://onepay.vn/onecomm-pay/vpc.op?' . $http_args;
		}



	}
	// @todo: need to check this and WC_Payment_Gateway::get_return_url($order = NULL). This might be used for IPN only
	public function get_onepay_return_url(){
		return WC()->api_request_url( __CLASS__ );
	}

	/**
	 * @param  array $args
	 * @return string
	 */
	public function create_vpc_SecureHash ( $args ){
		$stringHashData = "";

		// arrange array data a-z before make a hash
		ksort ($args );

		foreach($args as $key => $value) {

			// tạo chuỗi đầu dữ liệu những tham số có dữ liệu
			if (strlen($value) > 0) {
				//sử dụng cả tên và giá trị tham số để mã hóa
				if ((strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
					$stringHashData .= $key . "=" . $value . "&";
				}
			}
		}
		//xóa ký tự & ở thừa ở cuối chuỗi dữ liệu mã hóa*****************************
		$stringHashData = rtrim($stringHashData, "&");

		return strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', $this->secure_secret )));
	}


	public function check_vpc_SecureHash ($args, $vpc_SecureHash) {
		// Generate the "vpc_SecureHash" value from $args
		$vpc_SecureHash_from_args = $this->create_vpc_SecureHash($args);

		if ($vpc_SecureHash_from_args == $vpc_SecureHash ) {
			return true;
		} else {
			return false;
		}
	}

	public function handle_onepay_return_url(){
		if (isset($_GET['vpc_SecureHash'])) {

			$this->process_onepay_response_data( $_GET );

			// @todo NEXT check this part
			// Redirect to the return URL without the OnePay parameters
			// wp_redirect($this->get_return_url( $order ));
		}
	}

	public function process_onepay_response_data ($args ) {
		$vpc_SecureHash = $args['vpc_SecureHash'];

		// Remove the parameter "vpc_SecureHash" for validating SecureHash
		unset($args['vpc_SecureHash']);

		$check_vpc_SecureHash = $this->check_vpc_SecureHash($args, $vpc_SecureHash);

		if ( $check_vpc_SecureHash ) {
			/**
			 * $vpc_MerchTxnRef looks like this "139_20170418101843" or {order_id}_{date_time}
			 * @see $this->get_pay_url();
			 */
			$vpc_MerchTxnRef = $args['vpc_MerchTxnRef'];

			// Get the order_id part only
			$order_id = substr($vpc_MerchTxnRef,0,strrpos($vpc_MerchTxnRef,'_'));

			$order = wc_get_order($order_id);

			$vpc_TxnResponseCode = $args['vpc_TxnResponseCode'];

			// The payment was made successfully
			if ("0" == $vpc_TxnResponseCode ) {
				$order->payment_complete();
			}

			// Add the order note for the reference
			$order_note = sprintf(
				__('OnePay Domestic Gateway Info | Code: %1$s | Message: %2$s | MerchantTxnRef: %3$s', 'woo-viet'),
				$vpc_TxnResponseCode,
				$this->OnePay_getResponseDescription($vpc_TxnResponseCode),
				$vpc_MerchTxnRef
			);
			$order->add_order_note($order_note);

			// return $order;

		}
	}

	public function handle_onepay_ipn() {
		if (isset($_POST['vpc_SecureHash'])) {

			$this->process_onepay_response_data( $_POST );

			// Write the response
		}
	}

	public function OnePay_getResponseDescription($responseCode) {

		switch ($responseCode) {
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




}
