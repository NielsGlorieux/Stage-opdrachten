<?php
/**
 * woocommerce-attendance-listing-page.php created by Xando [Celine Gardier]
 * on 06/01/16
 */
function woocommerce_attendance_listing_page() { ?>
    </pre>
    <div class="wrap">
        <h2><?php _e('Attendances', 'woocommerce-attendance'); ?></h2>
         
    <!--mij   -->
    
    <?php
        if( isset( $_GET[ 'tab' ] ) ) {
            $active_tab = $_GET[ 'tab' ];
        } else{
            $active_tab = null;
        }
    $id = (isset($_GET['id'])) ? $_GET['id'] : 0; 
    $wc_pf = new WC_Product_Factory();
    $product = $wc_pf->get_product($id);?>
    
    <?php if(/*$_GET['page'] == 'attendance' && $_GET['tab'] == 'attendances' && */(!isset($_GET['id']) || $_GET['id'] == 0)) { ?>
    
    <h2 class="nav-tab-wrapper">
          <a href="?page=attendance&tab=attendances&id=<?php echo (isset($_GET['id'])) ? $_GET['id'] : 0;?>" class="nav-tab <?php echo $active_tab == 'attendances' ? 'nav-tab-active' : ''; ?>" class="nav-tab <?php echo  $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'attendances'; ?>" ><?php _e('Attendances', 'woocommerce-attendance');?> <?php echo (isset($product->post->post_title))?$product->post->post_title : "" ;?></a>
          <a href="?page=attendance&tab=mailsetup&id=<?php echo (isset($_GET['id'])) ? $_GET['id'] : 0;?>&attended=All" class="nav-tab <?php echo $active_tab == 'mailsetup' ? 'nav-tab-active' : ''; ?>"><?php _e('Email Template', 'woocommerce-attendance');?></a>
    </h2>
    
    <?php
        }else{
            ?>
      <h2 class="nav-tab-wrapper">
        <a href="?page=attendance&tab=attendances&id=<?php echo (isset($_GET['id'])) ? $_GET['id'] : 0;?>" class="nav-tab <?php echo $active_tab == 'attendances' ? 'nav-tab-active' : ''; ?>" class="nav-tab <?php echo  $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'attendances'; ?>" ><?php _e('Attendances', 'woocommerce-attendance');?> <?php echo (isset($product->post->post_title))?$product->post->post_title : "" ;?></a>
        <a href="?page=attendance&tab=mailsetup&id=<?php echo (isset($_GET['id'])) ? $_GET['id'] : 0;?>&attended=All" class="nav-tab <?php echo $active_tab == 'mailsetup' ? 'nav-tab-active' : ''; ?>"><?php _e('Email Template', 'woocommerce-attendance');?></a>
        <a href="?page=attendance&tab=mailsetupDID&id=<?php echo (isset($_GET['id'])) ? $_GET['id'] : 0;?>&attended=Attended" class="nav-tab <?php echo $active_tab == 'mailsetupDID' ? 'nav-tab-active' : ''; ?>"><?php _e('Email Template attended', 'woocommerce-attendance');?></a>
        <a href="?page=attendance&tab=mailsetupNOT&id=<?php echo (isset($_GET['id'])) ? $_GET['id'] : 0;?>&attended=Did+not+attend" class="nav-tab <?php echo $active_tab == 'mailsetupNOT' ? 'nav-tab-active' : ''; ?>"><?php _e('Email Template not attended', 'woocommerce-attendance');?></a>
         </h2>   
            
            <?php
        }   
         
        if($active_tab == 'attendances'){
            if($id > 0) {
                display_attendee_listing($id);
                
                }
            else{
                display_event_listing();
            }
       } 
       else{ 
            display_email_form($id);
       }    
        ?>
    </div>    
    <?php
}

global $jal_db_version;
$jal_db_version = '1.0';
global $table_name;
function jal_install(){
    global $wpdb;
    global $jal_db_version;
      
    $table_name = $wpdb-> prefix . "emailtemplates";
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
    title tinytext NOT NULL,
    message text NOT NULL,
    nameId int(10) NOT NULL,
    attended VARCHAR(15) NOT NULL,
    UNIQUE KEY id (nameId, attended)
   ) $charset_collate;";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
    add_option( 'jal_db_version', $jal_db_version );
    
     $wpdb->replace(
            $wpdb-> prefix . "emailtemplates",
            array(
                'title' => 'Welcome',
                'message' => '<p>Hello [first_name]!</p><p>This is the standard email template.</p><p>Feel free to change it!</p>',
                'nameId' => 0, 
                'attended' => 'All'
            ),
            array(
                '%s',
                '%s',
                '%d',
                '%s'
            )
        );
    
}

