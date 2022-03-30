<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manage the notices of the plugin
 *
 * @author   htdat
 * @since    1.3.1
 *
 */
class WooViet_Notices {

	var $settings = '';

	static $default_settings = array(
		'installation_time'               => 0,
		'displaying_survey_after_4_weeks' => 'not_yet',
	);

	/**
	 * WooViet_Notices constructor.
	 */
	public function __construct() {
		$this->settings = self::get_settings();
		$this->set_installation_time();
		$this->manage_displaying_survey_after_4_weeks();

	}

	public function get_settings() {
		$settings = get_option( 'woo-viet_notices', self::$default_settings );
		$settings = wp_parse_args( $settings, self::$default_settings );

		return $settings;
	}

	/**
	 * Set the installation time
	 */
	public function set_installation_time() {
		if ( ! $this->settings['installation_time'] ) {
			$this->settings['installation_time'] = time();
			$this->save_settings();
		}
	}

	/**
	 * Save settings for this classe
	 */
	public function save_settings() {
		update_option( 'woo-viet_notices', $this->settings );
	}

	/**
	 * Manage the notice 'displaying_survey_after_4_weeks'
	 */
	public function manage_displaying_survey_after_4_weeks() {
		$display_time = $this->settings['installation_time'] + ( 4 * 7 * 24 * 60 * 60 ); // 4 weeks; 7 days; 24 hours; 60 mins; 60 secs

		// Manage the dismiss action
		if ( isset( $_GET['wooviet_dismiss'] )
		     &&
		     ( 'displaying_survey_after_4_weeks' == $_GET['wooviet_dismiss'] )
		) {
			$this->settings['displaying_survey_after_4_weeks'] = 'done';
			$this->save_settings();
		}

		// Display the message
		if ( $display_time < time() &&
		     'not_yet' == $this->settings['displaying_survey_after_4_weeks']
		) {

			add_action( 'admin_notices', array( $this, 'add_displaying_survey_after_4_weeks' ) );

		}
	}

	/**
	 * The HTML code to display in the admin notice
	 */
	public function add_displaying_survey_after_4_weeks() {

		$line1 = __( 'Please help us to improve Woo Viet.', 'woo-viet' );
		$line2 = __( 'Rate us!', 'woo-viet' );
		$line3 = __( 'Or run a short survey:', 'woo-viet' );
		$line4 = '<a href="https://goo.gl/forms/QIMkNrRIxgERBBcm2" target="_blank">tiếng Việt</a> - <a href="https://goo.gl/forms/N4GxvBtaIg6iryx43" target="_blank">English</a>';
		$line5 = __( 'Dismiss this notice', 'woo-viet' );
		$link  = admin_url( 'admin.php?page=woo-viet&wooviet_dismiss=displaying_survey_after_4_weeks' );

		printf( '
			<div class="notice notice-success"> 
				<p><strong>%1$s</strong></p>
				<p><strong>
					<a href="https://wordpress.org/support/plugin/woo-viet/reviews/?filter=5#new-post" target="_blank">%2$s</a> %3$s %4$s
				</strong></p>
				<p>
					<a href="%6$s">%5$s</a>
				</p>
			</div>	    
	    ', $line1, $line2, $line3, $line4, $line5, $link );
	}

}
