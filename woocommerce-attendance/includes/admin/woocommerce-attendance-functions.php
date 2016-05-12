<?php
/**
 * Get all products sort alphabetically, return in array
 *
 * @return array
 */
function get_woocommerce_product_list() {
    $full_product_list = array();
    $loop = new WP_Query( array( 'post_type' => array('product', 'product_variation'), 'posts_per_page' => -1 ) );

    while ( $loop->have_posts() ) : $loop->the_post();
        $theid = get_the_ID();
        $sku = get_post_meta($theid, '_sku', true );
        $thetitle = get_the_title();
        $categories = wp_get_object_terms ($theid, 'product_cat', array('fields' =>'names'));
        // add product to array but don't add the parent of product variations
        if(is_array($categories) && !in_array('Lidmaatschappen', $categories)):
            $full_product_list[] = array(
                'id' => $theid,
                'title' => $thetitle,
                'sku' => $sku,
            );
        endif;
    endwhile; wp_reset_query();
    // sort into alphabetical order, by title
    usort($full_product_list, function($a, $b) {
        return $a['title'] > $b['title'];
    });

    return $full_product_list;
}


function split_string_into_key_value_pair ($string, $delimiter1, $delimiter2) {
    $array = array();

    $x = explode($delimiter1, $string);
    foreach ( $x as $s ) {
        list($k, $v) = explode($delimiter2, $s);
        $array[$k] = $v;
    }

    return $array;
}

/**
 * Override usort
 *
 * @param $a
 * @param $b
 * @return bool
 */
function woocommerce_attendance_usort($a, $b) {
    $orderby    = isset($_GET['orderby']) ? $_GET['orderby']: array_keys($a)[0];
    $order      = isset($_GET['order']) ? $_GET['order'] : 'desc';

    if ( $order == 'asc' ) {
        return $a[$orderby] > $b[$orderby];
    } else {
        return $a[$orderby] < $b[$orderby];
    }
}

function woocommerce_attendance_in_array_r($item , $array){
    return preg_match('/"'.$item.'"/i' , json_encode($array));
}

#region GET
if(isset($_GET['action'])) :
    $action = $_GET['action'];
    $product = (isset($_GET['id'])) ? get_post($_GET['id']) : '';

    switch($action) :
        case 'attend':
            $id = $_GET['guest'];
            $attend = $_GET['attend'];
            woocommerce_attendance_set_attend($id, $attend);
            break;
        case 'print-tag':
            woocommerce_attendance_print_tag($product, $_GET['guest']);
            break;
        case 'print-tags':
            woocommerce_attendance_print_tag($product, explode('-', $_GET['guests']));
            break;
        case 'print-certificate':
            woocommerce_attendance_print_certificate($product, $_GET['guest']);
            break;
        case 'print-certificates':
            woocommerce_attendance_print_certificate($product, explode('-', $_GET['guests']));
            break;
        case 'send-email':
            woocommerce_attendance_send_email($product, array($_GET['guest']));
            break;
        // case 'send-emailALL':
        //     woocommerce_attendance_send_email($product, array($_GET['guest']), "All");
        //     break;
        case 'send-emails':
            woocommerce_attendance_send_email($product, explode('-', $_GET['guests']));
            break;
    endswitch;
endif;

function woocommerce_attendance_set_attend($id, $will_attend) {
    global $wpdb;
    $wpdb->query($wpdb->prepare("
      UPDATE {$wpdb->prefix}postmeta
      SET meta_value = '%s'
      WHERE post_id = '%s'
      AND meta_key = '_attended'",
        $will_attend,
        $id
    ));
    $wpdb->flush();
    header("Location: ".admin_url(sprintf('/admin.php?page=%s&id=%s&tab=attendances', $_REQUEST['page'], $_GET['id']), 'http'));
    exit;
}

function woocommerce_attendance_get_template($name, $product = null) {
    $plugin_folder = WP_PLUGIN_DIR.'/woocommerce-attendance/includes/templates/';
    $theme_folder = get_stylesheet_directory();
    //if id is not null, check theme folder for specific template
    if(!is_null($product)) {
        if(file_exists($theme_folder.'/woocommerce-attendance/'. $product)) {
            //specific id template exists, use that one
            return $theme_folder . '/woocommerce-attendance/' .$product;
        }
    }
    //check theme folder for the file
    if(file_exists($theme_folder.'/woocommerce-attendance/'.$name)) {
        //file overridden in theme, use that one
        $template = $theme_folder.'/woocommerce-attendance/'.$name;
    } else {
        //use plugin files
        $template = $plugin_folder.$name;
    }
    return $template;
}

