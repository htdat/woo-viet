<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create the admin page under wp-admin -> WooCommerce -> WooCommerce for Vietnam
 *
 * @author   htdat
 * @since    1.0
 *
 */
class WooViet_Admin_Page {

	var $message = '';

	public function __construct() {

		if (isset($_REQUEST['wooviet_nonce']) && isset ($_REQUEST['action']) && 'wooviet_save_settings' == $_REQUEST['action'])
			$this->save_settings();

		add_action('admin_menu', array($this, 'register_submenu_page'));

	}

	public function register_submenu_page() {
		add_submenu_page(
			'woocommerce',
			'WooCommerce for Vietnam Settings',
			'WooCommerce for Vietnam',
			'manage_options',
			'woocommerce-for-vietnam',
			array( $this, 'admin_page_html' )
		);
	}

	public function save_settings(){
		if ( wp_verify_nonce( $_REQUEST['wooviet_nonce'], 'wooviet_save_settings') ){
			update_option('woocommerce-for-vietnam', $_REQUEST['settings']);

			$this->message =
				'<div class="updated notice"><p><strong>' .
				__('Settings saved', 'woocommerce-for-vietnam') .
		        '</p></strong></div>'
			;

		} else {

			$this->message =
				'<div class="error notice"><p><strong>' .
				__('Please refresh this page. Can not save settings!', 'woocommerce-for-vietnam') .
				'</p></strong></div>'
			;
		}
	}
	public function admin_page_html() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = WooViet::get_settings();
		//var_dump($settings); exit;


		?>
		<div class="wrap">
			<h1><?= esc_html( get_admin_page_title() ); ?></h1>
			<form name="woocommerce_for_vietnam" method="post">
				<?php echo $this->message ?>
				<input type="hidden" id="action" name="action" value="wooviet_save_settings">
				<input type="hidden" id="wooviet_nonce" name="wooviet_nonce" value="<?php echo wp_create_nonce( 'wooviet_save_settings' ) ?>">
				<table class="form-table">
					<tbody>
					<tr>
						<th scope="row"><?php _e('Add provinces for Vietnam', 'woocommerce-for-vietnam')?></th>
						<td>
							<input name="settings[add_province][enabled]" type="hidden" value="no">
							<input name="settings[add_province][enabled]" type="checkbox" id="add_province" value="yes"
								<?php if('yes' == $settings['add_province']['enabled'] ) echo 'checked="checked"'?>>
							<label for="add_province"><?php _e('Enabled', 'woocommerce-for-vietnam')?></label>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php _e('Change VND currency symbol', 'woocommerce-for-vietnam')?></th>
						<td>
							<input name="settings[change_currency_symbol][enabled]" type="hidden" value="no">
							<input name="settings[change_currency_symbol][enabled]" type="checkbox" id="change_currency_symbol" value="yes"
								<?php if('yes' == $settings['change_currency_symbol']['enabled'] )echo 'checked="checked"'?>>
							<label for="change_currency_symbol"><?php _e('Enabled', 'woocommerce-for-vietnam')?></label>
							<br />
							<br />
							<input type="text" name="settings[change_currency_symbol][text]"
							       value="<?php echo $settings['change_currency_symbol']['text']?>"
							       id="change_currency_symbol_text"class="small-text">
							<label for="change_currency_symbol_text"><?php _e('Insert a text to change the default symbol <code>đ</code>', 'woocommerce-for-vietnam')?></label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Convert <code>000</code> of prices to K (or anything)', 'woocommerce-for-vietnam')?></th>
						<td>
							<input name="settings[convert_price][enabled]" type="hidden" value="no">
							<input name="settings[convert_price][enabled]" type="checkbox" id="convert_price" value="yes"
								<?php if('yes' == $settings['convert_price']['enabled'] ) echo 'checked="checked"'?>>
							<label for="convert_price"><?php _e('Enabled', 'woocommerce-for-vietnam')?></label>

							<fieldset><br />
								<input type="text" name="settings[convert_price][text]"
								       value="<?php echo $settings['convert_price']['text']?>"
								       id="convert_price_text" class="small-text">
								<label for="convert_price_text"><?php _e('Choose what you want to change. E.g:', 'woocommerce-for-vietnam')?> <code>K</code>, <code>nghìn</code>, <code>ngàn</code></label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php printf(__('Support VND for <a href="%s">the Paypal Standard gateway</a>','woocommerce-for-vietnam'), 'https://docs.woocommerce.com/document/paypal-standard/') ?></th>
						<td>
							<input name="settings[vnd_paypal_standard][enabled]" type="hidden" value="no">
							<input name="settings[vnd_paypal_standard][enabled]" type="checkbox" id="vnd_paypal_standard" value="yes"
								<?php if('yes' == $settings['vnd_paypal_standard']['enabled'] ) echo 'checked="checked"'?>>
							<label for="vnd_paypal_standard"><?php _e('Enabled', 'woocommerce-for-vietnam')?></label>

							<fieldset><br />
								<select name="settings[vnd_paypal_standard][currency]" id="vnd_paypal_standard_currency">
									<?php
									$paypal_supported_currencies = array( 'AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP', 'RMB', 'RUB' );
									foreach ($paypal_supported_currencies as $currency ) {

										if ( strtoupper($currency) == $settings['vnd_paypal_standard']['currency']){
											printf('<option selected="selected" value="%1$s">%1$s</option>', $currency);
										} else {
											printf('<option value="%1$s">%1$s</option>', $currency);
										}

									}
									?>
								</select>
								<label for="vnd_paypal_standard_currency"><?php _e('Select a Paypal supported currency (like USD, EUR, etc), which is used to convert VND prices', 'woocommerce-for-vietnam')?></label>
								<br />
								<br />

								<input name="settings[vnd_paypal_standard][rate]" type="number" step="1" min="100" id="vnd_paypal_standard_rate" style="width: 70px;"
								       value="<?php echo $settings['vnd_paypal_standard']['rate']?>"
								<label for="vnd_paypal_standard_rate"><?php _e('Insert the exchange rate of this currency to VND', 'woocommerce-for-vietnam')?></label>
							</fieldset>

						</td>
					</tr>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
				</p>

			</form>
		</div><!-- #wrap ->
        <?php
	}

}