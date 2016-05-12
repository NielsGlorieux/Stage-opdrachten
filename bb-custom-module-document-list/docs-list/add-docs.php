<?php

/**
 * @class FLAddDocsModule
 */
class FLAddDocsModule extends FLBuilderModule {
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Documenten toevoegen', 'fl-builder'),
            'description'   => __('A module for showing documents in a list.', 'fl-builder'),
            'category'		=> __('Advanced Modules', 'fl-builder'),
            'dir'           => FL_MODULE_DOCS_DIR . 'docs-list/',
            'url'           => FL_MODULE_DOCS_URL . 'docs-list/',
            'editor_export' => true, 
            'enabled'       => true, 
            'partial_refresh' => true
        ));
        
     
        $this->add_css('font-awesome');
        $this->add_js('jquery-bxslider');
    }     
    public function update($settings)
    { 
        return $settings;
    }
     
    public function delete()
    {
    
    }
}

FLBuilder::register_module('FLAddDocsModule', array(
    'general'       => array( 
        'title'         => __('General', 'fl-builder'), 
        'sections'      => array( 
            'general'       => array( 
                'title'         => __('Add documents to show them in a list', 'fl-builder'), 
                'fields'        => array( 
                    'custom_field_add_docs' => array(
                        'type'          => 'my-custom-field-word',
                        'label'         => __('Documents', 'fl-builder'),
                        'default'       => ''
                    ),
                    'urls_field' => array(
                    'type'          => 'form',
                    'label'         => __('URL', 'fl-builder'),
                    'form'          => 'urls_field', // ID of a registered form.
                    'preview_text'  => 'label', // ID of a field to use for the preview text.
                    'multiple'      =>  true,
                    )
                )
            )
        )
    )
   
));

FLBuilder::register_settings_form('urls_field', array(
    'title' => __('My Form Field', 'fl-builder'),
    'tabs'  => array(
        'general'      => array(
            'title'         => __('General', 'fl-builder'),
            'sections'      => array(
                'general'       => array(
                    'title'         => '',
                    'fields'        => array(
                        'label'         => array(
                            'type'          => 'text',
                            'label'         => __('Label', 'fl-builder')
                        ),
                         'url'         => array(
                            'type'          => 'text',
                            'label'         => __('URL', 'fl-builder'),
                            'default'       => 'http://'
                        ),
                          'date'         => array(
                            'type'          => 'text',
                            'label'         => __('Datum', 'fl-builder')
                        ),
                    )
                ),
            )
        )
    )
));