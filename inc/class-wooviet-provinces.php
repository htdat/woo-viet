<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to handle Vietnam Provinces
 *
 * @author   htdat
 * @since    1.0
 *
 */
class WooViet_Provinces {

	/**
	 * Constructor: Add filters
	 */
	public function __construct() {
		add_filter( 'woocommerce_states', array( $this, 'add_provinces' ) );
		add_filter( 'woocommerce_get_country_locale', array( $this, 'edit_vn_locale' ) );
		add_filter( 'woocommerce_localisation_address_formats', array( $this, 'edit_vn_address_formats' ) );

		// Enqueue province scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_provinces_scripts' ) );
	}

	/**
	 * Change the address format of Vietnam, add {state} (or "Province" in Vietnam)
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public function edit_vn_address_formats( $array ) {

		$array['VN'] = "{name}\n{company}\n{address_1}\n{city}\n{state}\n{country}";

		return $array;

	}

	/**
	 * Change the way displaying address fields in the checkout page when selecting Vietnam
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public function edit_vn_locale( $array ) {
		$array['VN']['state']['label']    = __( 'Province', 'woo-viet' );
		$array['VN']['state']['required'] = true;

		$array['VN']['city']['label']      = __( 'District', 'woo-viet' );
		$array['VN']['postcode']['hidden'] = true;

		return $array;
	}


	/**
	 * Add 63 provinces of Vietnam
	 *
	 * @param $states
	 *
	 * @return array
	 */
	public function add_provinces( $states ) {
		/**
		 * @source: https://vi.wikipedia.org/wiki/Tỉnh_thành_Việt_Nam and https://en.wikipedia.org/wiki/Provinces_of_Vietnam
		 */
		$states['VN'] = array(
			'THANH-PHO-HUE'        => __( 'Thành phố Huế', 'woo-viet' ),
			'THANH-PHO-HA-NOI'        => __( 'Thành phố Hà Nội', 'woo-viet' ),
			'TP-CAN-THO'        => __( 'Tp Cần Thơ', 'woo-viet' ),
			'TP-HAI-PHONG'        => __( 'Tp Hải Phòng', 'woo-viet' ),
			'TP-HO-CHI-MINH'        => __( 'Tp Hồ Chí Minh', 'woo-viet' ),
			'TP-DA-NANG'        => __( 'Tp Đà Nẵng', 'woo-viet' ),
			'TINH-AN-GIANG'        => __( 'Tỉnh An Giang', 'woo-viet' ),
			'TINH-BAC-NINH'        => __( 'Tỉnh Bắc Ninh', 'woo-viet' ),
			'TINH-CAO-BANG'        => __( 'Tỉnh Cao Bằng', 'woo-viet' ),
			'TINH-CA-MAU'        => __( 'Tỉnh Cà Mau', 'woo-viet' ),
			'TINH-GIA-LAI'        => __( 'Tỉnh Gia Lai', 'woo-viet' ),
			'TINH-HA-TINH'        => __( 'Tỉnh Hà Tĩnh', 'woo-viet' ),
			'TINH-HUNG-YEN'        => __( 'Tỉnh Hưng Yên', 'woo-viet' ),
			'TINH-KHANH-HOA'        => __( 'Tỉnh Khánh Hòa', 'woo-viet' ),
			'TINH-LAI-CHAU'        => __( 'Tỉnh Lai Châu', 'woo-viet' ),
			'TINH-LAO-CAI'        => __( 'Tỉnh Lào Cai', 'woo-viet' ),
			'TINH-LAM-DONG'        => __( 'Tỉnh Lâm Đồng', 'woo-viet' ),
			'TINH-LANG-SON'        => __( 'Tỉnh Lạng Sơn', 'woo-viet' ),
			'TINH-NGHE-AN'        => __( 'Tỉnh Nghệ An', 'woo-viet' ),
			'TINH-NINH-BINH'        => __( 'Tỉnh Ninh Bình', 'woo-viet' ),
			'TINH-PHU-THO'        => __( 'Tỉnh Phú Thọ', 'woo-viet' ),
			'TINH-QUANG-NGAI'        => __( 'Tỉnh Quảng Ngãi', 'woo-viet' ),
			'TINH-QUANG-NINH'        => __( 'Tỉnh Quảng Ninh', 'woo-viet' ),
			'TINH-QUANG-TRI'        => __( 'Tỉnh Quảng Trị', 'woo-viet' ),
			'TINH-SON-LA'        => __( 'Tỉnh Sơn La', 'woo-viet' ),
			'TINH-THANH-HOA'        => __( 'Tỉnh Thanh Hóa', 'woo-viet' ),
			'TINH-THAI-NGUYEN'        => __( 'Tỉnh Thái Nguyên', 'woo-viet' ),
			'TINH-TUYEN-QUANG'        => __( 'Tỉnh Tuyên Quang', 'woo-viet' ),
			'TINH-TAY-NINH'        => __( 'Tỉnh Tây Ninh', 'woo-viet' ),
			'TINH-VINH-LONG'        => __( 'Tỉnh Vĩnh Long', 'woo-viet' ),
			'TINH-DIEN-BIEN'        => __( 'Tỉnh Điện Biên', 'woo-viet' ),
			'TINH-DAK-LAK'        => __( 'Tỉnh Đắk Lắk', 'woo-viet' ),
			'TINH-DONG-NAI'        => __( 'Tỉnh Đồng Nai', 'woo-viet' ),
			'TINH-DONG-THAP'        => __( 'Tỉnh Đồng Tháp', 'woo-viet' ),
		);

		return $states;

	}

	/**
	 * Enqueue provinces scripts
	 *
	 * Arrange the address field orders to the Vietnam standard in the checkout page: Country - Province - District - Address
	 * @author    Longkt
	 * @since    1.4
	 */
	public function load_provinces_scripts() {
		// Enqueue province style
		wp_enqueue_style( 'woo-viet-provinces-style', WOO_VIET_URL . 'assets/provinces.css' );

		// Enqueue province script
		wp_enqueue_script( 'woo-viet-provinces-script', WOO_VIET_URL . 'assets/provinces.js', array( 'jquery' ), '1.0', true );
	}
}
