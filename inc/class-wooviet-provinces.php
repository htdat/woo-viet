<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to handle Vietnam Provinces
 *
 * @todo Arrange the orders of fields displaying in the checkout page: Country - Province - District - Address
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
		$array['VN']['state']['label']    = __( 'Province', 'woocommerce' );
		$array['VN']['state']['required'] = true;

		$array['VN']['city']['label']      = __( 'District', 'woocommerce' );
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
			'AN-GIANG'        => __( 'An Giang', 'woocommerce-for-vietnam' ),
			'BA-RIA-VUNG-TAU' => __( 'Ba Ria - Vung Tau', 'woocommerce-for-vietnam' ),
			'BAC-LIEU'        => __( 'Bac Lieu', 'woocommerce-for-vietnam' ),
			'BAC-KAN'         => __( 'Bac Kan', 'woocommerce-for-vietnam' ),
			'BAC-GIANG'       => __( 'Bac Giang', 'woocommerce-for-vietnam' ),
			'BAC-NINH'        => __( 'Bac Ninh', 'woocommerce-for-vietnam' ),
			'BEN-TRE'         => __( 'Ben Tre', 'woocommerce-for-vietnam' ),
			'BINH-DUONG'      => __( 'Binh Duong', 'woocommerce-for-vietnam' ),
			'BINH-DINH'       => __( 'Binh Dinh', 'woocommerce-for-vietnam' ),
			'BINH-PHUOC'      => __( 'Binh Phuoc', 'woocommerce-for-vietnam' ),
			'BINH-THUAN'      => __( 'Binh Thuan', 'woocommerce-for-vietnam' ),
			'CA-MAU'          => __( 'Ca Mau', 'woocommerce-for-vietnam' ),
			'CAO-BANG'        => __( 'Cao Bang', 'woocommerce-for-vietnam' ),
			'CAN-THO'         => __( 'Can Tho', 'woocommerce-for-vietnam' ),
			'DA-NANG'         => __( 'Da Nang', 'woocommerce-for-vietnam' ),
			'DAK-LAK'         => __( 'Dak Lak', 'woocommerce-for-vietnam' ),
			'DAK-NONG'        => __( 'Dak Nong', 'woocommerce-for-vietnam' ),
			'DONG-NAI'        => __( 'Dong Nai', 'woocommerce-for-vietnam' ),
			'DONG-THAP'       => __( 'Dong Thap', 'woocommerce-for-vietnam' ),
			'DIEN-BIEN'       => __( 'Dien Bien', 'woocommerce-for-vietnam' ),
			'GIA-LAI'         => __( 'Gia Lai', 'woocommerce-for-vietnam' ),
			'HA-GIANG'        => __( 'Ha Giang', 'woocommerce-for-vietnam' ),
			'HA-NAM'          => __( 'Ha Nam', 'woocommerce-for-vietnam' ),
			'HA-NOI'          => __( 'Ha Noi', 'woocommerce-for-vietnam' ),
			'HA-TINH'         => __( 'Ha Tinh', 'woocommerce-for-vietnam' ),
			'HAI-DUONG'       => __( 'Hai Duong', 'woocommerce-for-vietnam' ),
			'HAI-PHONG'       => __( 'Hai Phong', 'woocommerce-for-vietnam' ),
			'HOA-BINH'        => __( 'Hoa Binh', 'woocommerce-for-vietnam' ),
			'HAU-GIANG'       => __( 'Hau Giang', 'woocommerce-for-vietnam' ),
			'HUNG-YEN'        => __( 'Hung Yen', 'woocommerce-for-vietnam' ),
			'HO-CHI-MINH'     => __( 'Ho Chi Minh', 'woocommerce-for-vietnam' ),
			'KHANH-HOA'       => __( 'Khanh Hoa', 'woocommerce-for-vietnam' ),
			'KIEN-GIANG'      => __( 'Kien Giang', 'woocommerce-for-vietnam' ),
			'KON-TUM'         => __( 'Kon Tum', 'woocommerce-for-vietnam' ),
			'LAI-CHAU'        => __( 'Lai Chau', 'woocommerce-for-vietnam' ),
			'LAO-CAI'         => __( 'Lao Cai', 'woocommerce-for-vietnam' ),
			'LANG-SON'        => __( 'Lang Son', 'woocommerce-for-vietnam' ),
			'LAM-DONG'        => __( 'Lam Dong', 'woocommerce-for-vietnam' ),
			'LONG-AN'         => __( 'Long An', 'woocommerce-for-vietnam' ),
			'NAM-DINH'        => __( 'Nam Dinh', 'woocommerce-for-vietnam' ),
			'NGHE-AN'         => __( 'Nghe An', 'woocommerce-for-vietnam' ),
			'NINH-BINH'       => __( 'Ninh Binh', 'woocommerce-for-vietnam' ),
			'NINH-THUAN'      => __( 'Ninh Thuan', 'woocommerce-for-vietnam' ),
			'PHU-THO'         => __( 'Phu Tho', 'woocommerce-for-vietnam' ),
			'PHU-YEN'         => __( 'Phu Yen', 'woocommerce-for-vietnam' ),
			'QUANG-BINH'      => __( 'Quang Binh', 'woocommerce-for-vietnam' ),
			'QUANG-NAM'       => __( 'Quang Nam', 'woocommerce-for-vietnam' ),
			'QUANG-NGAI'      => __( 'Quang Ngai', 'woocommerce-for-vietnam' ),
			'QUANG-NINH'      => __( 'Quang Ninh', 'woocommerce-for-vietnam' ),
			'QUANG-TRI'       => __( 'Quang Tri', 'woocommerce-for-vietnam' ),
			'SOC-TRANG'       => __( 'Soc Trang', 'woocommerce-for-vietnam' ),
			'SON-LA'          => __( 'Son La', 'woocommerce-for-vietnam' ),
			'TAY-NINH'        => __( 'Tay Ninh', 'woocommerce-for-vietnam' ),
			'THAI-BINH'       => __( 'Thai Binh', 'woocommerce-for-vietnam' ),
			'THAI-NGUYEN'     => __( 'Thai Nguyen', 'woocommerce-for-vietnam' ),
			'THANH-HOA'       => __( 'Thanh Hoa', 'woocommerce-for-vietnam' ),
			'THUA-THIEN-HUE'  => __( 'Thua Thien - Hue', 'woocommerce-for-vietnam' ),
			'TIEN-GIANG'      => __( 'Tien Giang', 'woocommerce-for-vietnam' ),
			'TRA-VINH'        => __( 'Tra Vinh', 'woocommerce-for-vietnam' ),
			'TUYEN-QUANG'     => __( 'Tuyen Quang', 'woocommerce-for-vietnam' ),
			'VINH-LONG'       => __( 'Vinh Long', 'woocommerce-for-vietnam' ),
			'VINH-PHUC'       => __( 'Vinh Phuc', 'woocommerce-for-vietnam' ),
			'YEN-BAI'         => __( 'Yen Bai', 'woocommerce-for-vietnam' ),
		);

		return $states;

	}
}