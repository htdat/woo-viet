<?php 
if (! defined( 'ABSPATH' )) {
    exit;
}

class WooViet_Shipping_Method extends WC_Shipping_Method {

    public function __construct( $instance_id = 0 ) {
        $this->id                    = 'wooviet_shipping';
        $this->instance_id           = absint( $instance_id );
        $this->method_title          = __( 'WooViet Shipping City', 'woo-viet' );
        $this->method_description    = __( 'Allow to set shipping price to city (district) in Viet Nam', 'woo-viet' );
        $this->supports              = array(
            'shipping-zones',
            'instance-settings',
        );
        $this->title = 'WooViet Shipping City';
        
        $this->init();

        add_action( 'wp_ajax_get_customer_city_choose', array($this, 'get_customer_city_choose_function') );

    }

    public function init() {

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables
        $this->title = $this->get_option( 'title' );
        $this->city = $this->get_option( 'city' );
        $this->city_cost = $this->get_option( 'city_cost' );
        $this->state_cost = $this->get_option( 'state_cost' );
        // Actions
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    public function get_customer_city_choose_function(){

        if( isset( $_POST['customer_city'] ) ) {
            $customer_city = $_POST['customer_city'];
        }

        //Don't forget to always exit in the ajax function.
        exit();
    }

    public function init_form_fields() {
        global $wpdb;
        $zone_selected = array();

        // Query get location zone and method ID
        $location_zone = $wpdb->get_results( 
            "
            SELECT {$wpdb->prefix}woocommerce_shipping_zone_locations.location_code, {$wpdb->prefix}woocommerce_shipping_zone_methods.instance_id 
            FROM {$wpdb->prefix}woocommerce_shipping_zone_locations
            INNER JOIN {$wpdb->prefix}woocommerce_shipping_zone_methods
            ON {$wpdb->prefix}woocommerce_shipping_zone_locations.zone_id = {$wpdb->prefix}woocommerce_shipping_zone_methods.zone_id
            "
            , ARRAY_A );

        for ($i = 0; $i < count($location_zone); $i++) {
            // Get location code
            $location_code[] = substr($location_zone[$i]['location_code'], 3);

            // Get method ID
            $instance_id[] = $location_zone[$i]['instance_id'];                   
        }

        $vn_state = array(
            'AN-GIANG'        => array(
                'Huyện An Phú' => 'Huyện An Phú',
                'Huyện Châu Phú' => 'Huyện Châu Phú',
                'Huyện Châu Thành' => 'Huyện Châu Thành',
                'Huyện Chợ Mới' => 'Huyện Chợ Mới',
                'Huyện Phú Tân' => 'Huyện Phú Tân',
                'Huyện Thoại Sơn' => 'Huyện Thoại Sơn',
                'Huyện Tịnh Biên' => 'Huyện Tịnh Biên',
                'Huyện Tri Tôn' => 'Huyện Tri Tôn',
                'Thành phố Châu Đốc' => 'Thành phố Châu Đốc',
                'Thành phố Long Xuyên' => 'Thành phố Long Xuyên',
                'Thị xã Tân Châu' => 'Thị xã Tân Châu'
            ),
            'BA-RIA-VUNG-TAU' => array(
                'Huyện Châu Đức' => 'Huyện Châu Đức',
                'Huyện Côn Đảo' => 'Huyện Côn Đảo',
                'Huyện Đất Đỏ' => 'Huyện Đất Đỏ',
                'Huyện Long Điền' => 'Huyện Long Điền',
                'Huyện Tân Thành' => 'Huyện Tân Thành',
                'Huyện Xuyên Mộc' => 'Huyện Xuyên Mộc',
                'Thành phố Bà Rịa' => 'Thành phố Bà Rịa',
                'Thành phố Vũng Tàu' => 'Thành phố Vũng Tàu'
            ),
        );

        // Get current method ID
        if( isset( $_REQUEST['instance_id'] ) ) {
            $current_instance_id = $_REQUEST['instance_id'];
            for ( $i = 0; $i < count( $instance_id ); $i++ ) {

                // Match current location ID
                if ( $current_instance_id == $instance_id[$i] ) {
                    foreach ($vn_state as $key => $value) {

                        if( $location_code[$i] == $key) {
                            $zone_selected_temp[$i] = $value;
                        }                       
                    }

                    $zone_selected = array_merge( $zone_selected, $zone_selected_temp[$i] );
                }
            }
        }

        $this->instance_form_fields = array(
            'title' => array(
                'title'       => __( 'Title', 'woo-viet' ),
                'type'        => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'woo-viet' ),
                'default'     => __( 'Wooviet shipping', 'woo-viet' ),
                'desc_tip'    => true,
            ),
            'city' => array(
                'title'         => __( 'City', 'woo-viet' ),
                'type'          => 'select',
                'class'         => 'wc-enhanced-select',
                'options'       => $zone_selected,
            ),
            'city_cost' => array(
                'title'         => __( 'City Cost', 'woo-viet' ),
                'type'          => 'price',
                'placeholder'   => '0',
                'description'   => __( 'Optional cost for shipping to city.', 'woo-viet' ),
                'default'       => '',
                'desc_tip'      => true,
            ),
            'state_cost' => array(
                'title'         => __( 'Cost', 'woo-viet' ),
                'type'          => 'price',
                'placeholder'   => '0',
                'description'   => __( 'Optional cost for shipping to state.', 'woo-viet' ),
                'default'       => '',
                'desc_tip'      => true,
            ),
            /*'services'  => array(
                'type'            => 'services',
                'class'           => 'rates_tab_field',
            ),*/
        );
                
    }

    /**
     * calculate_shipping function.
     *
     * @access public
     *
     * @param mixed $package
     *
     * @return void
     */

    public function calculate_shipping( $package = array() ) {
        if( $this->district == 'Huyện Châu Thành' ) {
            $this->cost = $this->get_option( 'cost' );
        } else {
            $this->cost = 25;
        }

        $this->add_rate( array(
          'id'   => $this->id,
          'label' => $this->title,
          'cost'   => $this->cost,
        ) );
    }

    /**
     * generate_services_html function.
     */
    /*public function generate_services_html() {
        ob_start();
        include( 'html-wf-services.php' );
        return ob_get_clean();
    }*/

}






