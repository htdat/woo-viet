<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings for OnePay Domestic Gateway.
 * @since 1.3
 */
return array(
	'enabled'       => array(
		'title'   => __( 'Enable/Disable', 'woo-viet' ),
		'type'    => 'checkbox',
		'label'   => __( 'OnePay Domestic Gateway (by Woo Viet)', 'woo-viet' ),
		'default' => 'no'
	),
	'testmode'      => array(
		'title'       => __( 'OnePay Sandbox', 'woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable OnePay sandbox (testing)', 'woocommerce' ),
		'default'     => 'no',
		'description' => sprintf( __( 'OnePay sandbox can be used to test payments. See <a href="%s">the testing info</a>.', 'woocommerce' ), 'https://mtf.onepay.vn/developer/?page=modul_noidia' ),
	),
	'title'         => array(
		'title'       => __( 'Title', 'woo-viet' ),
		'type'        => 'text',
		'description' => __( 'This controls the title which the user sees during checkout.', 'woo-viet' ),
		'default'     => __( 'OnePay Domestic Gateway', 'woo-viet' ),
		'desc_tip'    => true,
	),
	'description'   => array(
		'title'       => __( 'Description', 'woo-viet' ),
		'type'        => 'textarea',
		'desc_tip'    => true,
		'description' => __( 'This controls the description which the user sees during checkout.', 'woo-viet' ),
		'default'     => __( 'With OnePay, you can make payment by using any local Vietnam ATM card.', 'woo-viet' )
	),
	'order_button_text'   => array(
		'title'       => __( 'Button text', 'woo-viet' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Button label in the checkout page.', 'woo-viet' ),
		'default'     => __( 'Pay with ATM cards', 'woo-viet' )
	),
	'api_details'   => array(
		'title'       => __( 'API Credentials', 'woo-viet' ),
		'type'        => 'title',
		'description' => sprintf( __( 'Enter your OnePay API credentials. Contact OnePay to have your credentials %shere%s.', 'woo-viet' ), '<a href="http://onepay.com.vn/home/en/contact-us.html">', '</a>' ),
	),
	'merchant_id'   => array(
		'title'       => __( 'Merchant ID', 'woo-viet' ),
		'type'        => 'text',
		'description' => __( 'Get your Merchant ID from OnePay.', 'woo-viet' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Required. Provided by OnePay.', 'woo-viet' )
	),
	'access_code'   => array(
		'title'       => __( 'Access Code', 'woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Get your Access Code from OnePay.', 'woo-viet' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Required. Provided by OnePay.', 'woo-viet' )
	),
	'secure_secret' => array(
		'title'       => __( 'Secure Secret', 'woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Get your Secure Secret from OnePay.', 'woo-viet' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Required. Provided by OnePay.', 'woo-viet' )
	),
	'user'          => array(
		'title'       => __( 'User for queryDR. Test value: op01', 'woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Get your user info from OnePay.', 'woo-viet' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Required. Provided by OnePay', 'woo-viet' )
	),
	'password'      => array(
		'title'       => __( 'Password for queryDR. Test value: op123456', 'woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Get your password info from OnePay.', 'woo-viet' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Required. Provided by OnePay.', 'woo-viet' )
	),
	'more_info'     => array(
		'title'       => __( 'Instant Payment Notification (IPN)', 'woo-viet' ),
		'type'        => 'title',
		'description' =>
			sprintf( 'URL: <code>%s</code>', WooViet_OnePay_Domestic::get_onepay_ipn_url() ) . '<p/>' .
			sprintf( __( '%sContact OnePay%s to configure this URL on its site. <strong>This is required  based on its guidelines.</strong>', 'woo-viet' ), '<a href="http://onepay.com.vn/home/en/contact-us.html">', '</a>' ),
	),
	/**
	 * @since 1.3.1
	 */
	'debug'         => array(
		'title'       => __( 'Debug log', 'woo-viet' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable logging', 'woo-viet' ),
		'default'     => 'no',
		'description' => sprintf( __( 'Log events, such as IPN requests, inside %s', 'woo-viet' ), '<code>' . WC_Log_Handler_File::get_log_file_path( 'WooViet_OnePay_Domestic' ) . '</code>' ),
	),

);