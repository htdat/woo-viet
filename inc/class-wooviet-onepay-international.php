<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to handle the domestic OnePay gateway https://mtf.onepay.vn/developer/?page=modul_quocte
 *
 *
 * @author   htdat
 * @since    1.5.0
 *
 */

require_once('onepay/abstract-payment.php');
class WooViet_OnePay_International extends WooViet_OnePay_Abstract {
	public function configure_payment() {
		$this->method_title       = __( 'OnePay International Gateway (by Woo Viet)', 'woo-viet' );
		$this->method_description = __( 'OnePay supports all major international cards Visa, Master, JCB, Amex, etc.', 'woo-viet' );
	}
	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = include( 'onepay/international-settings.php' );
	}
}