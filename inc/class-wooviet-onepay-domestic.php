<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to handle the domestic OnePay gateway https://mtf.onepay.vn/developer/?page=modul_noidia
 *
 *
 * @author   htdat
 * @since    1.5.0
 *
 */

require_once('onepay/abstract-payment.php');
class WooViet_OnePay_Domestic extends WooViet_OnePay_Abstract {

	public function configure_payment() {
		$this->method_title       = __( 'OnePay Domestic Gateway (by Woo Viet)', 'woo-viet' );
		$this->method_description = __( 'OnePay supports all major bank ATMs in Vietnam.', 'woo-viet' );
	}

	public function get_onepay_payment_link( $testmode ) {
		return $testmode ? 'https://mtf.onepay.vn/onecomm-pay/vpc.op' : 'https://onepay.vn/onecomm-pay/vpc.op';
	}

	public function get_onepay_querydr_link( $testmode ) {
		return $testmode ? 'https://mtf.onepay.vn/onecomm-pay/Vpcdps.op' : 'https://onepay.vn/onecomm-pay/Vpcdps.op';
	}

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
				$result = "Vượt quá hạn mức thanh toán - Exceeds the maximum limit";
				break;
			case "21" :
				$result = "Số tiền không đủ để thanh toán - Insufficient fund";
				break;
			case "22" :
				$result = "Thông tin tài khoản không đúng - Invalid account ìnfo";
				break;
			case "23" :
				$result = "Tài khoản bị khóa - Account locked";
				break;
			case "24" :
				$result = "Thông tin thẻ không đúng - Incorrect card number";
				break;
			case "25" :
				$result = "OTP không đúng - Incorrect OTP";
				break;
			case "99" :
				$result = "Người sủ dụng hủy giao dịch - User cancel";
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
		$this->form_fields = include( 'onepay/domestic-settings.php' );
	}
}