<?php
/*
Plugin Name: xando-faq
Plugin URI: http://xando.be
Version: 0.1
Author: Xando
Description: Makes moderating a FAQ easy.
*/

function xando_faq_setup_post_types() {

	$faq_labels =  apply_filters( 'xando_faq_labels', array(
		'name'                => 'FAQ',
		'singular_name'       => 'FAQ',
		'add_new'             => __('Add New Question', 'xando_faq'),
		'add_new_item'        => __('Add New Question', 'xando_faq'),
		'edit_item'           => __('Edit Question', 'xando_faq'),
		'new_item'            => __('New Question', 'xando_faq'),
		'all_items'           => __('All Questions', 'xando_faq'),
		'view_item'           => __('View Questions', 'xando_faq'),
		'search_items'        => __('Search Questions', 'xando_faq'),
		'not_found'           => __('No Questions found', 'xando_faq'),
		'not_found_in_trash'  => __('No Questions found in Trash', 'xando_faq'),
		'parent_item_colon'   => '',
		'menu_name'           => __('Xando FAQ', 'xando_faq'),
		'exclude_from_search' => true
	) );


	$faq_args = array(
		'labels' 			=> $faq_labels,
		'public' 			=> true,
		'publicly_queryable'=> true,
		'show_ui' 			=> true,
		'show_in_menu' 		=> true,
		'query_var' 		=> true,
		'capability_type' 	=> 'post',
		'has_archive' 		=> false,
		'hierarchical' 		=> false,
        'rewrite' => array('slug' => 'faq_slug'),
		'supports' 			=> apply_filters('xando_faq_supports', array( 'title', 'editor' ) ),
	);
	register_post_type( 'xando_faq', apply_filters( 'xando_faq_setup_post_types', $faq_args ) );

}

add_action('init', 'xando_faq_setup_post_types');


add_action( 'admin_menu', 'xando_faq_add_admin_menu' );
add_action( 'admin_init', 'xando_faq_settings_init' );


function xando_faq_add_admin_menu(  ) { 
    add_submenu_page('edit.php?post_type=xando_faq', 'xando-faq', 'Settings and Help', 'manage_options', basename(__FILE__), 'xando_faq_options_page');
}


function xando_faq_settings_init(  ) { 

	register_setting( 'pluginPage', 'xando_faq_settings' );

	add_settings_section(
		'xando_faq_pluginPage_section', 
		__( 'Questions', 'wordpress' ), 
		'xando_faq_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'xando_faq_questions_allowed_0', 
		__( 'Enable users to ask questions', 'wordpress' ), 
		'xando_faq_questions_allowed_0_render', 
		'pluginPage', 
		'xando_faq_pluginPage_section' 
	);


}


function xando_faq_questions_allowed_0_render(  ) { 
    $options = get_option( 'xando_faq_settings' );
	?>
	<input type='checkbox' name='xando_faq_settings[xando_faq_questions_allowed_0]' value='1' <?php checked( isset( $options['xando_faq_questions_allowed_0'] ) ); ?>>
    <?php
}


function xando_faq_settings_section_callback(  ) { 

	echo __( 'Do you want to enable users to ask questions?', 'wordpress' );

}


function xando_faq_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h1>Settings</h1>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
	</form>
    <h1>Help</h1>
  <h2>How to change the looks of the question accordion.</h2>
        <p>To change the layout of the questions, find your theme's css folder.</p> 
        <!--<p>Add the items listed underneath like so: details {background : red;}</p>-->
  	    
        <h3>Css explanation</h3>
        <h4>details</h4>
        <p>In details you can change the background color and the border radius of the answers.</p>
        <h4>details summary</h4>
        <p>In this part you can change the looks of the question section.</p>
        <h4>details[open] summary</h4>
        <p>Here you can change stylings for the question section when its opened up.</p>
        <h4>details summary::-webkit-details-marker</h4>
        <p>By setting display to none, the default marker gets hidden.</p>
        <h4>details summary:before</h4>
        <p>In this section you can change the icon on the left of the question.
            You can change the image, make it bigger and remove it.<p>
        <h4>details[open] summary:before</h4>
        <p>In this part you can set the image next to the question when its opened.
        <h4>details.leeg summary</h4>       
        <p>When the question is unanswered, the question will have another color. You can change that color here.        
        <h4>details[open].leeg summary</h4>
        <p>Here you can change fontstyles for unanswered questions.
    <?php

}

