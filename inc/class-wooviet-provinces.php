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
			'THUA-THIEN-HUE'        => __( 'Thành phố Huế', 'woo-viet' ),
			'HA-NOI'        => __( 'Thành phố Hà Nội', 'woo-viet' ),
			'CAN-THO'        => __( 'Tp Cần Thơ', 'woo-viet' ),
			'HAI-PHONG'        => __( 'Tp Hải Phòng', 'woo-viet' ),
			'HO-CHI-MINH'        => __( 'Tp Hồ Chí Minh', 'woo-viet' ),
			'DA-NANG'        => __( 'Tp Đà Nẵng', 'woo-viet' ),
			'AN-GIANG'        => __( 'Tỉnh An Giang', 'woo-viet' ),
			'BAC-NINH'        => __( 'Tỉnh Bắc Ninh', 'woo-viet' ),
			'CAO-BANG'        => __( 'Tỉnh Cao Bằng', 'woo-viet' ),
			'CA-MAU'        => __( 'Tỉnh Cà Mau', 'woo-viet' ),
			'GIA-LAI'        => __( 'Tỉnh Gia Lai', 'woo-viet' ),
			'HA-TINH'        => __( 'Tỉnh Hà Tĩnh', 'woo-viet' ),
			'HUNG-YEN'        => __( 'Tỉnh Hưng Yên', 'woo-viet' ),
			'KHANH-HOA'        => __( 'Tỉnh Khánh Hòa', 'woo-viet' ),
			'LAI-CHAU'        => __( 'Tỉnh Lai Châu', 'woo-viet' ),
			'LAO-CAI'        => __( 'Tỉnh Lào Cai', 'woo-viet' ),
			'LAM-DONG'        => __( 'Tỉnh Lâm Đồng', 'woo-viet' ),
			'LANG-SON'        => __( 'Tỉnh Lạng Sơn', 'woo-viet' ),
			'NGHE-AN'        => __( 'Tỉnh Nghệ An', 'woo-viet' ),
			'NINH-BINH'        => __( 'Tỉnh Ninh Bình', 'woo-viet' ),
			'PHU-THO'        => __( 'Tỉnh Phú Thọ', 'woo-viet' ),
			'QUANG-NGAI'        => __( 'Tỉnh Quảng Ngãi', 'woo-viet' ),
			'QUANG-NINH'        => __( 'Tỉnh Quảng Ninh', 'woo-viet' ),
			'QUANG-TRI'        => __( 'Tỉnh Quảng Trị', 'woo-viet' ),
			'SON-LA'        => __( 'Tỉnh Sơn La', 'woo-viet' ),
			'THANH-HOA'        => __( 'Tỉnh Thanh Hóa', 'woo-viet' ),
			'THAI-NGUYEN'        => __( 'Tỉnh Thái Nguyên', 'woo-viet' ),
			'TUYEN-QUANG'        => __( 'Tỉnh Tuyên Quang', 'woo-viet' ),
			'TAY-NINH'        => __( 'Tỉnh Tây Ninh', 'woo-viet' ),
			'VINH-LONG'        => __( 'Tỉnh Vĩnh Long', 'woo-viet' ),
			'DIEN-BIEN'        => __( 'Tỉnh Điện Biên', 'woo-viet' ),
			'DAK-LAK'        => __( 'Tỉnh Đắk Lắk', 'woo-viet' ),
			'DONG-NAI'        => __( 'Tỉnh Đồng Nai', 'woo-viet' ),
			'DONG-THAP'        => __( 'Tỉnh Đồng Tháp', 'woo-viet' ),
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
