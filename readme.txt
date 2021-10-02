=== Woo Viet - WooCommerce for Vietnam ===
Contributors: htdat, thup90, longnguyen
Tags: OnePay WooCommerce, OnePay Vietnam, WooCommerce Vietnam, vietnam, vietnamese, vietnam provinces, paypal for vietnam dong, vnd, vietnam dong, vietnam currency, vietnam customization
Requires at least: 4.3
Tested up to: 5.8.1
Requires PHP: 7.0
Stable tag: 1.5.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add features to WooCommerce stores having anything related to Vietnam: currency, shipping address, PayPal and more.

== Description ==

**"Woo Viet - WooCommerce for Vietnam" brings the features that help to run WooCommerce stores and customize them for Vietnam much easier.**

Xem phiên bản tiếng Việt tại đây [https://vi.wordpress.org/plugins/woo-viet/](https://vi.wordpress.org/plugins/woo-viet/)

= FEATURES =

* Change the VND currency symbol `đ` to anything, e.g: `VND`, `VNĐ`, `đồng`, etc.
* Add provinces for Vietnam when visitors select Vietnam as a country of the shipping (billing) address. Arrange the address fields to the Vietnam standard: Country - Province - District - Address.
* Add districts to Vietnam provinces.
* Convert `000` of prices to `K` (or anything). E.g: `50000` (VND) will be `50K` (VND), or `50 thousand` (VND).
* Support `VND` for [the PayPal Standard gateway](https://docs.woocommerce.com/document/paypal-standard/). Convert `VND` prices to any PayPal supported currency before sending visitors to the PayPal pages.
* Support `VND` for [the PayPal Express Checkout gateway](https://docs.woocommerce.com/document/paypal-express-checkout/).
* Add [the OnePay domestic (local ATM cards) gateway](http://onepay.com.vn/). Implement all methods in [the official documents](https://mtf.onepay.vn/developer/?page=modul_noidia_php): QueryDR, IPN, and Return URL.
* Add [the OnePay international (Visa, Master, JCB, Amex cards) gateway](http://onepay.com.vn/). Implement all methods in [the official documents](https://mtf.onepay.vn/developer/?page=modul_quocte_php): QueryDR, IPN, and Return URL.


= ROAD MAP =

In the future, this plugin will add more and more features for the Vietnam market:

* Integrate at least one solution for collecting money by phone cards.
* Integrate the Vietnam payment gateways like Momo, VNPay, BaoKim, Ngan Luong, etc.
* Integrate the Vietnam shipping solutions like Giaohangnhanh, Giao Hang Tiet Kiem, VNPost, ViettelPost, etc.

= WHERE CAN I CONTRIBUTE MY CODE OR IDEA? =

* You can report bugs or contribute code on [this GitHub repo](https://github.com/htdat/woo-viet).
* Please also do let us know if the "bug" is just a grammar/spelling error in both English and Vietnamese. We try to make our products as perfect as possible.

= INSTALLATION =

Follow these steps to install and use the plugin:

1. Upload the plugin files to the `/wp-content/plugins/woo-viet` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the `Plugins` screen in WordPress.
1. Go to the `WooCommerce -> Woo Viet` screen and configure the plugin.

== Installation ==

Follow these steps to install and use the plugin:

1. Upload the plugin files to the `/wp-content/plugins/woo-viet` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Go to the `WooCommerce -> Woo Viet` screen and configure the plugin.


== Frequently Asked Questions ==

= WHERE CAN I CONTRIBUTE MY CODE OR IDEA? =

* You can report bugs or contribute code on [this GitHub repo](https://github.com/htdat/woo-viet).
* Please also do let us know if the "bug" is just a grammar/spelling error in both English and Vietnamese. We try to make our products as perfect as possible.

== Screenshots ==

1. The settings page under WooCommerce -> Woo Viet.
2. Prices are changed to "K", and the symbol is now "VND".
3. List provinces and districts when selecting Vietnam.
4. Let clients know about the currency conversion before switching to the PayPal pages.
5. OnePay domestic (local ATM cards) gateway.

== Changelog ==

See all change logs on [GitHub repo](https://github.com/htdat/woo-viet#changelog).

== Upgrade Notice ==

= 1.5.0 =

Version 1.5.0 comes with a new gateway OnePay International and other small improvements.

= 1.4 =

Version 1.4 arranges the address fields to the Vietnam standard (Country - Province - District - Address), supports VND for PayPal Express Checkout, and fixes issues on PayPal Standard and OnePay Domestic gateways.

= 1.3 =
The new version 1.3 comes with the feature "Add the OnePay domestic (local ATM cards) gateway]"

= 1.2 =
The new version 1.2 comes with the feature "Add districts to Vietnam provinces".