function woocommerce_attendance_create_content($attendee, $file, $title = '') {
    return str_replace([
        '[id]',
        '[slug]',
        '[last_name]',
        '[first_name]',
        '[company]',
        '[attended]',
        '[email]',
        '[country]',
        '[address]',
        '[city]',
        '[state]',
        '[postcode]',
        '[title]',
    ], [
        $attendee['ID'],
        $attendee['slug'],
        $attendee['last_name'],
        $attendee['first_name'],
        $attendee['company'],
        $attendee['attended'],
        $attendee['email'],
        $attendee['country'],
        $attendee['address'],
        $attendee['city'],
        $attendee['state'],
        $attendee['postcode'],
        $title,
    ],
        file_get_contents($file)
    );
}

//mij
function woocommerce_attendance_create_content_message($attendee, $message, $title = '') {
    return str_replace([
        '[id]',
        '[slug]',
        '[last_name]',
        '[first_name]',
        '[company]',
        '[attended]',
        '[email]',
        '[country]',
        '[address]',
        '[city]',
        '[state]',
        '[postcode]',
        '[title]',
    ], [
        $attendee['ID'],
        $attendee['slug'],
        $attendee['last_name'],
        $attendee['first_name'],
        $attendee['company'],
        $attendee['attended'],
        $attendee['email'],
        $attendee['country'],
        $attendee['address'],
        $attendee['city'],
        $attendee['state'],
        $attendee['postcode'],
        $title,
    ],
        $message
        
    );
}

function woocommerce_attendance_print_to_pdf($filename, $content) {
    set_time_limit(300);
    ini_set('memory_limit', '-1');

    $dompdf = new DOMPDF();
    $dompdf->load_html($content);
    $dompdf->set_paper( 'letter' , 'portrait' );
    $dompdf->render();
    $dompdf->stream($filename);

    echo "<script>window.close();</script>";
    exit();
}

/**
 * Print the tag (or if $id is an array; tags) PDF
 *
 * @param object $product
 * @param int|array $id
 */
function woocommerce_attendance_print_tag($product, $id) {
    $master = woocommerce_attendance_get_template('master.html');
    $template = woocommerce_attendance_get_template('nametag.html');
    $template_single = woocommerce_attendance_get_template('nametag-single.html');
    if(is_array($id)) {
        $filetype = 'tags-';
        $list = '';
        foreach($id as $attendee_id) {
            $a = woocommerce_attendance_get_attendee($attendee_id);
            $list .= woocommerce_attendance_create_content($a, $template);
        }
        $content = sprintf(
            file_get_contents($master),
            $product->post_title,
            $list
        );
        $filename = $product->post_name.'.pdf';

    } else {
        $filetype = 'tag-';
        $attendee = woocommerce_attendance_get_attendee($id);
        $content = sprintf(
            file_get_contents($master),
            $product->post_title,
            woocommerce_attendance_create_content($attendee, $template_single)
        );
        $filename = $product->post_name.'-'.$attendee['slug'].'.pdf';
    }
    woocommerce_attendance_print_to_pdf($filetype.$filename, $content);
}

/**
 * Print the certificate (or if $id is an array; certificates) PDF
 *
 * @param object $product
 * @param int|array $id
 */
function woocommerce_attendance_print_certificate($product, $id) {
    $master = woocommerce_attendance_get_template('master.html');
    $template = woocommerce_attendance_get_template('certificate.html');
    $template_single = woocommerce_attendance_get_template('certificate-single.html');
    if(is_array($id)) {
        $filetype = 'certificates-';
        $list = '';
        foreach($id as $attendee_id) {
            $a = woocommerce_attendance_get_attendee($attendee_id);
            $list .= woocommerce_attendance_create_content($a, $template);
        }
        $content = sprintf(
            file_get_contents($master),
            $product->post_title,
            $list
        );
        $filename = $product->post_name.'.pdf';

    } else {
        $filetype = 'certificate-';
        $attendee = woocommerce_attendance_get_attendee($id);
        $content = sprintf(
            file_get_contents($master),
            $product->post_title,
            woocommerce_attendance_create_content($attendee, $template_single)
        );
        $filename = $product->post_name.'-'.$attendee['slug'].'.pdf';
    }
    woocommerce_attendance_print_to_pdf($filetype.$filename, $content);
}

/**
 * Send the email (or if $id is an array; emails)
 *
 * @param object $product
 * @param array $id
 */
