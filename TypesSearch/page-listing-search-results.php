<?php

/*
Template Name: Search Page
*/
get_header(); ?>
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php
        global $query_string;

        $query_args = explode("&", $query_string);
        $search_query = array();

        if( strlen($query_string) > 0 ) {
            foreach($query_args as $key => $string) {
                $query_split = explode("=", $string);
                $search_query[$query_split[0]] = urldecode($query_split[1]);
            } 
        } 

        $list = array();
        $item = array();
        $taxonomies = get_object_taxonomies((isset($_POST['post_type1'])) ? $_POST['post_type1'] : ''); 

        foreach($_POST as $key => $value){
                if(in_array($key,$taxonomies)){
                    if($value !=''){
                        $item['taxonomy'] = htmlspecialchars($key); 
                        $item['terms'] = htmlspecialchars($value);   
                        $item['field'] = 'slug';
                        $list[] = $item;     
                    }
                }
        }
        $cleanArray = array_merge(array('relation' => 'AND'), $list);

        $meta_keys = generate_meta_keys();

        $fields = array();
        $types = array();
        foreach($meta_keys as $k){
            $testMeta = $k->meta_key;
            $fields[]=  $customField = substr($testMeta, 5);
        }

        $query_array = array('relation' => 'AND');
        foreach($_POST as $key => $value){  
            if(strpos($key,'@')){
                $pieces = explode("@", $key);
                $sleutel = $pieces[0];
                $type = $pieces[1];
            }
            if(!empty($type)){
                if($type == 'checkboxes'){
                    $stukken = explode('|', $sleutel);
                    $sleutel = $stukken[1];   
                }
            }
            //checken of het een wpcf is
            if(!empty($sleutel)){
                if(in_array($sleutel, $fields)){
                    if($value !=''){
                        switch($type){
                            case 'textfield':
                            array_push($query_array, array('key' => 'wpcf-'.$sleutel, 'value' => $value, 'compare' => 'LIKE')); 
                            break;
                            case 'checkbox':
                            array_push($query_array, array('key' => 'wpcf-'.$sleutel, 'value' => $value, 'compare' => '='));
                            break;
                            case 'numeric' : 
                            if($value[0]=='')
                            $value[0]='0';
                            if($value[1]=='')
                            $value[1]='2147483647';
                            $min = intval($value[0]);
                            $max = intval($value[1]);
                            array_push($query_array, array('key' => 'wpcf-'.$sleutel, 'value' => array($min, $max), 'type' => 'NUMERIC','compare' => 'BETWEEN')); 
                            break;
                            case 'radio' : 
                            array_push($query_array, array('key' => 'wpcf-'.$sleutel, 'value' => $value, 'compare' => '=')); 
                            break;
                            case 'checkboxes' :
                            array_push($query_array, array('key' => 'wpcf-'.$sleutel, 'value' => $value, 'compare' => 'LIKE')); 
                            break;
                            case 'select' : 
                            array_push($query_array, array('key' => 'wpcf-'.$sleutel, 'value' => $value, 'compare' => '=')); 
                            break;
                            default: break;
                        }
                    }
                }
            }
            $value = null;
            $type = null;
        }

        // var_dump($query_array);
        $args1 = array(
            'post_type' =>  ((isset($_POST['post_type1'])) ? $_POST['post_type1'] :  ''), 
            'tax_query' => $cleanArray,
            'meta_query' => $query_array,
            'posts_per_page' => 3,
            'paged' => get_query_var('page')
        );

        $finalQuery = new WP_Query($args1);

        echo ($finalQuery->post_count > 0 ) ? '<h3 class="foundPosts">' . $finalQuery->post_count. ' listings found</h3>' : '<h3 class="foundPosts">We found no results</h3>';?>
        <?php 

        while ( $finalQuery->have_posts() ) : $finalQuery->the_post();?> 
            <div class='showPost'> <?php   
                get_template_part( 'template-parts/content', 'search' );
                ?> 
            </div> <?php
        endwhile; 
        wp_reset_postdata(); 
        ?>
        
        <div class="row page-navigation">
            <?php next_posts_link('&laquo; Older Entries', $wp_query->max_num_pages) ?>
            <?php previous_posts_link('Newer Entries &raquo;') ?>
        </div>

    </main><!-- .site-main -->
    <?php get_sidebar( 'content-bottom' );?>

</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>

<?php

