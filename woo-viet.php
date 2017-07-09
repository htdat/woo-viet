<?php
/**
 * Plugin Name: Woo Viet - WooCommerce for Vietnam
 * Plugin URI: https://github.com/htdat/woo-viet
 * Description: This plugin provides features and integrations specifically for Vietnam.
 * Author: htdat
 * Author URI: https://profiles.wordpress.org/htdat
 * Text Domain: woo-viet
 * Domain Path: /languages
 * Version: 1.3.1
 * License:     GPLv2+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WOO_VIET_DIR', plugin_dir_path( __FILE__ ) );
define( 'WOO_VIET_URL', plugins_url( '/', __FILE__ ) );

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

	/**
	 * @var array The default settings for the whole plugin
	 */
	static $default_settings = array(
		'add_province'           =>
			array(
				'enabled' => 'yes',
			),
		'add_city'               =>
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
		'add_onepay_domestic'    =>
			array(
				'enabled' => 'yes',
			),
	);
	/**
	 * The properties to manage all classes under the "inc/" folder
	 * Example:
	 * - File name: class-wooviet-provinces.php
	 * - Class Name: \WooViet_Provinces
	 * - Method Name: WooViet->Provinces
	 */
	protected $Provinces;
	protected $Currency;
	protected $VND_PayPal_Standard;
	protected $Admin_Page;

	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Throw a notice if WooCommerce is NOT active
	 */
	public function notice_if_not_woocommerce() {
		$class = 'notice notice-error';

		$message = __( 'Woo Viet is not running because WooCommerce is not active. Please activate both plugins.',
			'woo-viet' );

		printf( '<div class="%1$s"><p><strong>%2$s</strong></p></div>', $class, $message );
	}

	/**
	 * Run this method under the "init" action
	 */
	public function init() {

		// Load the localization feature
		$this->i18n();

		if ( class_exists( 'WooCommerce' ) ) {
			// Run this plugin normally if WooCommerce is active
			$this->main();
		} else {
			// Throw a notice if WooCommerce is NOT active
			add_action( 'admin_notices', array( $this, 'notice_if_not_woocommerce' ) );
		}
	}

	/**
	 * Localize the plugin
	 * @since 1.0
	 */
	public function i18n() {
		load_plugin_textdomain( 'woo-viet', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * The main method to load the components
	 */
	public function main() {

		if ( is_admin() ) {
			// Add the admin setting page
			include( WOO_VIET_DIR . 'inc/class-wooviet-admin-page.php' );
			$this->Admin_Page = new WooViet_Admin_Page();

			// Add the notices class
			include( WOO_VIET_DIR . 'inc/class-wooviet-notices.php' );
			new WooViet_Notices();

		}

		$settings = self::get_settings();

		// Check if "Add the OnePay Domestic Gateway" is enabled
		if ( 'yes' == $settings['add_onepay_domestic']['enabled']
		     AND 'VND' == get_woocommerce_currency()
		) {
			include( 'inc/class-wooviet-onepay-domestic.php' );

			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway_class' ) );

			// Add the action to check the cron job for handling queryDR
			// It's not possible to add in the class "WooViet_OnePay_Domestic_Hook" because it's NOT always loadded
			if ( defined( 'DOING_CRON' ) and DOING_CRON ) {
				$this->WooViet_OnePay_Domestic_Hook = new WooViet_OnePay_Domestic();
				add_action( 'wooviet_handle_onepay_querydr', array(
					$this->WooViet_OnePay_Domestic_Hook,
					'handle_onepay_querydr'
				), 10, 1 );
			}

		}

		// Check if "Add provinces for Vietnam	" is enabled.
		if ( 'yes' == $settings['add_province']['enabled'] ) {
			include( WOO_VIET_DIR . 'inc/class-wooviet-provinces.php' );
			$this->Provinces = new WooViet_Provinces();

			// Enable "Add cities for Vietnam" if the province option is selected.
			if ( 'yes' == $settings['add_city']['enabled'] ) {
				include( WOO_VIET_DIR . 'inc/class-wooviet-cities.php' );
				new WooViet_Cities();
			}
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


		// Check if "Support VND for the PayPal Standard gateway" is enabled
		if ( 'yes' == $settings['vnd_paypal_standard']['enabled'] ) {
			include( WOO_VIET_DIR . 'inc/class-wooviet-vnd-paypal-standard.php' );
			$this->VND_PayPal_Standard = new WooViet_VND_PayPal_Standard(
				$settings['vnd_paypal_standard']['rate'],
				$settings['vnd_paypal_standard']['currency']
			);
		}

	}

	/**
	 * The wrapper method to get the settings of the plugin
	 * @return array
	 */
	static function get_settings() {
		$settings = get_option( 'woo-viet', self::$default_settings );
		$settings = wp_parse_args( $settings, self::$default_settings );

		return $settings;
	}

	/**
	 * Add the gateways to WooCommerce
	 *
	 * @param array $methods
	 *
	 * @since 1.3
	 * @return array
	 */
	public function add_gateway_class( $methods ) {
		$methods[] = 'WooViet_OnePay_Domestic';

		return $methods;

	}

}