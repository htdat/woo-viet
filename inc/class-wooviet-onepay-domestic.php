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
		$access_key = $this->access_key;
		$secret = $this->secret;               // require your secret key from 1pay
		$return_url = $this->get_1pay_return_url();
		$command = 'request_transaction';
		$amount = $order->get_total();   // >10000
		$order_id = $order->id;
		$order_info = sprintf( 'The payment for the order number %1$s on the site: %2$s', $order->id, get_home_url());

		// @todo: review and see the file do.php in the demo files

	}

	// @todo review this function
	/*
	public function static get_return_url(){
		return WC()->api_request_url( __CLASS__ );
	}
	*/
}
