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

	public function get_onepay_payment_link( $testmode ) {
		return $testmode ? 'https://mtf.onepay.vn/vpcpay/vpcpay.op' : 'https://onepay.vn/vpcpay/vpcpay.op';
	}

	public function get_onepay_querydr_link( $testmode ) {
		return $testmode ? 'https://mtf.onepay.vn/vpcpay/Vpcdps.op' : 'https://onepay.vn/vpcpay/Vpcdps.op';
	}

	public function OnePay_getResponseDescription( $responseCode ) {

		switch ( $responseCode ) {
			case "0" :
				$result = "Giao dịch thành công - Approved";
				break;
			case "1" :
			case "2" :
				$result = "Ngân hàng từ chối giao dịch - Bank Declined";
				break;
			case "3" :
				$result = "OnePay không nhận được kết quả giao dịch từ ngân hàng - OnePAY did not received payment result
from Issuer bank";
				break;
			case "4" :
				$result = "Thẻ hết hạn hoặc sai thông tin hết hạn thẻ -  Card expired or incorrect expired date";
				break;
			case "5" :
				$result = "Số tiền không đủ để thanh toán - Insufficient fund";
				break;
			case "6" :
			case "7" :
				$result = "Quá trình xử lý giao dịch phát sinh lỗi - An error was encountered while processing your transaction";
				break;
			case "8" :
				$result = "Thẻ không hỗ trợ giao
dịch thanh toán trên Internet - Ecommerce transaction is not supported for this card";
				break;
			case "9" :
				$result = "Ngân hàng từ chối giao dịch - Bank Declined";
				break;
			case "99" :
				$result = "Người sủ dụng hủy giao dịch - User cancel";
				break;
			case "B" :
			case "F" :
				$result = "Không xác thực được 3D-Secure - 3D Secure Authentication Failed";
				break;
			case "E" :
				$result = "Nhập sai CSC (Card Security Card) hoặc ngân hàng từ chối cấp phép cho giao dịch - You have entered wrong CSC or Issuer Bank declided transaction";
				break;
			case "Z" :
				$result = "Transaction restricted due to OFD’s policies - vi phạm quy định của hệ thống";
				break;
			default :
				$result = "Giao dịch thất bại - Failure";
		}

		return $result;
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = include( 'onepay/international-settings.php' );
	}
}