<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to handle Vietnam Currency Issues
 *
 * @author   htdat
 * @since    1.0
 *
 */
class WooViet_Currency {

	/**
	 * The new symbol of the VND currency
	 * @var string
	 * @use change_currency_symbol()
	 */
	protected $new_symbol;
	/**
	 * @var string
	 * @use convert_price_thousand_to_k()
	 */
	protected $thousand_text;

	/**
	 * Change the currency symbol "đ" to anything, e.g: VND, VNĐ, đồng
	 *
	 * @param string $new_symbol - Allow HTML tags like "<span style="color: red;"> đồng</span>"
	 */
	public function change_currency_symbol( $new_symbol = ' đ ' ) {
		$this->new_symbol = $new_symbol;
		add_filter( 'woocommerce_currency_symbol', array( $this, 'filter_woocommerce_currency_symbol' ), 10, 2 );
	}

	/**
	 * @see https://docs.woocommerce.com/document/change-a-currency-symbol/
	 *
	 * @use change_currency_symbol()
	 *
	 * @param $currency_symbol
	 * @param $currency
	 *
	 * @return mixed
	 */
	public function filter_woocommerce_currency_symbol( $currency_symbol, $currency ) {
		switch ( $currency ) {
			case 'VND':
				$currency_symbol = $this->new_symbol;
				break;
		}

		return $currency_symbol;
	}

	/**
	 * Change the stupid zeros in VND to the more "human" display.
	 * E.g: 50000 (VND) will be 50K (VND), or 50 thousands (VND)
	 *
	 * @param string $thousand_text - Allow HTML tags like "<span style="color: green;"> K</span>"
	 *
	 */
	public function convert_price_thousand_to_k( $thousand_text = ' K' ) {
		$this->thousand_text = $thousand_text;
		add_filter( 'formatted_woocommerce_price', array( $this, 'filter_formatted_woocommerce_price' ), 10, 5 );
	}

	/**
	 * @use convert_price_thousand_to_k()
	 *
	 * @param string $formatted_price
	 * @param float $price
	 * @param $decimals
	 * @param $decimal_separator
	 * @param $thousand_separator
	 *
	 * @return string
	 */
	public function filter_formatted_woocommerce_price( $formatted_price, $price, $decimals, $decimal_separator, $thousand_separator ) {

		if ( $price < 1000 ) {
			return $formatted_price;
		} else {
			$new_formatted_price = number_format( $price / 1000, $decimals, $decimal_separator, $thousand_separator ) . $this->thousand_text;

			return $new_formatted_price;
		}

	}
}