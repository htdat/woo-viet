<?php
/**
 * Plugin Name: Woo Viet - WooCommerce for Vietnam
 * Plugin URI: https://github.com/htdat/woo-viet
 * Description: This plugin provides features and integrations specifically for Vietnam.
 * Author: htdat
 * Author URI: https://profiles.wordpress.org/htdat
 * Text Domain: woo-viet
 * Domain Path: /languages
 * Version: 1.5.2
 *
 * WC requires at least: 3.0
 * WC tested up to: 5.7.1
 *
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
		'add_province'                =>
			array(
				'enabled' => 'yes',
			),
		'add_city'                    =>
			array(
				'enabled' => 'yes',
			),
		'change_currency_symbol'      =>
			array(
				'enabled' => 'yes',
				'text'    => 'VND',
			),
		'convert_price'               =>
			array(
				'enabled' => 'yes',
				'text'    => 'K',
			),
		'vnd_paypal_standard'         =>
			array(
				'enabled'  => 'yes',
				'currency' => 'USD',
				'rate'     => '22770',
			),
		'add_onepay_domestic'         =>
			array(
				'enabled' => 'yes',
			),
		'add_onepay_international'    =>
			array(
				'enabled' => 'yes',
			),
		'vnd_paypal_express_checkout' =>
			array(
				'enabled'  => 'yes',
				'currency' => 'USD',
				'rate'     => '22770',
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
	protected $VND_PayPal_Express_Checkout;

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
		$class = 'notice notice-warning';

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

			// Add "Settings" link when the plugin is active
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_settings_link' ) );
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

			add_filter( 'woocommerce_payment_gateways', function ( $methods ) {
				$methods[] = 'WooViet_OnePay_Domestic';
				return $methods;
			} );

			/**
			 * Add the action to check the cron job for handling queryDR
			 * It's not possible to add in the "WooViet_OnePay_Domestic" class
			 * because it's NOT always loaded in the 'woocommerce_payment_gateways' hook above
			 */
			if ( defined( 'DOING_CRON' ) and DOING_CRON ) {
				$wooviet_onepay_domestic = new WooViet_OnePay_Domestic();
				/**
				 * @since 1.5.0
				 */
				add_action( 'wooviet_onepay_domestic_handle_querydr', array(
					$wooviet_onepay_domestic,
					'handle_onepay_querydr'
				), 10, 1 );
				/**
				 * Backward compatibility
				 * Actually, this does help for the short time (exactly 20 minutes) @see WooViet_OnePay_Abstract::set_onepay_querydr_cron()
				 * when upgrading to version 1.5.0
				 * as the previous cron jobs set by the previous versions has not run yet.
				 *
				 * TODO: May consider removing this completely after 2 major versions. Target: 1.7
				 */
				add_action( 'wooviet_handle_onepay_querydr', array(
					$wooviet_onepay_domestic,
					'handle_onepay_querydr'
				), 10, 1 );
			}

		}

		/**
		 * Check if "Add the OnePay International Gateway" is enabled
		 * @since 1.5.0
		 */
		if ( 'yes' == $settings['add_onepay_international']['enabled']
		     AND 'VND' == get_woocommerce_currency()
		) {
			include( 'inc/class-wooviet-onepay-international.php' );

			add_filter( 'woocommerce_payment_gateways', function ( $methods ) {
				$methods[] = 'WooViet_OnePay_International';
				return $methods;
			} );

			/**
			 * Add the action to check the cron job for handling queryDR
			 * It's not possible to add in the "WooViet_OnePay_International" class
			 * because it's NOT always loaded in the 'woocommerce_payment_gateways' hook above
			 */
			if ( defined( 'DOING_CRON' ) and DOING_CRON ) {

				add_action( 'wooviet_onepay_international_handle_querydr', array(
					new WooViet_OnePay_International(),
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
		if ( 'yes' == $settings['vnd_paypal_standard']['enabled']
		     AND class_exists( 'WC_Gateway_Paypal' )
		     AND 'VND' == get_woocommerce_currency()
		) {
			include( WOO_VIET_DIR . 'inc/class-wooviet-vnd-paypal-standard.php' );
			$this->VND_PayPal_Standard = new WooViet_VND_PayPal_Standard(
				$settings['vnd_paypal_standard']['rate'],
				$settings['vnd_paypal_standard']['currency']
			);
		}

		// Check if "Support VND for the PayPal Express Checkout gateway" is enabled
		if ( 'yes' == $settings['vnd_paypal_express_checkout']['enabled']
		     AND class_exists( 'WC_Gateway_PPEC_Plugin' )
		     AND 'VND' == get_woocommerce_currency()
		) {
			include( WOO_VIET_DIR . 'inc/class-wooviet-vnd-paypal-express-checkout.php' );
			$this->VND_PayPal_Express_Checkout = new WooViet_VND_PayPal_Express_Checkout(
				$settings['vnd_paypal_express_checkout']['rate'],
				$settings['vnd_paypal_express_checkout']['currency']
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
	 * Add "Settings" link in the Plugins list page when the plugin is active
	 *
	 * @since 1.4
	 * @author Longkt
	 */
	public function add_settings_link( $links ) {
		$settings = array( '<a href="' . admin_url( 'admin.php?page=woo-viet' ) . '">' . __( 'Settings', 'woo-viet' ) . '</a>' );
		$links    = array_reverse( array_merge( $links, $settings ) );

		return $links;
	}

}
