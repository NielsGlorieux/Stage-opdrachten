<?php
/*
Plugin Name: Types Search
Plugin URI: http://www.xando.be
Description: Creates a dynamic search function for your Type custom post types.
Version: The Plugin's Version Number, e.g.: 1.0
Author: Niels Glorieux, Xando
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/
?>
<?php
add_filter( 'page_template', 'search_page_template' );
function search_page_template( $page_template )
{
    if ( is_page( 'listing-search-results' ) ) {
        $page_template = dirname( __FILE__ ) . '/page-listing-search-results.php';
    }
    return $page_template;
}

function buildSelect($tax){
    $terms = get_terms($tax);
    $x = '<select name="'. $tax .'">';
    $x .= '<option value="">Select '. ucfirst($tax) .'</option>';
    foreach ($terms as $term) {
        $x .= '<option value="' . $term->slug . '">' . $term->name . '</option>';
    }
    $x .= '</select>';

    return $x;
} 
       
function generate_meta_keys(){
    global $wpdb;
    
    if(isset($_POST['post_type'])){
        $post_type = $_POST['post_type'];
    }else if(isset($_POST['post_type1'])){
        $post_type = $_POST['post_type1'];
    }
   
    $query = "
        SELECT $wpdb->postmeta.meta_value, $wpdb->postmeta.meta_key
        FROM $wpdb->posts 
        LEFT JOIN $wpdb->postmeta 
        ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
        WHERE $wpdb->posts.post_type = '%s' 
        AND $wpdb->postmeta.meta_key != '' 
        AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)' 
        AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'
        GROUP BY $wpdb->postmeta.meta_key
    ";
    $meta_keys = $wpdb->get_results($wpdb->prepare($query, $post_type));
   
    return $meta_keys;
}
        
class Types_Search_Widget extends WP_Widget {
     
    function __construct() {
    parent::__construct(
         
        // base ID of the widget
        'types_search_widget',
         
        // name of the widget
        __('Types Search Widget', 'xando' ),
         
        // widget options
        array (
            'description' => __( 'Advanced Search with custom fields for posts.', 'xando' )
        )       
    );
    }
     
function form( $instance ) {   
    $defaults   = array( 'num_posts' => 5 );
    echo '<p>Exclude following post type(s):' . "\n";
    echo "<ul>\n";
    $post_types = get_post_types(array('public'=> true, '_builtin' => false, 'exclude_from_search' => false ));
    
    foreach( $post_types as $post_type ) {
        echo '  <li><input name="' . $this->get_field_name( $post_type ) . '" type="checkbox"';
        if( isset( $instance[$post_type] ) && $instance[$post_type] == 'on' ) {
            echo ' checked="checked"';
        }
        echo ' /> <label>' . $post_type . "</label></li>\n";
    }
    echo "</ul>\n";
}
     
function update( $new_instance, $old_instance ) {  

    return $new_instance;
}
       
function widget( $args, $instance ) { 
    if(isset($_POST['post_type'])){
        $post_type = $_POST['post_type'];
    }else 
    if(isset($_POST['post_type1'])){
        $post_type = $_POST['post_type1'];
    }
                
    $title = 'Advanced Search';
    echo $args['before_title'] . $title . $args['after_title'];

    ?> 
        <!--OUTPUT VANAF HIER-->
        
    <form method='post' action='index.php'>
        <h4>Kies een custom post type</h4>    
        <select id='posts' name='post_type' onchange="this.form.submit();">
            <option>Selecteer een post type</option>
            <?php $post_types = get_post_types(array('public'=> true, '_builtin' => false, 'exclude_from_search' => false ));
            foreach(array_keys($instance) as $ex){
                if(($key = array_search($ex, $post_types)) !== false){
                        unset($post_types[$key]);
                } 
            }
            foreach($post_types as $type){
            ?>  <option value='<?php echo $type ?>' <?php echo( (isset($_POST['post_type'])&&($_POST['post_type']==$type) ) || (isset($_POST['post_type1'])&&($_POST['post_type1']==$type)) ?' selected="selected"':'');?>><?php echo $type ?></option>
            <?php  
            }             
            ?>
            </select>
    </form>     
    <?php  
    if(!empty($post_type)){ ?>
    <form role="search" class="search-form"  method="post" action="<?php bloginfo('url');?>/index.php/listing-search-results/">    
        <input type="hidden" value="<?php echo (isset($post_type)) ? $post_type : '' ?>" name="post_type1" /><!--type_post-->        
    <?php 
                
    $taxonomies = get_object_taxonomies($post_type);
    $meta_keys = generate_meta_keys();
    if(!empty($meta_keys)){
        foreach($meta_keys as $meta){       
            $testMeta = $meta->meta_key;
            $customField = substr($testMeta, 5);
            $fieldConfig = wpcf_admin_fields_get_field($customField);
            
            if(!empty($fieldConfig['type'])){
                $type = $fieldConfig['type'];
            
                switch($type){
                    case 'textfield': ?>               
                        <div class='content'>
                            <h4><label for="<?php echo $customField ?>"><?php echo $customField ?></label></h4>
                            <input id='<?php echo $customField ?>' type="text" name="<?php echo $customField . "@" . $type ?>" style='width:auto'>
                        </div>         
                        <?php  
                        break;
                    case 'checkbox': ?> 
                        <div class='content'>
                            <b><label for="<?php echo $customField ?>"><?php echo $customField ?>:</label></b>
                            <input id='<?php echo $customField ?>' type="checkbox" name="<?php echo $customField . "@" . $type ?>" value="<?php echo $fieldConfig['data']['set_value'] ?>">
                        </div> 
                        <?php
                        break;           
                    case 'numeric' : ?> 
                        <div class='content'>
                            <h4><label for="<?php echo $customField ?>"><?php echo $customField ?>:</label></h4>
                            <h5><label for="<?php echo $customField ?>">minimum: </label></h5>
                            <input type="number" id='<?php echo $customField ?>' name="<?php echo $customField . "@" . $type . '[]' ?>" style='width:auto'>
                            <h5><label for="<?php echo $customField ?>">maximum: </label></h5>
                            <input type="number" id='<?php echo $customField ?>' name="<?php echo $customField . "@" . $type . '[]'  ?>" style='width:auto'>
                        </div>         
                        <?php   
                        break;
                    case 'radio' : ?>
                            <div class='content'>
                            <h4><label for="<?php echo $fieldConfig['meta_key']; ?>"><b><?php echo $fieldConfig['name']; ?></b></label></h4>
                            <?php
                            if (isset($fieldConfig['data']['options'])) {
                                foreach ($fieldConfig['data']['options'] as $option => $val) {
                                    if($option != 'default'){
                                        ?>
                                        <input type="radio" name="<?php echo $customField . '@' . $type; ?>" value="<?php echo  $val['value'];?>" id="<?php echo $val['title'] ?>"><?php echo $val['title']; ?><br>
                                        <?php 
                                    }
                                }
                            }  
                            ?>  
                            </div>
                            <?php
                            break;   
                    case 'select' : ?>
                            <div class='content'>
                                <h4><label for="<?php echo $customField ?>"><?php echo $customField ?></label></h4>
                                <select name='<?php echo $customField . "@" . $type ?>'>
                                <option value="">Select <?php echo $customField ?></option>
                                <?php           
                                if (isset($fieldConfig['data']['options'])) {
                                    foreach($fieldConfig['data']['options'] as $option => $val){
                                        if($option != 'default'){
                                        ?>
                                        <option name='' value='<?php echo $val['value']; ?>'><?php echo $val['title']; ?></option>
                                        <?php
                                        }
                                    }
                                }
                                ?>
                                </select>
                            </div>
                            <?php
                    }
                }  
            }
        }                
        ?> 
            <h4>Taxonomies</h4> <?php
            
            if(!empty($taxonomies)){
                foreach($taxonomies as $tax){               
                    echo buildSelect($tax);  
                }
            }
            ?>
            </p>
            <input  value='zoek' type="submit"/>
        </form>
        <?php 
         } 
        ?> 
        <br>
        <?php 
    echo $args['after_widget'];                
    }
}
     

function tutsplus_register_list_pages_widget() {
    register_widget( 'Types_Search_Widget' );
}
add_action( 'widgets_init', 'tutsplus_register_list_pages_widget' );

function wpb_adding_scripts() {
    wp_register_style('myStyleSheet', plugins_url('/css/slider.css', __FILE__));
    wp_enqueue_style( 'myStyleSheet'); 
}
add_action( 'wp_enqueue_scripts', 'wpb_adding_scripts' );  