//Help sectie nog naar page!
// function my_contextual_help( $contextual_help, $screen_id, $screen ) { 
//   //  if ( 'edit-xando_faq' == $screen->id ) {

//         $contextual_help = '<h2>How to change the looks of the question accordion.</h2>
//         <p>To change the layout of the questions, find the XANDO-FAQ folder, go to css and find the faq.css file.</p> 
//         <p>In this file you can play with the colors, font types and so on.</p>';

//     // } elseif ( 'xando_faq' == $screen->id ) {

//     //     $contextual_help = '<h2>Editing products</h2>
//     //     <p>This page allows you to view/modify product details. Please make sure to fill out the available boxes with the appropriate details (product image, price, brand) and <strong>not</strong> add these details to the product description.</p>';

//    // }
//     return $contextual_help;
// }
// add_action( 'contextual_help', 'my_contextual_help', 10, 3 );







function xando_faq_shortcode( $atts, $content = null ) {
	$faq='';
	extract(shortcode_atts(array(
		"limit" => ''
	), $atts));
	
	// Define limit
	if( $limit ) { 
		$posts_per_page = $limit; 
	} else {
		$posts_per_page = '-1';
	}
	
	$post_type 		= 'xando_faq';
	$orderby 		= 'menu_order';
	$order 			= 'ASC';
	
    $taxonomy_objects = get_terms('question_cats');	
      ?><h2 class='widget-title'>FAQ</h2><?php
    foreach($taxonomy_objects as $cat) {
        
        ?><h3><?php echo $cat->name;?></h3><?php
        
        $query = new WP_Query( array ( 
								'post_type'      => $post_type,
								'posts_per_page' => $posts_per_page,
								'orderby'        => $orderby, 
								'order'          => $order,
								'no_found_rows'  => 1,
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => $cat->taxonomy,
                                        'field' => 'term_id',
                                        'terms' => $cat->term_id,
                                        )
                                    )
								) 
						);
	?><p><?php
 
	$post_count = $query->post_count;
	// $i = 1;
    // wp_register_style( 'namespace', plugins_url('css/faq.css', __FILE__ ));
    // wp_enqueue_style('namespace'); 
     
	if( $post_count > 0) :
  
    // Loop
	while ($query->have_posts()) : $query->the_post();
        
     
        if(empty(get_the_content())){
              if(current_user_can('edit_posts')){
               ?>
               
                <details class="leeg" onclick="closeAll('this');">
                    <summary><?php echo the_title();?></summary>
                    <div><i>This question has yet to be answered.</i>
                        <a href=<?php echo get_edit_post_link();?>>edit</a>
                    </div>
                </details>
                 
              <?php }
        }
        else{
                ?>
                <details onclick="closeAll('this');">
                    <summary><?php echo the_title();?></summary>
                    <div><?php echo apply_filters('the_content', get_the_content()); 
                        if(current_user_can('edit_posts')){ 
                        ?>
                        <a href=<?php echo get_edit_post_link();?>>edit</a>
                        <?php }?>
                    </div>
                </details>
                    
                <?php
            }      
		?>      
        <?php
    endwhile;
    endif;
    
    ?><p> <?php
    wp_reset_query();
    }//einde foreach
   
    $options = get_option( 'xando_faq_settings' );
    //checked(isset( $options['xando_faq_questions_allowed_0']));
    if(isset($options['xando_faq_questions_allowed_0']) == 1){
    ?>    
    <form method="post" name="front_end" action=""><!--MAILTO:<?php// echo $admin_email;?>" enctype="text/plain"-->
        <input type="submit" style="float: right" value='submit'></input>
        <div style="overflow: hidden;">
        <input type="text" name="title" placeholder='Ask a question.' />
        </div>
        <input type="hidden" name="action" value="xando_faq" />
    </form>
    <br>
    <?php } else {
        ?>
        <p><i>Asking Questions has been disabled<?php echo (current_user_can('edit_posts')) ? ', go to the settings section to change this':''; ?><i><p>
        <?php
    }  ?>
    <script>
    function closeAll(index){
        var len = document.getElementsByTagName("details").length;
        for(var i=0; i<len; i++){
            if(i != index){
                document.getElementsByTagName("details")[i].removeAttribute("open");
            }
        }
    }
    </script>   
	<?php 
    $queryAll = new WP_Query( array ( 
                        'post_type'      => $post_type,
                        'posts_per_page' => $posts_per_page,
                        'orderby'        => $orderby, 
                        'order'          => $order,
                        'no_found_rows'  => 1,
                        
                        ) 
                );
    
    $posts = $queryAll->get_posts();
	$postTitles = array();
    
    foreach($posts as $k){
        $postTitles[] = $k->post_title;
    }
    
    if(isset($_POST['title']) && !in_array($_POST['title'], $postTitles)){
        echo addNewQuestion();
        $admin_email = get_option('admin_email');
        $subject = 'A new question has been added!';
        $message = 'The new question: ' . $_POST['title'];
        wp_mail($admin_email, $subject, $message);
        return;
    }
}

