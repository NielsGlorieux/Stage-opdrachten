<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Event_List extends WP_List_Table {

    /** Class constructor */
    public function __construct() {

        parent::__construct( [
            'singular' => __( 'Event', 'woocommerce-attendance' ), //singular name of the listed records
            'plural'   => __( 'Events', 'woocommerce-attendance' ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ] );
    }


    /**
     * Retrieve customers data from the database
     *
     * @return mixed
     */
    public static function get_events() {
        global $wpdb;
        $full_order_list = array();
        $wpdb->query("
          SELECT * FROM {$wpdb->prefix}posts
          WHERE post_type = 'product'
          AND post_title NOT LIKE '%lid%'
          AND post_excerpt NOT LIKE '%lid%'
          AND post_name NOT LIKE '%lid%'
          AND post_content NOT LIKE '%lid%'");
        $results = $wpdb->last_result;
        foreach ( $results as $result ) {
            $full_order_list[] = array(
                'ID' => $result->ID,
                'title' => $result->post_title,
                'attendees' => self::record_attendees_count($result->ID)
            );
            
          
        }
        
        
            
        usort($full_order_list, 'woocommerce_attendance_usort');
        $wpdb->flush();

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
        if(isset($_POST['attendeeAll']) ) {
            $event = $_POST['attendeeAll'];
           
           /*$attendees = */$wpdb->get_results($wpdb->prepare("
            SELECT post_id
            FROM {$wpdb->prefix}postmeta WHERE post_id IN (
            SELECT post_id FROM {$wpdb->prefix}postmeta
            WHERE meta_key = '_product_id'
            AND meta_value = '%s'
            )
            GROUP BY post_id",
            $event
            ));
            $result = $wpdb->last_result;
            
            $attendees = array();
            foreach($result as $attendee){
                 $attendees[] += $attendee->post_id;                
            
            }
           //echo $attendees[0];
            
            //echo $attendees->post_id;
            //echo $attendees[0]->post_id. " " .$attendees[1]->post_id;
            //$attendees = $wpdb->last_result;
            
           //echo $results[2]->post_title;
           //$attendee = $_GET[''];
           //$attendees $results => $result->ID
             woocommerce_attendance_send_email($event, $attendees, "All");
        
            }


        return $full_order_list;
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $wpdb->query("
          SELECT COUNT(ID) FROM {$wpdb->prefix}posts
          WHERE post_type = 'product'
        AND post_title NOT LIKE '%lid%'
        AND post_excerpt NOT LIKE '%lid%'
        AND post_name NOT LIKE '%lid%'
        AND post_content NOT LIKE '%lid%'");
        $results = $wpdb->last_result;
        $wpdb->flush();
        return count($results);
    }

    public static function record_attendees_count($event_id) {
        global $wpdb;

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


    /** Text displayed when no customer data is available */
    public function no_items() {
        _e( 'No events found.', 'woocommerce-attendance' );
    }


    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }


    /**
     * Method for title column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_title($item) {
        if($item['attendees'] == 0) {
            // no attendees = no link
            return sprintf(
                '%1$s',
                $item['title']
            );
        } else {
            return sprintf(
                '<a href="?page=%1$s&id=%2$s&tab=attendances&attended=All">%3$s</a>',
                $_REQUEST['page'],
                $item['ID'],
                $item['title']
            );
        }
    }
    
    //mij
     function column_actions( $item ) {
        
        $send_emailAll = __('Send Email (All template)', 'woocommerce-attendance');

        $button_send_emailAll = sprintf('<button style="color: rgba(56,45,239,1)" class="button actions send-email" name="attendeeAll" value="%1$s" title="%2$s"><span class="screen-reader-text">%2$s</span></button>',
            $item['ID'],
            $send_emailAll
        );

        return $button_send_emailAll;
    }


    /**
     * Method for attendees column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_attendees($item) {
        return sprintf(
            '%1$s',
            $item['attendees']
        );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = [
            //'cb'      => '<input type="checkbox" />',
            'title'    => __( 'Title', 'woocommerce-attendance' ),
            'attendees'    => __( 'Attendees', 'woocommerce-attendance' ),
            'actions'    => __( 'Send email', 'woocommerce-attendance' ),
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
            'title' => array( 'title', true ),
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

        $per_page     = $this->get_items_per_page( 'events_per_page', 10 );
        $current_page = $this->get_pagenum();
        $items        = self::get_events();
        $total_items  = count($items);
        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );

        $items = array_slice($items,(($current_page-1)*$per_page),$per_page);

        $this->items = $items;
    }
    
    
      function process_bulk_action() {
        //Detect when a bulk action is being triggered...
       
       if( 'send_emailAll' === $this->current_action() && isset($_POST['attendees']) ) {
            $attendees = $_POST['attendees'];
            $event = $_GET['id'];
            woocommerce_attendance_send_email($event, $attendees,"All");
        }
    }
    
    
    
    
}