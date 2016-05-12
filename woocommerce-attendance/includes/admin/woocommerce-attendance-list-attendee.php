<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Attendee_List extends WP_List_Table {

    /** Class constructor */
    public function __construct() {

        parent::__construct( [
            'singular' => __( 'Attendee', 'woocommerce-attendance' ), //singular name of the listed records
            'plural'   => __( 'Attendees', 'woocommerce-attendance' ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ] );
    }

    public static function get_attendees( $per_page = 5, $page_number = 1 ) {
        global $wpdb;
        $full_order_list = array();
        if ( isset($_GET['id']) && is_numeric($_GET['id']) ) {
            $event_id = $_GET['id'];

            $wpdb->query("SET SESSION group_concat_max_len = 1000000");
            $wpdb->query($wpdb->prepare("
            SELECT post_id as order_id,
            GROUP_CONCAT(CONCAT(meta_key, ':', meta_value) SEPARATOR '|') as meta
            FROM {$wpdb->prefix}postmeta WHERE post_id IN (
                SELECT post_id FROM {$wpdb->prefix}postmeta
                WHERE meta_key = '_product_id'
                AND meta_value = '%s'
            )
            GROUP BY post_id",
                $event_id
            ));
            $results = $wpdb->last_result;


            foreach ( $results as $result ) {
                $meta = split_string_into_key_value_pair($result->meta, '|', ':');
                $full_order_list[] = array(
                    'ID' => $result->order_id,
                    'last_name' => $meta['_shipping_last_name'],
                    'first_name' => $meta['_shipping_first_name'],
                    'company' => $meta['_shipping_company'],
                    'attended' => $meta['_attended'],
                    'email' => $meta['_billing_email'],
                    'country' => $meta['_shipping_country'],
                    'address' => $meta['_shipping_address_1'] . ' ' . $meta['_shipping_address_2'],
                    'city' => $meta['_shipping_city'],
                    'state' => $meta['_shipping_state'],
                    'postcode' => $meta['_shipping_postcode'],
                );
            }
            usort($full_order_list, 'woocommerce_attendance_usort');
        }
        $wpdb->flush();

        if(isset($_POST['show_only'])) {
            $show_only = $_POST['show_only'];
            return array_filter($full_order_list, function ($el) use ($show_only) {
                return ($el['attended'] == $show_only) ? true: false;
            });
        }
        if(isset($_POST['attendee']) ) {
            $attendee = $_POST['attendee'];
            $event = $_GET['id'];
            woocommerce_attendance_send_email($event, array($attendee), "Appart");
        }
        

        if(isset($_POST['s']) && strlen($_POST['s']) > 0) {
            $search = strtolower($_POST['s']);

            return array_filter($full_order_list, function ($el) use ($search) {
                $stay = false;
                foreach ( $el as $element ) {
                    if ( strpos(strtolower($element), $search) !== false ) {
                        $stay = true;
                    }
                }
                return $stay;
            });
        }

        return $full_order_list;
    }

    public static function record_count() {
        global $wpdb;

        if ( isset($_GET['id']) && is_numeric($_GET['id']) ) {
            $event_id = $_GET['id'];

            $wpdb->query($wpdb->prepare("
            SELECT COUNT(post_id)
            FROM {$wpdb->prefix}postmeta WHERE post_id IN (
                SELECT post_id FROM {$wpdb->prefix}postmeta
                WHERE meta_key = '_product_id'
                AND meta_value = '%s'
            )
            GROUP BY post_id",
                $event_id
            ));
            $results = $wpdb->last_result;
            $wpdb->flush();
            return count($results);
        }
        $wpdb->flush();
        return 0;
    }


    /** Text displayed when no customer data is available */
    public function no_items() {
        _e( 'No attendees found.', 'woocommerce-attendance' );
    }
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'last_name':
            case 'first_name':
            case 'company':
                return $item[ $column_name ];
            default:
                return $item[ $column_name ];
                //return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the actions buttons
     *
     * @param array $item
     *
     * @return string
     */
    function column_actions( $item ) {
        $no = __('Did not attend', 'woocommerce-attendance');
        $yes = __('Attended', 'woocommerce-attendance');
        $print_nametag = __('Print Name Tag', 'woocommerce-attendance');
        $print_certificate = __('Print Certificate', 'woocommerce-attendance');
        $send_email = __('Send Email', 'woocommerce-attendance');
        //mij
       // $send_emailAll = __('Send Email (All template)', 'woocommerce-attendance');

        if($item['attended'] == 'no') {
            $set_attend = 'yes';
        } else {
            $set_attend = 'no';
        }
        $button_attend = sprintf('<a class="button actions %1$s" href="?page=%2$s&id=%3$s&action=attend&attend=%1$s&guest=%4$s" title="%5$s"><span class="screen-reader-text">%5$s</span></a>',
            $set_attend,
            $_REQUEST['page'],
            $_GET['id'],
            $item['ID'],
            __('Set to:', 'woocommerce-attendance').' '.$$set_attend
        );

        $button_print_nametag = sprintf('<a class="button actions print-tag" target="_blank" href="?page=%1$s&id=%2$s&action=print-tag&guest=%3$s" title="%4$s"><span class="screen-reader-text">%4$s</span></a>',
            $_REQUEST['page'],
            $_GET['id'],
            $item['ID'],
            $print_nametag
        );

        $button_print_certificate = sprintf('<a class="button actions print-cert" target="_blank" href="?page=%1$s&id=%2$s&action=print-certificate&guest=%3$s" title="%4$s"><span class="screen-reader-text">%4$s</span></a>',
            $_REQUEST['page'],
            $_GET['id'],
            $item['ID'],
            $print_certificate
        );

        $button_send_email = sprintf('<button class="button actions send-email" name="attendee" value="%1$s" title="%2$s"><span class="screen-reader-text">%2$s</span></button>',
            $item['ID'],
            $send_email
        );
        // $button_send_emailAll = sprintf('<button style="color: rgba(56,45,239,1)" class="button actions send-email" name="attendeeAll" value="%1$s" title="%2$s"><span class="screen-reader-text">%2$s</span></button>',
        //     $item['ID'],
        //     $send_emailAll
        // );

        return $button_attend.$button_print_nametag.$button_print_certificate.$button_send_email/*.$button_send_emailAll*/;
    }


    /**
     * Method for last_name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_last_name($item) {
        $actions = array(
        );

        return sprintf(
            '%1$s %2$s',
            $item['last_name'],
            $this->row_actions($actions)
        );
    }

    function column_attended($item) {
        $attended = $item['attended'];
        $no = __('Did not attend', 'woocommerce-attendance');
        $yes = __('Attended', 'woocommerce-attendance');
        if($attended == 'yes') {
            //don't attend anymore
            $txt = $yes;
        } else {
            //attend
            $txt = $no;
        }

        return sprintf('<span class="dashicons dashicons-%s" title="%s"></span><span class="screen-reader-text">%s</span>', $attended, $txt, $txt );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = [
            'cb'      => '<input type="checkbox" />',
            'attended'    => '',
            'last_name'    => __( 'Last Name', 'woocommerce-attendance' ),
            'first_name' => __( 'First Name', 'woocommerce-attendance' ),
            'company'    => __( 'Company', 'woocommerce-attendance' ),
            'email' => __( 'Email', 'woocommerce-attendance' ),
            'country' => __( 'Country', 'woocommerce-attendance' ),
            'address' => __( 'Address', 'woocommerce-attendance' ),
            'city' => __( 'City', 'woocommerce-attendance' ),
            'state' => __( 'State', 'woocommerce-attendance' ),
            'postcode' => __( 'Postcode', 'woocommerce-attendance' ),
            'actions'    => __( 'Actions', 'woocommerce-attendance' ),
        ];

        return $columns;
    }


    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'last_name' => array( 'last_name', true ),
            'first_name' => array( 'first_name', false ),
            'company' => array( 'company', false ),
            'email' => array( 'email', false ),
            'country' => array( 'country', false ),
            'address' => array( 'address', false ),
            'city' => array( 'city', false ),
            'state' => array( 'state', false ),
            'postcode' => array( 'postcode', false ),
            'attended' => array( 'attended', false ),
        );

        return $sortable_columns;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'attendees_per_page', 10 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();
        $items        = self::get_attendees( $per_page, $current_page );
        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );

        $items = array_slice($items,(($current_page-1)*$per_page),$per_page);

        $this->items = $items;
    }


    function get_bulk_actions() {
        $actions = array(
            'print_nametags'    => __('Print Name Tag', 'woocommerce-attendance'),
            'print_certificates'    => __('Print Certificate', 'woocommerce-attendance'),
            'send_email'    => __('Send Email', 'woocommerce-attendance'),
            'send_emailAll'    => __('Send Email (All Template)', 'woocommerce-attendance')
        );
        return $actions;
    }

    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="attendees[]" value="%s" />', $item['ID']
        );
    }

    function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        if( 'print_nametags' === $this->current_action() && isset($_POST['attendees']) ) {
            $attendees = $_POST['attendees'];

            $url = admin_url(sprintf('admin.php?page=%s&id=%s&action=print-tags&guests=%s',
                $_REQUEST['page'],
                $_GET['id'],
                implode('-', $attendees)
            ), 'http');
            ?>
            <script>
                window.open('<?php echo $url; ?>');
            </script>
            <?php
        }
        elseif( 'print_certificates' === $this->current_action() && isset($_POST['attendees']) ) {
            $attendees = $_POST['attendees'];

            $url = admin_url(sprintf('admin.php?page=%s&id=%s&action=print-certificates&guests=%s',
                $_REQUEST['page'],
                $_GET['id'],
                implode('-', $attendees)
            ), 'http');
            ?>
            <script>
                window.open('<?php echo $url; ?>');
            </script>
            <?php
        }
        elseif( 'send_email' === $this->current_action() && isset($_POST['attendees']) ) {
            $attendees = $_POST['attendees'];
            $event = $_GET['id'];
            woocommerce_attendance_send_email($event, $attendees,"Appart");
        }
        elseif( 'send_emailAll' === $this->current_action() && isset($_POST['attendees']) ) {
            $attendees = $_POST['attendees'];
            $event = $_GET['id'];
            woocommerce_attendance_send_email($event, $attendees,"Standaard");
        }
    }

    public function show_only_buttons( ) {
        if ( !isset($_POST['show_only']) && !$this->has_items() )
            return;
        ?>
        <p><?php _e('Show only:', 'woocommerce-attendance'); ?>
            <button type="submit" value="yes" name="show_only" class="button">
                <span class="dashicons-before dashicons-yes"><?php _e('Attended', 'woocommerce-attendance'); ?></span>
            </button>
            <button type="submit" value="no" name="show_only" class="button">
                <span class="dashicons-before dashicons-no"><?php _e('Did not attend', 'woocommerce-attendance'); ?></span>
            </button>
            <button type="submit" class="button">
                <?php _e('All', 'woocommerce-attendance'); ?>
            </button>
        </p>
        <?php
    }
}