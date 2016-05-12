<?php
/**
 * Plugin Name: Beaver Builder Custom Module, Add Documents 
 * Plugin URI: http://www.xando.be
 * Description: A custom builder module that makes you add documents to a page in bulk.
 * Version: 1.0
 * Author: Xando, Niels Glorieux
 * Author URI: http://www.xando.be
 */
define( 'FL_MODULE_DOCS_DIR', plugin_dir_path( __FILE__ ) );
define( 'FL_MODULE_DOCS_URL', plugins_url( '/', __FILE__ ) );

/**
 * Custom modules
 */
function fl_load_module() {
	if ( class_exists( 'FLBuilder' ) ) {
	    require_once 'docs-list/add-docs.php';
	}
}
add_action( 'init', 'fl_load_module' );

/**
 * Custom fields
 */

function fl_documents_field ( $name, $value, $field ) { 
    if(!empty($value)){  
        $pieces = explode(',', $value);
    }
    ?>
    <div class="fl-docx-field fl-builder-custom-field<?php if(empty($value) /*|| !$file*/) echo ' fl-docx-empty'; if(isset($field['class'])) echo ' ' . $field['class']; ?>">
        <a class="fl-docx-select" href="javascript:void(0);" onclick="return false;"><?php _e('Select documents', 'fl-builder'); ?></a>
        <div class="fl-docx-preview">    
            <?php if(!empty($value)) : ?>
            <div class="fl-multiple-docs-count">
                <?php printf( _n( '<b>1</b> document selected', '<b>%d</b> documents selected', count( $pieces ), 'fl-builder' ), count( $pieces ) ); ?>
            </div>  
            <?php else: ?>
            <div class="fl-multiple-docs-count">

            </div>  
            <?php endif; ?>
            <div class="fl-clear"></div>
        </div>
        <input id='idies' name="<?php echo $name; ?>" type="hidden" value='<?php echo $value ?>' />
    </div>
<?php 
}
add_action( 'fl_builder_control_my-custom-field-word', 'fl_documents_field' , 1, 3 );

function assets_documents() {
    if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) {   	
           wp_enqueue_script( 'documentSelection', FL_MODULE_DOCS_URL . 'docs-list/js/documentSelection.js', array(), '', true ); 
           wp_enqueue_style( 'settings', FL_MODULE_DOCS_URL . 'docs-list/css/settings.css', array(), '' );
    }
}
add_action( 'wp_enqueue_scripts', 'assets_documents' );
