<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Vietnam Cities to WooCommerce
 *
 * @credit: https://github.com/8manos/wc-city-select
 * @author   htdat
 * @since    1.2
 *
 *
 */
class WooViet_Cities {

	public function __construct() {

		/**
		 * Load the 'WC City Select' class if this plugin is NOT active
		 */
		if ( ! class_exists( 'WC_City_Select' ) ) {
			include( WOO_VIET_DIR . 'lib/wc-city-select/wc-city-select.php' );
		}

		add_filter( 'wc_city_select_cities', array( $this, 'add_cities' ) );

		// Change priority country -> city -> district -> address for Viet Nam
		add_filter( 'woocommerce_default_address_fields', array( $this, 'custom_fields_priority') );

	}

	/**
	 * Add Vietnam Cities
	 *
	 * @param $cities
	 *
	 * @return array
	 */
	public function add_cities( $cities ) {
		/**
		 * @source: https://github.com/htdat/woo-viet/issues/4#issuecomment-277449462
		 * @source: https://gist.github.com/10h30/7e9307d405ff9ef88cf7d226c90a5d13
		 */
		$cities['VN'] = array(
			'AN-GIANG'        => array(
				'Huyện An Phú',
				'Huyện Châu Phú',
				'Huyện Châu Thành',
				'Huyện Chợ Mới',
				'Huyện Phú Tân',
				'Huyện Thoại Sơn',
				'Huyện Tịnh Biên',
				'Huyện Tri Tôn',
				'Thành phố Châu Đốc',
				'Thành phố Long Xuyên',
				'Thị xã Tân Châu'
			),
			'BA-RIA-VUNG-TAU' => array(
				'Huyện Châu Đức',
				'Huyện Côn Đảo',
				'Huyện Đất Đỏ',
				'Huyện Long Điền',
				'Huyện Tân Thành',
				'Huyện Xuyên Mộc',
				'Thành phố Bà Rịa',
				'Thành phố Vũng Tàu'
			),
			'BAC-LIEU'        => array(
				'Huyện Đông Hải',
				'Huyện Hoà Bình',
				'Huyện Hồng Dân',
				'Huyện Phước Long',
				'Huyện Vĩnh Lợi',
				'Thành phố Bạc Liêu',
				'Thị xã Giá Rai'
			),
			'BAC-KAN'         => array(
				'Huyện Ba Bể',
				'Huyện Bạch Thông',
				'Huyện Chợ Đồn',
				'Huyện Chợ Mới',
				'Huyện Na Rì',
				'Huyện Ngân Sơn',
				'Huyện Pác Nặm',
				'Thành Phố Bắc Kạn',
			),
			'BAC-GIANG'       => array(
				'Huyện Hiệp Hòa',
				'Huyện Lạng Giang',
				'Huyện Lục Nam',
				'Huyện Lục Ngạn',
				'Huyện Sơn Động',
				'Huyện Tân Yên',
				'Huyện Việt Yên',
				'Huyện Yên Dũng',
				'Huyện Yên Thế',
				'Thành phố Bắc Giang'
			),
			'BAC-NINH'        => array(
				'Huyện Gia Bình',
				'Huyện Lương Tài',
				'Huyện Quế Võ',
				'Huyện Thuận Thành',
				'Huyện Tiên Du',
				'Huyện Yên Phong',
				'Thành phố Bắc Ninh',
				'Thị xã Từ Sơn'
			),
			'BEN-TRE'         => array(
				'Huyện Ba Tri',
				'Huyện Bình Đại',
				'Huyện Châu Thành',
				'Huyện Chợ Lách',
				'Huyện Giồng Trôm',
				'Huyện Mỏ Cày Bắc',
				'Huyện Mỏ Cày Nam',
				'Huyện Thạnh Phú',
				'Thành phố Bến Tre'
			),
			'BINH-DUONG'      => array(
				'Huyện Bắc Tân Uyên',
				'Huyện Bàu Bàng',
				'Huyện Dầu Tiếng',
				'Huyện Phú Giáo',
				'Thành phố Thủ Dầu Một',
				'Thị xã Bến Cát',
				'Thị xã Dĩ An',
				'Thị xã Tân Uyên',
				'Thị xã Thuận An'
			),
			'BINH-DINH'       => array(
				'Huyện An Lão',
				'Huyện Hoài Ân',
				'Huyện Hoài Nhơn',
				'Huyện Phù Cát',
				'Huyện Phù Mỹ',
				'Huyện Tây Sơn',
				'Huyện Tuy Phước',
				'Huyện Vân Canh',
				'Huyện Vĩnh Thạnh',
				'Thành phố Qui Nhơn',
				'Thị xã An Nhơn'
			),
			'BINH-PHUOC'      => array(
				'Huyện Bù Đăng',
				'Huyện Bù Đốp',
				'Huyện Bù Gia Mập',
				'Huyện Chơn Thành',
				'Huyện Đồng Phú',
				'Huyện Hớn Quản',
				'Huyện Lộc Ninh',
				'Huyện Phú Riềng',
				'Thị xã Bình Long',
				'Thị xã Đồng Xoài',
				'Thị xã Phước Long'
			),
			'BINH-THUAN'      => array(
				'Huyện Bắc Bình',
				'Huyện Đức Linh',
				'Huyện Hàm Tân',
				'Huyện Hàm Thuận Bắc',
				'Huyện Hàm Thuận Nam',
				'Huyện Phú Quí',
				'Huyện Tánh Linh',
				'Huyện Tuy Phong',
				'Thành phố Phan Thiết',
				'Thị xã La Gi'
			),
			'CA-MAU'          => array(
				'Huyện Cái Nước',
				'Huyện Đầm Dơi',
				'Huyện Năm Căn',
				'Huyện Ngọc Hiển',
				'Huyện Phú Tân',
				'Huyện Thới Bình',
				'Huyện Trần Văn Thời',
				'Huyện U Minh',
				'Thành phố Cà Mau'
			),
			'CAO-BANG'        => array(
				'Huyện Bảo Lạc',
				'Huyện Bảo Lâm',
				'Huyện Hạ Lang',
				'Huyện Hà Quảng',
				'Huyện Hoà An',
				'Huyện Nguyên Bình',
				'Huyện Phục Hoà',
				'Huyện Quảng Uyên',
				'Huyện Thạch An',
				'Huyện Thông Nông',
				'Huyện Trà Lĩnh',
				'Huyện Trùng Khánh',
				'Thành phố Cao Bằng'
			),
			'CAN-THO'         => array(
				'Huyện Cờ Đỏ',
				'Huyện Phong Điền',
				'Huyện Thới Lai',
				'Huyện Vĩnh Thạnh',
				'Quận Bình Thuỷ',
				'Quận Cái Răng',
				'Quận Ninh Kiều',
				'Quận Ô Môn',
				'Quận Thốt Nốt',
			),
			'DA-NANG'         => array(
				'Huyện Hòa Vang',
				'Huyện Hoàng Sa',
				'Quận Cẩm Lệ',
				'Quận Hải Châu',
				'Quận Liên Chiểu',
				'Quận Ngũ Hành Sơn',
				'Quận Sơn Trà',
				'Quận Thanh Khê',
			),
			'DAK-LAK'         => array(
				'Huyện Buôn Đôn',
				'Huyện Cư Kuin',
				'Huyện Cư M gar',
				'Huyện Ea H leo',
				'Huyện Ea Kar',
				'Huyện Ea Súp',
				'Huyện Krông A Na',
				'Huyện Krông Bông',
				'Huyện Krông Búk',
				'Huyện Krông Năng',
				'Huyện Krông Pắc',
				'Huyện Lắk',
				'Huyện M Đrắk',
				'Thành phố Buôn Ma Thuột',
				'Thị Xã Buôn Hồ'
			),
			'DAK-NONG'        => array(
				'Huyện Cư Jút',
				'Huyện Đăk Glong',
				'Huyện Đắk Mil',
				'Huyện Đắk R Lấp',
				'Huyện Đắk Song',
				'Huyện Krông Nô',
				'Huyện Tuy Đức',
				'Thị xã Gia Nghĩa'
			),
			'DONG-NAI'        => array(
				'Huyện Cẩm Mỹ',
				'Huyện Định Quán',
				'Huyện Long Thành',
				'Huyện Nhơn Trạch',
				'Huyện Tân Phú',
				'Huyện Thống Nhất',
				'Huyện Trảng Bom',
				'Huyện Vĩnh Cửu',
				'Huyện Xuân Lộc',
				'Thành phố Biên Hòa',
				'Thị xã Long Khánh'
			),
			'DONG-THAP'       => array(
				'Huyện Cao Lãnh',
				'Huyện Châu Thành',
				'Huyện Hồng Ngự',
				'Huyện Lai Vung',
				'Huyện Lấp Vò',
				'Huyện Tam Nông',
				'Huyện Tân Hồng',
				'Huyện Thanh Bình',
				'Huyện Tháp Mười',
				'Thành phố Cao Lãnh',
				'Thành phố Sa Đéc',
				'Thị xã Hồng Ngự'
			),
			'DIEN-BIEN'       => array(
				'Huyện Điện Biên',
				'Huyện Điện Biên Đông',
				'Huyện Mường Ảng',
				'Huyện Mường Chà',
				'Huyện Mường Nhé',
				'Huyện Nậm Pồ',
				'Huyện Tủa Chùa',
				'Huyện Tuần Giáo',
				'Thành phố Điện Biên Phủ',
				'Thị Xã Mường Lay'
			),
			'GIA-LAI'         => array(
				'Huyện Chư Păh',
				'Huyện Chư Prông',
				'Huyện Chư Pưh',
				'Huyện Chư Sê',
				'Huyện Đăk Đoa',
				'Huyện Đăk Pơ',
				'Huyện Đức Cơ',
				'Huyện Ia Grai',
				'Huyện Ia Pa',
				'Huyện KBang',
				'Huyện Kông Chro',
				'Huyện Krông Pa',
				'Huyện Mang Yang',
				'Huyện Phú Thiện',
				'Thành phố Pleiku',
				'Thị xã An Khê',
				'Thị xã Ayun Pa'
			),
			'HA-GIANG'        => array(
				'Huyện Bắc Mê',
				'Huyện Bắc Quang',
				'Huyện Đồng Văn',
				'Huyện Hoàng Su Phì',
				'Huyện Mèo Vạc',
				'Huyện Quản Bạ',
				'Huyện Quang Bình',
				'Huyện Vị Xuyên',
				'Huyện Xín Mần',
				'Huyện Yên Minh',
				'Thành phố Hà Giang'
			),
			'HA-NAM'          => array(
				'Huyện Bình Lục',
				'Huyện Duy Tiên',
				'Huyện Kim Bảng',
				'Huyện Lý Nhân',
				'Huyện Thanh Liêm',
				'Thành phố Phủ Lý'
			),
			'HA-NOI'          => array(
				'Huyện Ba Vì',
				'Huyện Chương Mỹ',
				'Huyện Đan Phượng',
				'Huyện Đông Anh',
				'Huyện Gia Lâm',
				'Huyện Hoài Đức',
				'Huyện Mê Linh',
				'Huyện Mỹ Đức',
				'Huyện Phú Xuyên',
				'Huyện Phúc Thọ',
				'Huyện Quốc Oai',
				'Huyện Sóc Sơn',
				'Huyện Thạch Thất',
				'Huyện Thanh Oai',
				'Huyện Thanh Trì',
				'Huyện Thường Tín',
				'Huyện Ứng Hòa',
				'Quận Ba Đình',
				'Quận Bắc Từ Liêm',
				'Quận Cầu Giấy',
				'Quận Đống Đa',
				'Quận Hà Đông',
				'Quận Hai Bà Trưng',
				'Quận Hoàn Kiếm',
				'Quận Hoàng Mai',
				'Quận Long Biên',
				'Quận Nam Từ Liêm',
				'Quận Tây Hồ',
				'Quận Thanh Xuân',
				'Thị xã Sơn Tây'
			),
			'HA-TINH'         => array(
				'Huyện Cẩm Xuyên',
				'Huyện Can Lộc',
				'Huyện Đức Thọ',
				'Huyện Hương Khê',
				'Huyện Hương Sơn',
				'Huyện Kỳ Anh',
				'Huyện Lộc Hà',
				'Huyện Nghi Xuân',
				'Huyện Thạch Hà',
				'Huyện Vũ Quang',
				'Thành phố Hà Tĩnh',
				'Thị xã Hồng Lĩnh',
				'Thị xã Kỳ Anh'
			),
			'HAI-DUONG'       => array(
				'Huyện Bình Giang',
				'Huyện Cẩm Giàng',
				'Huyện Gia Lộc',
				'Huyện Kim Thành',
				'Huyện Kinh Môn',
				'Huyện Nam Sách',
				'Huyện Ninh Giang',
				'Huyện Thanh Hà',
				'Huyện Thanh Miện',
				'Huyện Tứ Kỳ',
				'Thành phố Hải Dương',
				'Thị xã Chí Linh'
			),
			'HAI-PHONG'       => array(
				'Huyện An Dương',
				'Huyện An Lão',
				'Huyện Bạch Long Vĩ',
				'Huyện Cát Hải',
				'Huyện Kiến Thuỵ',
				'Huyện Thuỷ Nguyên',
				'Huyện Tiên Lãng',
				'Huyện Vĩnh Bảo',
				'Quận Đồ Sơn',
				'Quận Dương Kinh',
				'Quận Hải An',
				'Quận Hồng Bàng',
				'Quận Kiến An',
				'Quận Lê Chân',
				'Quận Ngô Quyền'
			),
			'HOA-BINH'        => array(
				'Huyện Cao Phong',
				'Huyện Đà Bắc',
				'Huyện Kim Bôi',
				'Huyện Kỳ Sơn',
				'Huyện Lạc Sơn',
				'Huyện Lạc Thủy',
				'Huyện Lương Sơn',
				'Huyện Mai Châu',
				'Huyện Tân Lạc',
				'Huyện Yên Thủy',
				'Thành phố Hòa Bình'
			),
			'HAU-GIANG'       => array(
				'Huyện Châu Thành',
				'Huyện Châu Thành A',
				'Huyện Long Mỹ',
				'Huyện Phụng Hiệp',
				'Huyện Vị Thuỷ',
				'Thành phố Vị Thanh',
				'Thị xã Long Mỹ',
				'Thị xã Ngã Bảy'
			),
			'HUNG-YEN'        => array(
				'Huyện Ân Thi',
				'Huyện Khoái Châu',
				'Huyện Kim Động',
				'Huyện Mỹ Hào',
				'Huyện Phù Cừ',
				'Huyện Tiên Lữ',
				'Huyện Văn Giang',
				'Huyện Văn Lâm',
				'Huyện Yên Mỹ',
				'Thành phố Hưng Yên'
			),
			'HO-CHI-MINH'     => array(
				'Huyện Bình Chánh',
				'Huyện Cần Giờ',
				'Huyện Củ Chi',
				'Huyện Hóc Môn',
				'Huyện Nhà Bè',
				'Quận 1',
				'Quận 10',
				'Quận 11',
				'Quận 12',
				'Quận 2',
				'Quận 3',
				'Quận 4',
				'Quận 5',
				'Quận 6',
				'Quận 7',
				'Quận 8',
				'Quận 9',
				'Quận Bình Tân',
				'Quận Bình Thạnh',
				'Quận Gò Vấp',
				'Quận Phú Nhuận',
				'Quận Tân Bình',
				'Quận Tân Phú',
				'Quận Thủ Đức'
			),
			'KHANH-HOA'       => array(
				'Huyện Cam Lâm',
				'Huyện Diên Khánh',
				'Huyện Khánh Sơn',
				'Huyện Khánh Vĩnh',
				'Huyện Trường Sa',
				'Huyện Vạn Ninh',
				'Thành phố Cam Ranh',
				'Thành phố Nha Trang',
				'Thị xã Ninh Hòa'
			),
			'KIEN-GIANG'      => array(
				'Huyện An Biên',
				'Huyện An Minh',
				'Huyện Châu Thành',
				'Huyện Giang Thành',
				'Huyện Giồng Riềng',
				'Huyện Gò Quao',
				'Huyện Hòn Đất',
				'Huyện Kiên Hải',
				'Huyện Kiên Lương',
				'Huyện Phú Quốc',
				'Huyện Tân Hiệp',
				'Huyện U Minh Thượng',
				'Huyện Vĩnh Thuận',
				'Thành phố Rạch Giá',
				'Thị xã Hà Tiên'
			),
			'KON-TUM'         => array(
				'Huyện Đắk Glei',
				'Huyện Đắk Hà',
				'Huyện Đắk Tô',
				'Huyện Ia H Drai',
				'Huyện Kon Plông',
				'Huyện Kon Rẫy',
				'Huyện Ngọc Hồi',
				'Huyện Sa Thầy',
				'Huyện Tu Mơ Rông',
				'Thành phố Kon Tum'
			),
			'LAI-CHAU'        => array(
				'Huyện Mường Tè',
				'Huyện Nậm Nhùn',
				'Huyện Phong Thổ',
				'Huyện Sìn Hồ',
				'Huyện Tam Đường',
				'Huyện Tân Uyên',
				'Huyện Than Uyên',
				'Thành phố Lai Châu'
			),
			'LAO-CAI'         => array(
				'Huyện Bắc Hà',
				'Huyện Bảo Thắng',
				'Huyện Bảo Yên',
				'Huyện Bát Xát',
				'Huyện Mường Khương',
				'Huyện Sa Pa',
				'Huyện Si Ma Cai',
				'Huyện Văn Bàn',
				'Thành phố Lào Cai'
			),
			'LANG-SON'        => array(
				'Huyện Bắc Sơn',
				'Huyện Bình Gia',
				'Huyện Cao Lộc',
				'Huyện Chi Lăng',
				'Huyện Đình Lập',
				'Huyện Hữu Lũng',
				'Huyện Lộc Bình',
				'Huyện Tràng Định',
				'Huyện Văn Lãng',
				'Huyện Văn Quan',
				'Thành phố Lạng Sơn'
			),
			'LAM-DONG'        => array(
				'Huyện Bảo Lâm',
				'Huyện Cát Tiên',
				'Huyện Đạ Huoai',
				'Huyện Đạ Tẻh',
				'Huyện Đam Rông',
				'Huyện Di Linh',
				'Huyện Đơn Dương',
				'Huyện Đức Trọng',
				'Huyện Lạc Dương',
				'Huyện Lâm Hà',
				'Thành phố Bảo Lộc',
				'Thành phố Đà Lạt'
			),
			'LONG-AN'         => array(
				'Huyện Bến Lức',
				'Huyện Cần Đước',
				'Huyện Cần Giuộc',
				'Huyện Châu Thành',
				'Huyện Đức Hòa',
				'Huyện Đức Huệ',
				'Huyện Mộc Hóa',
				'Huyện Tân Hưng',
				'Huyện Tân Thạnh',
				'Huyện Tân Trụ',
				'Huyện Thạnh Hóa',
				'Huyện Thủ Thừa',
				'Huyện Vĩnh Hưng',
				'Thành phố Tân An',
				'Thị xã Kiến Tường'
			),
			'NAM-DINH'        => array(
				'Huyện Giao Thủy',
				'Huyện Hải Hậu',
				'Huyện Mỹ Lộc',
				'Huyện Nam Trực',
				'Huyện Nghĩa Hưng',
				'Huyện Trực Ninh',
				'Huyện Vụ Bản',
				'Huyện Xuân Trường',
				'Huyện Ý Yên',
				'Thành phố Nam Định'
			),
			'NGHE-AN'         => array(
				'Huyện Anh Sơn',
				'Huyện Con Cuông',
				'Huyện Diễn Châu',
				'Huyện Đô Lương',
				'Huyện Hưng Nguyên',
				'Huyện Kỳ Sơn',
				'Huyện Nam Đàn',
				'Huyện Nghi Lộc',
				'Huyện Nghĩa Đàn',
				'Huyện Quế Phong',
				'Huyện Quỳ Châu',
				'Huyện Quỳ Hợp',
				'Huyện Quỳnh Lưu',
				'Huyện Tân Kỳ',
				'Huyện Thanh Chương',
				'Huyện Tương Dương',
				'Huyện Yên Thành',
				'Thành phố Vinh',
				'Thị xã Cửa Lò',
				'Thị xã Hoàng Mai',
				'Thị xã Thái Hoà'
			),
			'NINH-BINH'       => array(
				'Huyện Gia Viễn',
				'Huyện Hoa Lư',
				'Huyện Kim Sơn',
				'Huyện Nho Quan',
				'Huyện Yên Khánh',
				'Huyện Yên Mô',
				'Thành phố Ninh Bình',
				'Thành phố Tam Điệp'
			),
			'NINH-THUAN'      => array(
				'Huyện Bác Ái',
				'Huyện Ninh Hải',
				'Huyện Ninh Phước',
				'Huyện Ninh Sơn',
				'Huyện Thuận Bắc',
				'Huyện Thuận Nam',
				'Thành phố Phan Rang-Tháp Chàm'
			),
			'PHU-THO'         => array(
				'Huyện Cẩm Khê',
				'Huyện Đoan Hùng',
				'Huyện Hạ Hoà',
				'Huyện Lâm Thao',
				'Huyện Phù Ninh',
				'Huyện Tam Nông',
				'Huyện Tân Sơn',
				'Huyện Thanh Ba',
				'Huyện Thanh Sơn',
				'Huyện Thanh Thuỷ',
				'Huyện Yên Lập',
				'Thành phố Việt Trì',
				'Thị xã Phú Thọ'
			),
			'PHU-YEN'         => array(
				'Huyện Đông Hòa',
				'Huyện Đồng Xuân',
				'Huyện Phú Hoà',
				'Huyện Sơn Hòa',
				'Huyện Sông Hinh',
				'Huyện Tây Hoà',
				'Huyện Tuy An',
				'Thành phố Tuy Hoà',
				'Thị xã Sông Cầu'
			),
			'QUANG-BINH'      => array(
				'Huyện Bố Trạch',
				'Huyện Lệ Thủy',
				'Huyện Minh Hóa',
				'Huyện Quảng Ninh',
				'Huyện Quảng Trạch',
				'Huyện Tuyên Hóa',
				'Thành Phố Đồng Hới',
				'Thị xã Ba Đồn'
			),
			'QUANG-NAM'       => array(
				'Huyện Bắc Trà My',
				'Huyện Đại Lộc',
				'Huyện Đông Giang',
				'Huyện Duy Xuyên',
				'Huyện Hiệp Đức',
				'Huyện Nam Giang',
				'Huyện Nam Trà My',
				'Huyện Nông Sơn',
				'Huyện Núi Thành',
				'Huyện Phú Ninh',
				'Huyện Phước Sơn',
				'Huyện Quế Sơn',
				'Huyện Tây Giang',
				'Huyện Thăng Bình',
				'Huyện Tiên Phước',
				'Thành phố Hội An',
				'Thành phố Tam Kỳ',
				'Thị xã Điện Bàn'
			),
			'QUANG-NGAI'      => array(
				'Huyện Ba Tơ',
				'Huyện Bình Sơn',
				'Huyện Đức Phổ',
				'Huyện Lý Sơn',
				'Huyện Minh Long',
				'Huyện Mộ Đức',
				'Huyện Nghĩa Hành',
				'Huyện Sơn Hà',
				'Huyện Sơn Tây',
				'Huyện Sơn Tịnh',
				'Huyện Tây Trà',
				'Huyện Trà Bồng',
				'Huyện Tư Nghĩa',
				'Thành phố Quảng Ngãi'
			),
			'QUANG-NINH'      => array(
				'Huyện Ba Chẽ',
				'Huyện Bình Liêu',
				'Huyện Cô Tô',
				'Huyện Đầm Hà',
				'Huyện Hải Hà',
				'Huyện Hoành Bồ',
				'Huyện Tiên Yên',
				'Huyện Vân Đồn',
				'Thành phố Cẩm Phả',
				'Thành phố Hạ Long',
				'Thành phố Móng Cái',
				'Thành phố Uông Bí',
				'Thị xã Đông Triều',
				'Thị xã Quảng Yên'
			),
			'QUANG-TRI'       => array(
				'Huyện Cam Lộ',
				'Huyện Cồn Cỏ',
				'Huyện Đa Krông',
				'Huyện Gio Linh',
				'Huyện Hải Lăng',
				'Huyện Hướng Hóa',
				'Huyện Triệu Phong',
				'Huyện Vĩnh Linh',
				'Thành phố Đông Hà',
				'Thị xã Quảng Trị'
			),
			'SOC-TRANG'       => array(
				'Huyện Châu Thành',
				'Huyện Cù Lao Dung',
				'Huyện Kế Sách',
				'Huyện Long Phú',
				'Huyện Mỹ Tú',
				'Huyện Mỹ Xuyên',
				'Huyện Thạnh Trị',
				'Huyện Trần Đề',
				'Thành phố Sóc Trăng',
				'Thị xã Ngã Năm',
				'Thị xã Vĩnh Châu'
			),
			'SON-LA'          => array(
				'Huyện Bắc Yên',
				'Huyện Mai Sơn',
				'Huyện Mộc Châu',
				'Huyện Mường La',
				'Huyện Phù Yên',
				'Huyện Quỳnh Nhai',
				'Huyện Sông Mã',
				'Huyện Sốp Cộp',
				'Huyện Thuận Châu',
				'Huyện Vân Hồ',
				'Huyện Yên Châu',
				'Thành phố Sơn La'
			),
			'TAY-NINH'        => array(
				'Huyện Bến Cầu',
				'Huyện Châu Thành',
				'Huyện Dương Minh Châu',
				'Huyện Gò Dầu',
				'Huyện Hòa Thành',
				'Huyện Tân Biên',
				'Huyện Tân Châu',
				'Huyện Trảng Bàng',
				'Thành phố Tây Ninh'
			),
			'THAI-BINH'       => array(
				'Huyện Đông Hưng',
				'Huyện Hưng Hà',
				'Huyện Kiến Xương',
				'Huyện Quỳnh Phụ',
				'Huyện Thái Thụy',
				'Huyện Tiền Hải',
				'Huyện Vũ Thư',
				'Thành phố Thái Bình'
			),
			'THAI-NGUYEN'     => array(
				'Huyện Đại Từ',
				'Huyện Định Hóa',
				'Huyện Đồng Hỷ',
				'Huyện Phú Bình',
				'Huyện Phú Lương',
				'Huyện Võ Nhai',
				'Thành phố Sông Công',
				'Thành phố Thái Nguyên',
				'Thị xã Phổ Yên'
			),
			'THANH-HOA'       => array(
				'Huyện Bá Thước',
				'Huyện Cẩm Thủy',
				'Huyện Đông Sơn',
				'Huyện Hà Trung',
				'Huyện Hậu Lộc',
				'Huyện Hoằng Hóa',
				'Huyện Lang Chánh',
				'Huyện Mường Lát',
				'Huyện Nga Sơn',
				'Huyện Ngọc Lặc',
				'Huyện Như Thanh',
				'Huyện Như Xuân',
				'Huyện Nông Cống',
				'Huyện Quan Hóa',
				'Huyện Quan Sơn',
				'Huyện Quảng Xương',
				'Huyện Thạch Thành',
				'Huyện Thiệu Hóa',
				'Huyện Thọ Xuân',
				'Huyện Thường Xuân',
				'Huyện Tĩnh Gia',
				'Huyện Triệu Sơn',
				'Huyện Vĩnh Lộc',
				'Huyện Yên Định',
				'Thành phố Thanh Hóa',
				'Thị xã Bỉm Sơn',
				'Thị xã Sầm Sơn'
			),
			'THUA-THIEN-HUE'  => array(
				'Huyện A Lưới',
				'Huyện Nam Đông',
				'Huyện Phong Điền',
				'Huyện Phú Lộc',
				'Huyện Phú Vang',
				'Huyện Quảng Điền',
				'Thành phố Huế',
				'Thị xã Hương Thủy',
				'Thị xã Hương Trà'
			),
			'TIEN-GIANG'      => array(
				'Huyện Cái Bè',
				'Huyện Cai Lậy',
				'Huyện Châu Thành',
				'Huyện Chợ Gạo',
				'Huyện Gò Công Đông',
				'Huyện Gò Công Tây',
				'Huyện Tân Phú Đông',
				'Huyện Tân Phước',
				'Thành phố Mỹ Tho',
				'Thị xã Cai Lậy',
				'Thị xã Gò Công'
			),
			'TRA-VINH'        => array(
				'Huyện Càng Long',
				'Huyện Cầu Kè',
				'Huyện Cầu Ngang',
				'Huyện Châu Thành',
				'Huyện Duyên Hải',
				'Huyện Tiểu Cần',
				'Huyện Trà Cú',
				'Thành phố Trà Vinh',
				'Thị xã Duyên Hải'
			),
			'TUYEN-QUANG'     => array(
				'Huyện Chiêm Hóa',
				'Huyện Hàm Yên',
				'Huyện Lâm Bình',
				'Huyện Nà Hang',
				'Huyện Sơn Dương',
				'Huyện Yên Sơn',
				'Thành phố Tuyên Quang'
			),
			'VINH-LONG'       => array(
				'Huyện  Vũng Liêm',
				'Huyện Bình Tân',
				'Huyện Long Hồ',
				'Huyện Mang Thít',
				'Huyện Tam Bình',
				'Huyện Trà Ôn',
				'Thành phố Vĩnh Long',
				'Thị xã Bình Minh'
			),
			'VINH-PHUC'       => array(
				'Huyện Bình Xuyên',
				'Huyện Lập Thạch',
				'Huyện Sông Lô',
				'Huyện Tam Đảo',
				'Huyện Tam Dương',
				'Huyện Vĩnh Tường',
				'Huyện Yên Lạc',
				'Thành phố Vĩnh Yên',
				'Thị xã Phúc Yên'
			),
			'YEN-BAI'         => array(
				'Huyện Lục Yên',
				'Huyện Mù Căng Chải',
				'Huyện Trạm Tấu',
				'Huyện Trấn Yên',
				'Huyện Văn Chấn',
				'Huyện Văn Yên',
				'Huyện Yên Bình',
				'Thành phố Yên Bái',
				'Thị xã Nghĩa Lộ'
			),
		);

		return $cities;

	}

	/**
	* Set priority for state and city
	*
	* Topic https://wordpress.org/support/topic/thay-doi-thu-tu-field-tinh-huyen/
	* 
	* @author 	Longkt
	* @since 	1.4
	*
	*/
	function custom_fields_priority( $fields ) {
		$fields['state']['priority'] = 50;
		$fields['city']['priority'] = 60;
		$fields['address_1']['priority'] = 70;
		$fields['address_2']['priority'] = 80;
		return $fields;
	}
}