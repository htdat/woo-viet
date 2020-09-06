<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create the admin page under wp-admin -> WooCommerce -> Woo Viet
 *
 * @author   htdat
 * @since    1.0
 *
 */
class WooViet_Admin_Page {

	/**
	 * @var string The message to display after saving settings
	 */
	var $message = '';

	/**
	 * WooViet_Admin_Page constructor.
	 */
	public function __construct() {
		// Catch and run the save_settings() action
		if ( isset( $_REQUEST['wooviet_nonce'] ) && isset ( $_REQUEST['action'] ) && 'wooviet_save_settings' == $_REQUEST['action'] ) {
			$this->save_settings();
		}

		add_action( 'admin_menu', array( $this, 'register_submenu_page' ) );

	}

	/**
	 * Save settings for the plugin
	 */
	public function save_settings() {
		if ( wp_verify_nonce( $_REQUEST['wooviet_nonce'], 'wooviet_save_settings' ) ) {
			update_option( 'woo-viet', $_REQUEST['settings'] );

			$this->message =
				'<div class="updated notice"><p><strong>' .
				__( 'Settings saved', 'woo-viet' ) .
				'</p></strong></div>';

		} else {

			$this->message =
				'<div class="error notice"><p><strong>' .
				__( 'Can not save settings! Please refresh this page.', 'woo-viet' ) .
				'</p></strong></div>';
		}
	}

	/**
	 * Register the sub-menu under "WooCommerce"
	 * Link: http://my-site.com/wp-admin/admin.php?page=woo-viet
	 */
	public function register_submenu_page() {
		add_submenu_page(
			'woocommerce',
			__( 'Woo Viet Settings', 'woo-viet' ),
			'Woo Viet',
			'manage_options',
			'woo-viet',
			array( $this, 'admin_page_html' )
		);
	}

	/**
	 * Generate the HTML code of the settings page
	 */
	public function admin_page_html() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = WooViet::get_settings();