//mij
function display_email_form($idfrom){
     global $wpdb;

    $products = get_woocommerce_product_list();


    if(isset($_POST['submit'])) {
        $id = $idfrom;//$_POST['product'];
        $newTitle = $_POST['title'];       
        $newMessage1 = str_replace("\\", "", htmlspecialchars($_POST['message']));
        $newMessage = wpautop((str_replace(array("\\","&lt;","&gt;","&quot;","&amp;nbsp;"), array("","<",">", "\"", "&nbsp;"),$newMessage1)));
        
        //$attended = $_POST['attended'];

        $attended = $_GET['attended'];

        $wpdb->replace(
            $wpdb-> prefix . "emailtemplates",
            array(
                'title' => $newTitle,
                'message' => $newMessage,
                'nameId' => $idfrom, //$id,
                'attended' => $attended
            ),
            array(
                '%s',
                '%s',
                '%d',
                '%s'
            )
        );
        
        
    }
    
    $attendedurl = (isset($_GET['attended'])) ? $_GET['attended'] : 0;
    $oldTitle = $wpdb->get_var("SELECT title FROM wp_emailtemplates
    WHERE nameId = $idfrom
    AND attended = '$attendedurl'");

    $oldMessage = $wpdb->get_var("SELECT message FROM wp_emailtemplates
    WHERE nameId = $idfrom
    AND attended = '$attendedurl'");

    
    $options = array("All",
    "Attended",
    "Did not attend");
   
   //Voor shortcodes 1
   // add_action('media_buttons','add_sc_select',11);  
  
    
    //voor shortcodes 2
    add_action('admin_print_footer_scripts','eg_quicktags');
    
    //voor shortcodes 3
    add_action('admin_head', 'gavickpro_add_my_tc_button');

    

 ?>

<form class="mailform" method="post" action="">
   
    <h1> <?php _e('Email Template','woocommerce-attendance')?></h1>
    <?php
      $attendedurl = (isset($_GET['attended'])) ? $_GET['attended'] : 0;
    ?>   
     

     
     <h2> <?php _e('Title','woocommerce-attendance')?>:</h2>
        <input type="text" name="title" value="<?php echo $oldTitle?>">
        </br>
        <h2><?php  _e('Message','woocommerce-attendance')?>:</h2>
    
         <?php wp_editor($oldMessage, 'message',array(
        'quicktags' => true,
        'tinymce' => true,
        'textarea_rows' => 14,
        'media_buttons' => true,
        "editor_class" => true
        )); ?>
        </br>
        <input class="button" type="submit" value="<?php  _e('Save Template','woocommerce-attendance')?>" name="submit">
    
 </form>
   
   
 
   </br>
  

    <?php
  
   
}

function display_event_listing () {
    global $events_obj;
    $events_obj->prepare_items(); ?>
    <form method="post">
        <input type="hidden" name="page" value="attendance">
        <?php
            $events_obj->search_box( __('search', 'woocommerce-attendance'), 'search_id' );
            echo '<h3>&nbsp;</h3>';
            $events_obj->display();
        ?>
    </form>
    <?php
}

function display_attendee_listing ($id) {
    global $attendees_obj;
    $wc_pf = new WC_Product_Factory();
    $product = $wc_pf->get_product($id);
    ?>
    <h3>
        <span class="pull-right">
            <a class="button" href="<?php echo sprintf("?page=%s&tab=attendances", $_REQUEST['page']); ?>">
                <?php _e('Back', 'woocommerce-attendance'); ?>
            </a>
        </span>
        <?php echo $product->post->post_title; ?>
    </h3>
  <?php

    $attendees_obj->prepare_items(); ?>
    <form method="post">
        <input type="hidden" name="page" value="attendance">
        <?php
            $attendees_obj->search_box( __('search', 'woocommerce-attendance'), 'search_id' );
            $attendees_obj->show_only_buttons();
            $attendees_obj->display();
        ?>
    </form>
  <?php
}



