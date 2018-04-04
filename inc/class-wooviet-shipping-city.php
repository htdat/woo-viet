<?php 
if (! defined( 'ABSPATH' )) {
    exit;
}

class WooViet_Shipping_Method extends WC_Shipping_Method {

    public static $customer_city;
    public $zone_selected;

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
        /*$this->city_select = array(
            'city_title',
            'city_cost',
            'city_zone'
        );*/
        // Actions
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }


    public function init_form_fields() {
        global $wpdb;
        $this->zone_selected = array();

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
                'Huyện Châu Đức',
                'Huyện Côn Đảo',
                'Huyện Đất Đỏ',
                'Huyện Long Điền',
                'Huyện Tân Thành',
                'Huyện Xuyên Mộc',
                'Thành phố Bà Rịa',
                'Thành phố Vũng Tàu'
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

                    $this->zone_selected = array_merge( $this->zone_selected, $zone_selected_temp[$i] );
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
                'options'       => $this->zone_selected,
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
                'title'         => __( 'State Cost', 'woo-viet' ),
                'type'          => 'price',
                'placeholder'   => '0',
                'description'   => __( 'Optional cost for shipping to state if customer choose any city.', 'woo-viet' ),
                'default'       => '',
                'desc_tip'      => true,
            ),
            /*'city_select1'  => array(
                'type'            => 'city_select',
                'class'           => 'city_select',
            ),*/
        );
                
    }

    public function get_customer_city_choose(){

        if( isset( $_POST['customer_city'] ) ) {
            self::$customer_city = $_POST['customer_city'];
        }

        print_r(self::$customer_city);
        
        //Don't forget to always exit in the ajax function.
        exit();
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
        if( $this->city == self::$customer_city ) {
            $this->cost = $this->city_cost;
        } else {
            $this->cost = $this->state_cost;
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
    /*public function generate_city_select_html() {
        ob_start();
        ?>
        <tr valign="top" id="service_options" class="rates_tab_field" >
            <td class="forminp" colspan="2" style="padding-left:0px">
            <strong><?php _e( 'City zone', 'woo-viet' ); ?></strong><br/>
                <table class="widefat" style="width:100%">
                    <thead>
                        <th><?php _e( 'Title', 'woo-viet' ); ?></th>
                        <th><?php _e('City zone','woo-viet');?></th>
                        <th><?php _e('Price','woo-viet');?></th>
                        
                    </thead>
                    <tbody>
                        
                        <tr>
                            <td>
                                <input class="input-text regular-input " type="text" name="woocommerce_wooviet_shipping_city_select1_city_title" id="woocommerce_wooviet_shipping_city_title" placeholder="" value="<?php echo $this->get_option( 'city_title' ) ?>">
                            </td>

                            <td>
                                <select name="woocommerce_wooviet_shipping_city" id="" class="select wc-enhanced-select  enhanced">
                                    <?php foreach ($this->zone_selected as $value) : ?>
                                        <option value="<?php echo $value ?>" <?php ( $this->get_option( 'city' ) == $value ) ? 'selected' : '' ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            
                            <td>
                                <input class="input-text regular-input " type="text" name="woocommerce_wooviet_shipping_city_cost" id="woocommerce_wooviet_shipping_city_cost" placeholder="" value="<?php echo $this->get_option( 'city_cost' ) ?>">
                            </td>
                            
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>



        <?php return ob_get_clean();
    }*/

    

}






