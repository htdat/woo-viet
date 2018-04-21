<?php 

if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display only one country in the Checkout page
 *
 * @author Longkt
 * @since  1.5
 */
class WooViet_Countries {

	public function __construct() {

		// Enqueue scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_countries_scripts' ) );

	}

	/**
	 * Load countries scripts
	 */
	public function load_countries_scripts() {

		wp_enqueue_script( 'woo-viet-countries', WOO_VIET_URL . 'assets/countries.js', array( 'jquery' ), '1.0', true );

		$settings = get_option( 'woo-viet' );
		$country_code = $settings['display-only-one-country']['country'];

		wp_localize_script( 'woo-viet-countries', 'woo_viet_countries', array(
			'country_code' => $country_code,
		) );
		
	}
}