		?>
        <div class="wrap">
            <h1><?= esc_html( get_admin_page_title() ); ?></h1>
            <form name="woocommerce_for_vietnam" method="post">
				<?php echo $this->message ?>
                <input type="hidden" id="action" name="action" value="wooviet_save_settings">
                <input type="hidden" id="wooviet_nonce" name="wooviet_nonce"
                       value="<?php echo wp_create_nonce( 'wooviet_save_settings' ) ?>">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><?php _e( 'Add the OnePay Domestic Gateway', 'woo-viet' ) ?></th>
                        <td>
                            <input name="settings[add_onepay_domestic][enabled]" type="hidden" value="no">
                            <input name="settings[add_onepay_domestic][enabled]" type="checkbox"
                                   id="add_onepay_domestic" value="yes"
								<?php if ( 'yes' == $settings['add_onepay_domestic']['enabled'] )
									echo 'checked="checked"' ?>>
                            <label for="add_onepay_domestic"><?php _e( 'Enabled', 'woo-viet' ) ?></label>
                            <br/>
                            <br/>
                            <label for="">
								<?php
								echo sprintf( __( 'Your store currency is <code>%s</code>. ', 'woo-viet' ), get_woocommerce_currency() );
								// Handle whether or not the store current is VND
								if ( 'VND' == get_woocommerce_currency() ) {
									_e( 'OnePay can work on your site.', 'woo-viet' );
									echo '<br/>';
									if ( 'yes' == $settings['add_onepay_domestic']['enabled'] ) {
										echo sprintf( __( 'Please configure this gateway under <a href="%s">WooCommerce -> Settings -> Checkout</a>.', 'woo-viet' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wooviet_onepay_domestic' ) );
									}
								} else {
									_e( '<span style="color: red" ">This gateway is not active on your site. Because OnePay supports VND only.</span>', 'woo-viet' );
								}

								?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Add the OnePay International Gateway', 'woo-viet' ) ?></th>
                        <td>
                            <input name="settings[add_onepay_international][enabled]" type="hidden" value="no">
                            <input name="settings[add_onepay_international][enabled]" type="checkbox"
                                   id="add_onepay_international" value="yes"
			                    <?php if ( 'yes' == $settings['add_onepay_international']['enabled'] )
				                    echo 'checked="checked"' ?>>
                            <label for="add_onepay_international"><?php _e( 'Enabled', 'woo-viet' ) ?></label>
                            <br/>
                            <br/>
                            <label for="">
			                    <?php
			                    echo sprintf( __( 'Your store currency is <code>%s</code>. ', 'woo-viet' ), get_woocommerce_currency() );
			                    // Handle whether or not the store current is VND
			                    if ( 'VND' == get_woocommerce_currency() ) {
				                    _e( 'OnePay can work on your site.', 'woo-viet' );
				                    echo '<br/>';
				                    if ( 'yes' == $settings['add_onepay_international']['enabled'] ) {
					                    echo sprintf( __( 'Please configure this gateway under <a href="%s">WooCommerce -> Settings -> Checkout</a>.', 'woo-viet' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wooviet_onepay_international' ) );
				                    }
			                    } else {
				                    _e( '<span style="color: red" ">This gateway is not active on your site. Because OnePay supports VND only.</span>', 'woo-viet' );
			                    }

			                    ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Add provinces for Vietnam', 'woo-viet' ) ?></th>
                        <td>
                            <input name="settings[add_province][enabled]" type="hidden" value="no">
                            <input name="settings[add_province][enabled]" type="checkbox" id="add_province" value="yes"
								<?php if ( 'yes' == $settings['add_province']['enabled'] )
									echo 'checked="checked"' ?>>
                            <label for="add_province"><?php _e( 'Enabled', 'woo-viet' ) ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Add districts for Vietnam', 'woo-viet' ) ?><br/>
                            <i><?php _e( 'Require "Add provinces for Vietnam" active', 'woo-viet' ) ?></i>
                        </th>
                        <td>
                            <input name="settings[add_city][enabled]" type="hidden" value="no">
                            <input name="settings[add_city][enabled]" type="checkbox" id="add_city" value="yes"
								<?php if ( 'yes' == $settings['add_city']['enabled'] )
									echo 'checked="checked"' ?>>
                            <label for="add_city"><?php _e( 'Enabled', 'woo-viet' ) ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Change VND currency symbol', 'woo-viet' ) ?></th>
                        <td>
                            <input name="settings[change_currency_symbol][enabled]" type="hidden" value="no">
                            <input name="settings[change_currency_symbol][enabled]" type="checkbox"
                                   id="change_currency_symbol" value="yes"
								<?php if ( 'yes' == $settings['change_currency_symbol']['enabled'] )
									echo 'checked="checked"' ?>>
                            <label for="change_currency_symbol"><?php _e( 'Enabled', 'woo-viet' ) ?></label>
                            <br/>
                            <br/>
                            <input type="text" name="settings[change_currency_symbol][text]"
                                   value="<?php echo $settings['change_currency_symbol']['text'] ?>"
                                   id="change_currency_symbol_text" class="small-text">
                            <label for="change_currency_symbol_text"><?php _e( 'Insert a text to change the default symbol <code>đ</code>', 'woo-viet' ) ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Convert <code>000</code> of prices to K (or anything)', 'woo-viet' ) ?></th>
                        <td>
                            <input name="settings[convert_price][enabled]" type="hidden" value="no">
                            <input name="settings[convert_price][enabled]" type="checkbox" id="convert_price"
                                   value="yes"
								<?php if ( 'yes' == $settings['convert_price']['enabled'] )
									echo 'checked="checked"' ?>>
                            <label for="convert_price"><?php _e( 'Enabled', 'woo-viet' ) ?></label>

                            <fieldset><br/>
                                <input type="text" name="settings[convert_price][text]"
                                       value="<?php echo $settings['convert_price']['text'] ?>"
                                       id="convert_price_text" class="small-text">
                                <label for="convert_price_text"><?php _e( 'Choose what you want to change. E.g:', 'woo-viet' ) ?>
                                    <code>K</code>, <code>nghìn</code>, <code>ngàn</code></label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php printf( __( 'Support VND for <a href="%s">the PayPal Standard gateway</a>', 'woo-viet' ), 'https://docs.woocommerce.com/document/paypal-standard/' ) ?></th>
                        <td>
                            <input name="settings[vnd_paypal_standard][enabled]" type="hidden" value="no">
                            <input name="settings[vnd_paypal_standard][enabled]" type="checkbox"
                                   id="vnd_paypal_standard" value="yes"
								<?php if ( 'yes' == $settings['vnd_paypal_standard']['enabled'] )
									echo 'checked="checked"' ?>>
                            <label for="vnd_paypal_standard"><?php _e( 'Enabled', 'woo-viet' ) ?></label>

                            <fieldset><br/>
                                <select name="settings[vnd_paypal_standard][currency]"
                                        id="vnd_paypal_standard_currency">
									<?php
									$paypal_supported_currencies = array(
										'AUD',
										'BRL',
										'CAD',
										'MXN',
										'NZD',
										'HKD',
										'SGD',
										'USD',
										'EUR',
										'JPY',
										'TRY',
										'NOK',
										'CZK',
										'DKK',
										'HUF',
										'ILS',
										'MYR',
										'PHP',
										'PLN',
										'SEK',
										'CHF',
										'TWD',
										'THB',
										'GBP',
										'RMB',
										'RUB'
									);
									foreach ( $paypal_supported_currencies as $currency ) {

										if ( strtoupper( $currency ) == $settings['vnd_paypal_standard']['currency'] ) {
											printf( '<option selected="selected" value="%1$s">%1$s</option>', $currency );
										} else {
											printf( '<option value="%1$s">%1$s</option>', $currency );
										}

									}
									?>
                                </select>
                                <label for="vnd_paypal_standard_currency"><?php _e( 'Select a PayPal supported currency (like USD, EUR, etc), which is used to convert VND prices', 'woo-viet' ) ?></label>
                                <br/>
                                <br/>

                                <input name="settings[vnd_paypal_standard][rate]" type="number" step="1" min="100"
                                       id="vnd_paypal_standard_rate" style="width: 70px; padding-right: 0;"
                                       value="<?php echo $settings['vnd_paypal_standard']['rate'] ?>"
                                <label for="vnd_paypal_standard_rate"><?php _e( 'Insert the exchange rate of this currency to VND', 'woo-viet' ) ?></label>
                            </fieldset>

                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php printf( __( 'Support VND for <a href="%s">the PayPal Express Checkout gateway</a>', 'woo-viet' ), 'https://docs.woocommerce.com/document/paypal-express-checkout/' ) ?></th>
                        <td>
                            <input name="settings[vnd_paypal_express_checkout][enabled]" type="hidden" value="no">
                            <input name="settings[vnd_paypal_express_checkout][enabled]" type="checkbox"
                                   id="vnd_paypal_express_checkout" value="yes"
								<?php if ( 'yes' == $settings['vnd_paypal_express_checkout']['enabled'] )
									echo 'checked="checked"' ?>>
                            <label for="vnd_paypal_express_checkout"><?php _e( 'Enabled', 'woo-viet' ) ?></label>

                            <fieldset><br/>
                                <select name="settings[vnd_paypal_express_checkout][currency]"
                                        id="vnd_paypal_express_checkout_currency">
									<?php
									foreach ( $paypal_supported_currencies as $currency ) {

										if ( strtoupper( $currency ) == $settings['vnd_paypal_express_checkout']['currency'] ) {
											printf( '<option selected="selected" value="%1$s">%1$s</option>', $currency );
										} else {
											printf( '<option value="%1$s">%1$s</option>', $currency );
										}

									}
									?>
                                </select>
                                <label for="vnd_paypal_express_checkout_currency"><?php _e( 'Select a PayPal supported currency (like USD, EUR, etc), which is used to convert VND prices', 'woo-viet' ) ?></label>
                                <br/>
                                <br/>

                                <input name="settings[vnd_paypal_express_checkout][rate]" type="number" step="1"
                                       min="100"
                                       id="vnd_paypal_express_checkout_rate" style="width: 70px; padding-right: 0;"
                                       value="<?php echo $settings['vnd_paypal_express_checkout']['rate'] ?>"
                                <label for="vnd_paypal_express_checkout_rate"><?php _e( 'Insert the exchange rate of this currency to VND', 'woo-viet' ) ?></label>
                            </fieldset>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                </p>

            </form>
            <div id="wooviet-admin-footer"
                style="border: 1px dotted; padding: 5px;">
	            <?php
	            printf( __( 'Wanna get support or give feedback? Please <a href="%1$s">rate Woo Viet</a> or post questions <a href="%2$s">in the forum</a>!', 'woo-viet' ),
                    'https://wordpress.org/support/plugin/woo-viet/reviews/?filter=5#new-post',
                    'https://wordpress.org/support/plugin/woo-viet/'
                )
	            ?>
            </div>
        </div><!-- #wrap ->
        <?php
	}

}