add_shortcode("xando_faq", "xando_faq_shortcode");

//FRONTEND POST ADD
function addNewQuestion(){
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == "xando_faq") {

    $title = $_POST['title'];
    $post_type = 'xando_faq';
   
    $new_post = array(
    'post_title'    => $title,
    'post_status'   => 'publish',          
    'post_type'     => $post_type 
    );
    $pid = wp_insert_post($new_post);
}
}   
function categorie() {

	$labels = array(
		'name'                       => _x( 'Categories', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Categories', 'text_domain' ),
		'all_items'                  => __( 'All categories', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New categorie name', 'text_domain' ),
		'add_new_item'               => __( 'Add a new category', 'text_domain' ),
		'edit_item'                  => __( 'Edit category', 'text_domain' ),
		'update_item'                => __( 'Update category', 'text_domain' ),
		'view_item'                  => __( 'View category', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove categories', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular categories', 'text_domain' ),
		'search_items'               => __( 'Search categories', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No categories', 'text_domain' ),
		'items_list'                 => __( 'Category list', 'text_domain' ),
		'items_list_navigation'      => __( 'Category list navigation', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'question_cats', array( 'xando_faq' ), $args );
    wp_insert_term('Andere', 'question_cats');
}
add_action( 'init', 'categorie', 0 );

function save_post_xando_faq( $post_id ) {
    $current_post = get_post( $post_id );

    // This makes sure the taxonomy is only set when a new post is created
    if ( $current_post->post_date == $current_post->post_modified ) {
    wp_set_object_terms( $post_id, 'Andere', 'question_cats', true );
    }
}
add_action( 'save_post_xando_faq', 'save_post_xando_faq' );


//CSS
function xando_faq_css() {
    //if( current_user_can( 'level_5' ) ){
        wp_register_style( 'namespace', plugins_url('css/faq.css', __FILE__ ));
        wp_enqueue_style('namespace'); 
   // }
}
add_action( 'admin_enqueue_scripts', 'xando_faq_css' );
add_action( 'wp_enqueue_scripts', 'xando_faq_css' );




// add_action('admin_menu', 'wpdocs_register_my_custom_submenu_page');
 
// function wpdocs_register_my_custom_submenu_page() {
       
        
//     add_submenu_page(
//         'faq_slug', 
//         'xando-faq', 
//         'Help', 
//         'manage_options', 
//         basename(__FILE__), 
//         'help_submenu_page_callback');
// }
 
// function help_submenu_page_callback() {
//     echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
//         echo '<h2>My Custom Submenu Page</h2>';
//     echo '</div>';
// }

//WIDGET
class Xando_FAQ_Widget extends WP_Widget {
    public function __construct() {
    parent::__construct(
        'xando_faq_widget',
        'FAQ Widget',
        array( 'description' => 'A Widget for displaying a FAQ' ) 
    );
}

    public function widget( $args, $instance ) {
      
    ?>
    <div class='faq-widget'>
        <?php echo do_shortcode('[xando_faq limit="-1"]'); ?>
    </div>
    <?php    
  
    }

     public function form( $instance ) {
        // outputs the options form on admin
    }

    public function update( $new_instance, $old_instance ) {
        // processes widget options to be saved
    }
}
function faq_widgets_init(){
    register_widget( 'Xando_FAQ_Widget' );
}
add_action( 'widgets_init', 'faq_widgets_init' );