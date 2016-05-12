<?php
$dir = WP_PLUGIN_DIR.'/woocommerce-attendance/includes/admin/';

require_once( $dir.'../dompdf/dompdf_config.inc.php' );
require_once( $dir.'woocommerce-attendance-functions.php' );
require_once($dir . 'woocommerce-attendance-list-event.php');
require_once($dir . 'woocommerce-attendance-list-attendee.php');
require_once( $dir.'woocommerce-attendance-listing-page.php' );

