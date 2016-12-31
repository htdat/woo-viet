<?php
/**
 * Plugin Name: WooCommerce for Vietnam by htdat
 * Plugin URI: https://github.com/htdat
 * Description: This plugin provides features and integrations specifically for Vietnam.
 * Author: htdat
 * Author URI: https://profiles.wordpress.org/htdat
 * Text Domain: woocommerce-for-vietnam
 * Domain Path: /languages
 * Version: 1.0-dev
 * License:     GPLv2+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WOO_VIET_DIR', plugin_dir_path( __FILE__ ) );
// define( 'WOO_VIET_URL', plugins_url( '/', __FILE__ ) );

/**
 * Start the instance
 */

new WooViet();

/**
 * The main class of the plugin
 *
 * @author   htdat
 * @since    1.0
 */
class WooViet {

	protected $Provinces;
	protected $Currency;
	protected $VND_Paypal_Standard;

	// protected $settings;

	static $default_settings = array(
		'add_province'           =>
			array(
				'enabled' => 'yes',
			),
		'change_currency_symbol' =>
			array(
				'enabled' => 'yes',
				'text'    => 'VND',
			),
		'convert_price'          =>
			array(
				'enabled' => 'yes',
				'text'    => 'K',
			),
		'vnd_paypal_standard'    =>
			array(
				'enabled'  => 'yes',
				'currency' => 'USD',
				'rate'     => '22770',
			),
	);

	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'i18n' ) );
		add_action( 'init', array( $this, 'init' ) );

	}

	public function init() {

		if ( is_admin() ) {
			// @todo: Add the admin setting page
			include( WOO_VIET_DIR . 'inc/class-wooviet-admin-page.php' );
			new WooViet_Admin_Page();

		}

		$settings = self::get_settings();

		// Check if "Add provinces for Vietnam	" is enabled.
		if ( 'yes' == $settings['add_province']['enabled'] ) {
			include( WOO_VIET_DIR . 'inc/class-wooviet-provinces.php' );
			$this->Provinces = new WooViet_Provinces();
		}

		include( WOO_VIET_DIR . 'inc/class-wooviet-currency.php' );
		$this->Currency = new WooViet_Currency();

		// Check if "Change VND currency symbol" is enabled
		if ( 'yes' == $settings['change_currency_symbol']['enabled'] ) {
			$this->Currency->change_currency_symbol( $settings['change_currency_symbol']['text'] );
		}

		// Check if "Convert 000 of prices to K (or anything)" is enabled
		if ( 'yes' == $settings['convert_price']['enabled'] ) {
			$this->Currency->convert_price_thousand_to_k( $settings['convert_price']['text'] );
		}


		// Check if "Support VND for the Paypal Standard gateway" is enabled
		if ( 'yes' == $settings['vnd_paypal_standard']['enabled'] ) {
			include( WOO_VIET_DIR . 'inc/class-wooviet-vnd-paypal-standard.php' );
			$this->VND_Paypal_Standard = new WooViet_VND_Paypal_Standard(
				$settings['vnd_paypal_standard']['rate'],
				$settings['vnd_paypal_standard']['currency']
			);
		}

	}

	/**
	 * Localize the plugin
	 * @since 1.0
	 */
	public function i18n() {
		load_plugin_textdomain( 'freshfunbits', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	static function get_settings() {

		return get_option( 'woocommerce-for-vietnam', self::$default_settings );
	}

}