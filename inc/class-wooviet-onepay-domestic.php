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
		$this->id                 = 'wooviet_onepay_domestic';
		$this->has_fields         = false;
		$this->order_button_text  = __( 'Proceed to OnePay', 'woo-viet' );
		$this->method_title       = __( 'OnePay Domestic Gateway (by Woo Viet)', 'woo-viet' );
		// @todo - check the method description
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

		// @todo: the sandbox option

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
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
			'vpc_ReturnURL' => $this->get_onepay_return_url(),
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
	// @todo: need to check this and WC_Payment_Gateway::get_return_url($order = NULL)
	public function get_onepay_return_url(){
		return WC()->api_request_url( __CLASS__ );
	}

	/**
	 * Get the PayPal request URL for an order.
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

	public function process_return_url (){
		// http://localhost/woo-viet/wc-api/WooViet_OnePay_Domestic/?vpc_AdditionData=686868&vpc_Amount=40000000&vpc_Command=pay&vpc_CurrencyCode=VND&vpc_Locale=en&vpc_MerchTxnRef=%23133_20170418024557&vpc_Merchant=ONEPAY&vpc_OrderInfo=%23133+-+http%3A%2F%2Flocalhost%2Fwoo-viet&vpc_TransactionNo=1576998&vpc_TxnResponseCode=0&vpc_Version=2&vpc_SecureHash=25C4443CFF95F6D5BCE4AD86DAA6756960688BD8868CBE898989125050504361
	}

}