function woocommerce_attendance_send_email($product, $id, $allOrNot="") {
    $master = woocommerce_attendance_get_template('master.html');
    $template = woocommerce_attendance_get_template('email.html', 'template-'.$product.'.html');
    $product = get_post($product);
    global $wpdb;
    
    
    
    $idie = (isset($_GET['id'])) ? $_GET['id'] : 0;
    
    $ids = $wpdb->get_col('SELECT nameId FROM wp_emailtemplates');
    if(!in_array($idie, $ids)){
        $idie = 0;
    }   
  

    foreach($id as $attendee_id) {
        $a = woocommerce_attendance_get_attendee($attendee_id);
        $mailAttendee=  $a['email'];
        
        $attendedurl = $wpdb->get_var(
        "SELECT meta_value
         FROM {$wpdb->prefix}postmeta WHERE post_id IN(
             SELECT post_id 
             FROM {$wpdb->prefix}postmeta
             WHERE post_id IN(
                    SELECT post_id
                    FROM {$wpdb->prefix}postmeta
                    WHERE meta_key = '_billing_email'
                          AND meta_value = '$mailAttendee') 
             AND meta_key = '_product_id'
             AND meta_value = '$idie')
         AND meta_key = '_attended'");
   
    if($allOrNot == "All"){
        $newTitle = getNewTitleFromDb($idie, 'All');
        $newMessage = getNewMessageFromDb($idie, 'All'); 
    }
    else if($allOrNot=="Appart"){
       switch($attendedurl){
        case 'yes':   
        $newTitle = getNewTitleFromDb($idie, 'Attended');
        $newMessage = getNewMessageFromDb($idie, 'Attended');
        break;
        case 'no':  
        $newTitle = getNewTitleFromDb($idie, 'Did not attend');
        $newMessage = getNewMessageFromDb($idie, 'Did not attend');
        break;
        default:  
        $newTitle = getNewTitleFromDb(0, 'All');
        $newMessage = getNewMessageFromDb(0, 'All'); 
        }
    }else{
        $newTitle = getNewTitleFromDb('0', 'All');
        $newMessage = getNewMessageFromDb('0', 'All');
    }
  
    if(isset($newTitle))
    $subject = $newTitle;
    else
    $subject =getNewTitleFromDb($idie, 'All');
    //mij
    if(isset($newMessage)){
        $message = woocommerce_attendance_create_content_message($a, $newMessage, $subject);
    }
    else{
        $newMessage = getNewMessageFromDb($idie, 'All');
        $message = woocommerce_attendance_create_content_message($a, $newMessage, $subject);
   }

    $to = $a['email'];
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    wp_mail($to, $subject, $message, $headers);
    }

}

function getNewTitleFromDb($id, $attended){
    global $wpdb;
     return $wpdb->get_var("SELECT title FROM wp_emailtemplates
        WHERE nameId = $id AND attended = '$attended'");
     
   
}
function getNewMessageFromDb($id, $attended){
    global $wpdb;
     return $wpdb->get_var("SELECT message FROM wp_emailtemplates
        WHERE nameId = $id AND attended = '$attended'");    
}



function woocommerce_attendance_get_attendee($id) {
    global $wpdb;
    $wpdb->query("SET SESSION group_concat_max_len = 1000000");
    $wpdb->query($wpdb->prepare("SELECT post_id as order_id, GROUP_CONCAT(CONCAT(meta_key, ':', meta_value) SEPARATOR '|') as meta FROM {$wpdb->prefix}postmeta WHERE post_id = '%s' LIMIT 1",
        $id
    ));
    $result = $wpdb->last_result[0];

    $meta = split_string_into_key_value_pair($result->meta, '|', ':');
    $attendee = array(
        'ID' => $result->order_id,
        'slug' => sanitize_title($meta['_shipping_company'].' '.$meta['_shipping_last_name'].' '.$meta['_shipping_first_name']),
        'last_name' => $meta['_shipping_last_name'],
        'first_name' => $meta['_shipping_first_name'],
        'company' => $meta['_shipping_company'],
        'attended' => $meta['_attended'],
        'email' => $meta['_billing_email'],
        'country' => $meta['_shipping_country'],
        'address' => $meta['_shipping_address_1'].' '.$meta['_shipping_address_2'],
        'city' => $meta['_shipping_city'],
        'state' => $meta['_shipping_state'],
        'postcode' => $meta['_shipping_postcode'],
    );

    $wpdb->flush();
    return $attendee;
}




function eg_quicktags() {
?>
<script type="text/javascript" charset="utf-8">
$ids= 

QTags.addButton( 'eg_title',  <?php echo '\''.  __('title', 'woocommerce-attendance') . '\''?> , '[title]', '', '' );
QTags.addButton( 'eg_last_name',  <?php echo '\''.  __('last_name', 'woocommerce-attendance') . '\''?> , '[last_name]', '', '' );
QTags.addButton( 'eg_fist_name',  <?php echo '\''.  __('first_name', 'woocommerce-attendance') . '\''?> , '[first_name]', '', '' );
QTags.addButton( 'eg_company',  <?php echo '\''.  __('company', 'woocommerce-attendance') . '\''?> , '[company]', '', '' );
QTags.addButton( 'eg_email',  <?php echo '\''.  __('email', 'woocommerce-attendance') . '\''?> , '[email]', '', '' );
QTags.addButton( 'eg_country',  <?php echo '\''.  __('country', 'woocommerce-attendance') . '\''?> , '[country]', '', '' );
QTags.addButton( 'eg_address',  <?php echo '\''.  __('address', 'woocommerce-attendance') . '\''?> , '[address]', '', '' );
QTags.addButton( 'eg_city',  <?php echo '\''.  __('city', 'woocommerce-attendance') . '\''?> , '[city]', '', '' );
QTags.addButton( 'eg_state',  <?php echo '\''.  __('state', 'woocommerce-attendance') . '\''?> , '[state]', '', '' );
QTags.addButton( 'eg_postcode',  <?php echo '\''.  __('postcode', 'woocommerce-attendance') . '\''?> , '[postcode]', '', '' );



</script>
<?php
}


//voor vertalingen in js

// function pw_load_scripts(){
//     wp_enqueue_script('pw-script',plugins_url( '../../assets/js/text-button.js', __FILE__ ));
// 	wp_localize_script('pw-script', 'pw_script_vars', array(
//     'title'=> __('title', 'woocommerce-attendance') ,
//     'last_name' => __('last_name', 'woocommerce-attendance'),
//     'first_name' =>  __('first_name', 'woocommerce-attendance') ,
//     'company' =>   __('company', 'woocommerce-attendance'),
//     'email' => __('email', 'woocommerce-attendance') ,
//     'country' =>  __('country', 'woocommerce-attendance'),
//     'address' =>   __('address', 'woocommerce-attendance'),
//     'city' => __('city', 'woocommerce-attendance'),
//     'state' =>  __('state', 'woocommerce-attendance') ,
//     'postcode' =>  __('postcode', 'woocommerce-attendance')
// )
// 	);
  
// }
//add_action('wp_enqueue_scripts', 'wp_load_scripts');








add_action('admin_head', 'gavickpro_add_my_tc_button');
function gavickpro_add_my_tc_button() {
    global $typenow;
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
    return;
    }
    // verify the post type
    //if( ! in_array( $typenow, array( 'post', 'page' ) ) )
    //    return;
    // check if WYSIWYG is enabled
    if ( get_user_option('rich_editing') == 'true') {
        add_filter("mce_external_plugins", "gavickpro_add_tinymce_plugin");
        add_filter('mce_buttons', 'gavickpro_register_my_tc_button');
    }
}

//voor shortcodes 3


function gavickpro_add_tinymce_plugin($plugin_array) {
    $plugin_array['gavickpro_tc_button'] = plugins_url( '../../assets/js/text-button.js', __FILE__ ); // CHANGE THE BUTTON SCRIPT HERE
    return $plugin_array;
}

function gavickpro_register_my_tc_button($buttons) {
   array_push($buttons, "gavickpro_tc_button");
   return $buttons;
}














// $strings = 'tinyMCE.addI18n(
//     {' . _WP_Editors::$mce_locale . '.extrastrings:
//         {
//             id: "' . esc_js( __('id', 'woocommerce-attendance') ) . '",
//             slug: "' . esc_js( __('slug', 'woocommerce-attendance') ) . '",
//             title: "' . esc_js( __('title', 'woocommerce-attendance') ) . '",
//             last_name: "' . esc_js( __('last_name', 'woocommerce-attendance') ) . '",
//             first_name: "' . esc_js( __('first_name', 'woocommerce-attendance') ) . '",
//             company: "' . esc_js( __('company', 'woocommerce-attendance') ) . '",
//             email: "' . esc_js( __('email', 'woocommerce-attendance') ) . '",
//             country: "' . esc_js( __('country', 'woocommerce-attendance') ) . '",
//             address: "' . esc_js( __('address', 'woocommerce-attendance') ) . '",
//             city: "' . esc_js( __('city', 'woocommerce-attendance') ) . '",
//             state: "' . esc_js( __('state', 'woocommerce-attendance') ) . '",
//             postcode: "' . esc_js( __('postcode', 'woocommerce-attendance') ) ) . '"
//         }
//     }
// )';

#endregion