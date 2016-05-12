<?php
// use App\Settings;
// $theme = Settings::where('name','theme')->first();
// var_dump($theme);
// $path ='';
// if($theme->value == ''){
//     $path = 'resources/views';
// }else{
//     $path = 'themes/'. $theme->value .'/views';
// }
     
return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */
    
    'paths' => [
        
        // 
   
    //   realpath(base_path('resources/views')), 
        
        // realpath(base_path($path)),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => realpath(storage_path('framework/views')),

];
