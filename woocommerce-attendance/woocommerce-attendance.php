<?php
/*
Plugin Name: WooCommerce Attendance
Plugin URI:  http://xando.be
Description: You need both the WooCommerce and the WooCommerce Memberships plugin.
Version:     1.0
Author:      Xando
Author URI:  http://xando.be
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: woocommerce-attendance
*/
$attendees_obj = null;

#region Includes
/**
 * Includes
 */
if ( is_admin() ) {
    // We are in admin mode
    require_once( dirname(__file__).'/includes/admin/woocommerce-attendance-admin.php' );

}
#region Text Domain
function woocommerce_attendance_load_plugin_textdomain() {
    load_plugin_textdomain( 'woocommerce-attendance', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'woocommerce_attendance_load_plugin_textdomain' );
#endregion
#endregion

#region (De)Activation
/**
 * Hook to run after plugin activation
 */
function woocommerce_attendance_plugin_activation() {
    //dependent plugins
    $woocommerce = 'woocommerce/woocommerce.php';
    $woocommerce_memberships = 'woocommerce-memberships/woocommerce-memberships.php';

    // replace this with your version
    $woocommerce_version = '2.4.12';
    $woocommerce_memberships_version = '1.4.1';

    //check woocommerce
    xando_check_for_plugin('WooCommerce',$woocommerce, $woocommerce_version);
    //check woocommerce memberships
    //xando_check_for_plugin('WooCommerce Memberships',$woocommerce_memberships, $woocommerce_memberships_version);
   
}
register_activation_hook( __FILE__, 'woocommerce_attendance_plugin_activation' );
register_activation_hook( __FILE__, 'jal_install' );//mij

/**
 * Hook to run after plugin deactivation
 */
 

 
 
function woocommerce_attendance_plugin_deactivation() {

}
register_deactivation_hook( __FILE__, 'woocommerce_attendance_plugin_deactivation' );
#endregion

#region Admin Menu
function woocommerce_attendance_settings_menu() {
    $hook = add_submenu_page(
        'woocommerce', //parent-slug
        __( 'Attendance Settings', 'woocommerce-attendance' ), //page-title
        __('Attendances', 'woocommerce-attendance'), //menu-title
        'manage_woocommerce', //capability
        'attendance', //slug
        'woocommerce_attendance_listing_page'//callback
    );
    add_action( "load-$hook", 'add_options' );
}
add_action('admin_menu', 'woocommerce_attendance_settings_menu');


/**
 * Screen options
 */
function add_options() {
    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;
    global $attendees_obj, $events_obj;

    $option = 'per_page';

    if ( $id > 0 ) {
        $args = array(
            'label' => __('Attendees per page:', 'woocommerce-attendance'),
            'default' => 10,
            'option' => 'attendees_per_page'
        );
        add_screen_option($option, $args);
        $attendees_obj = new Attendee_List();
    } else {
        $args = array(
            'label' => __('Events per page:', 'woocommerce-attendance'),
            'default' => 10,
            'option' => 'events_per_page'
        );
        add_screen_option($option, $args);
        $events_obj = new Event_List();
    }

}
#endregion

#region General Functions

if ( !function_exists('woocommerce_attendance_init' ) ) {
    function woocommerce_attendance_init() {
        //register_setting( 'woocommerce_attendance_setting_example', 'foo' );
        //register_setting( 'woocommerce_attendance_setting_demo', 'bar' );
    }
}

if ( !function_exists( 'woocommerce_attendance_get_demo_setting' ) ) {
    function woocommerce_attendance_get_demo_setting() {
        return get_option( 'woocommerce_attendance_setting' );
    }
}

/**
 * Check if depending plugins are activated
 */
if ( !function_exists('xando_check_for_plugin' ) ) {
    function xando_check_for_plugin ($name, $plugin, $version) {
        $error = false;

        //check plugin dependent
        if ( file_exists(WP_PLUGIN_DIR . '/' . $plugin) ) {
            $error_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
            $error = !version_compare($error_data['Version'], $version, '>=') ? true : false;
        }
        if ( $error ) {
            $string = printf(__('Please make sure that the %s plugin, with at least version %s, is installed and activated before trying to install the WooCommerce Attendance plugin.', 'woocommerce-attendance'), $name, $version);
            echo '<strong>' . $string . '</strong>';

            //Adding @ before will prevent XDebug output
            @trigger_error($string, E_USER_ERROR);
        }
    }
}

/**
 * Adds admin bar items
 */
function woocommerce_attendance_admin_bar_render() {
    $attendance_trans = __('Attendances', 'woocommerce-attendance');
    $page_url = esc_url(get_admin_url(null, 'admin.php?page=attendance&tab=attendances'));
    xando_admin_bar_render(
        $attendance_trans,
        $page_url,
        '',
        array(
            'class' => 'attendance',
            'title' => $attendance_trans
        )
    );
    /*
    if(is_admin()) {
        foreach ( get_woocommerce_product_list() as $product ) {
            xando_admin_bar_render(
                $product['title'],
                $page_url . '&id=' . $product['id'],
                $attendance_trans
            );
        }
    }*/
}
add_action('admin_bar_menu', 'woocommerce_attendance_admin_bar_render', 100);


add_action('plugins_loaded', 'wan_load_textdomain');
function wan_load_textdomain() {
	load_plugin_textdomain( 'woocommerce-attendance', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}






if(!function_exists('xando_admin_bar_render')) {
    /**
     * Adds menu parent or submenu item.
     * @param string $title the label of the menu item
     * @param string $href the link to the item (settings page or ext site)
     * @param string $parent Parent label (if creating a submenu item)
     * @param array $custom_meta
     */
    function xando_admin_bar_render ($title, $href = '', $parent = '', $custom_meta = array()) {
        global $wp_admin_bar;

        if ( !is_super_admin()
            || !is_object($wp_admin_bar)
            || !function_exists('is_admin_bar_showing')
            || !is_admin_bar_showing()
        ) {
            return;
        }

        // Generate ID based on the current filename and the title supplied.
        $id = sanitize_key(basename(__FILE__, '.php') . '-' . $title);

        // Generate the ID of the parent.
        if(strlen($parent) > 0) {
            $parent = sanitize_key(basename(__FILE__, '.php') . '-' . $parent);
        }

        // links from the current host will open in the current window
        $meta = strpos($href, site_url()) !== false ? array() : array('target' => '_blank'); // external links open in new tab/window
        $meta = array_merge($meta, $custom_meta);

        $wp_admin_bar->add_node(array(
            'parent' => $parent,
            'id' => $id,
            'title' => $title,
            'href' => $href,
            'meta' => $meta,
        ));
    }
}

/**
 * Add Ultraness links to plugin page
 * @param $links
 * @return array
 */
function woocommerce_attendance_plugin_action_links( $links ) {
    $links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=attendance') ) .'">'.__('Attendances', 'woocommerce-attendance').'</a>';
    $links[] = '<a href="http://ultraness.com" target="_blank">'.__('More plugins from Ultraness', 'woocommerce-attendance').'</a>';
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'woocommerce_attendance_plugin_action_links' );

/**
 * Add CSS files
 */
function woocommerce_attendance_css() {
    if( current_user_can( 'level_5' ) ){
        wp_register_style( 'woocommerce-attendance-css', plugin_dir_url( __FILE__ ) . 'assets/css/woocommerce-attendance.css','','', 'screen' );
        wp_enqueue_style( 'woocommerce-attendance-css' );
    }
}
add_action( 'admin_enqueue_scripts', 'woocommerce_attendance_css' );
add_action( 'wp_enqueue_scripts', 'woocommerce_attendance_css' );
/**
 * Add JS files
 */
function woocommerce_attendance_js() {
    if( current_user_can( 'level_5' ) ){
        $translation_array = array(
            'title'=> __('title', 'woocommerce-attendance') ,
            'last_name' => __('Last Name', 'woocommerce-attendance'),
            'first_name' =>  __('First Name', 'woocommerce-attendance') ,
            'company' =>   __('company', 'woocommerce-attendance'),
            'email' => __('email', 'woocommerce-attendance') ,
            'country' =>  __('country', 'woocommerce-attendance'),
            'address' =>   __('address', 'woocommerce-attendance'),
            'city' => __('city', 'woocommerce-attendance'),
            'state' =>  __('state', 'woocommerce-attendance') ,
            'postcode' =>  __('postcode', 'woocommerce-attendance')
        );

        wp_register_script( 'shortcodes',  plugin_dir_url( __FILE__ ) . 'assets/js/text-button.js' );

        wp_localize_script('shortcodes', 'shortcode', $translation_array );

        wp_enqueue_script('shortcodes' );
    }
}
add_action( 'admin_enqueue_scripts', 'woocommerce_attendance_js' );


/*
function woocommerce_attendance_add_order_item_meta( $item_id, $values, $cart_item_key ) {
    wc_add_order_item_meta( $item_id, 'sku', 'testerdetest' , false );
    wc_add_order_item_meta( $item_id, 'sku', 'testerdetest' , false );
}
add_action( 'woocommerce_add_order_item_meta', 'woocommerce_attendance_add_order_item_meta', 10, 3 );
*/
function woocommerce_attendance_add_item_meta( $order_id ) {
    $id = 0;
    foreach ( WC()->cart->cart_contents as $product ) {
        $id = $product['product_id'];
    }
    update_post_meta( $order_id, '_product_id', $id );
    update_post_meta( $order_id, '_attended', 'yes' );
}
add_action ('woocommerce_checkout_update_order_meta', 'woocommerce_attendance_add_item_meta');

function woocommerce_attendance_get_attendees_object() {
    global $attendees_obj;
    return $attendees_obj;
}



#endregion