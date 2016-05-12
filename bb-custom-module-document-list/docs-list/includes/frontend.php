<div class="fl-example-text">
    <?php
    global $wpdb;
    ?>
    <table class="table" style="width:100%">
        <tr>
            <th style="width:19%">Datum</th>
            <th class="absorbing-column">Titel</th>
        </tr>
    <?php
    if(count($settings->urls_field) > 0){ 
        foreach($settings->urls_field as $url){
            if(!empty($url->label)){
            ?>
                <tr>
                    <td><?php echo $url->date ?></td>
                    <td><a target="_blank" href="<?php echo $url->url ?>"><?php echo $url->label ?></a></td> 
                </tr>
                <?php
            }
        }
        
    }
   
    if(!empty($settings->custom_field_add_docs)){
            $docs = $wpdb->get_results( 
            "
            SELECT post_title, post_date, guid, post_excerpt
            FROM $wpdb->posts
            WHERE ID IN ({$settings->custom_field_add_docs})"
            );
        foreach($docs as $doc){
        ?>
        <tr>
            <?php if(!empty($doc->post_excerpt) && DateTime::createFromFormat("d/m/Y", $doc->post_excerpt )): ?>
                <td><?php echo $doc->post_excerpt ?></td> 
            <?php else: ?>
                <td><?php echo date_i18n('d/m/Y', mysql2date('U',$doc->post_date)); ?></td>
            <?php endif; ?>
                <td><a target="_blank" href="<?php echo $doc->guid ?>"><?php echo $doc->post_title ?></a></td>
        </tr>
        <?php  
        }
    }
    ?>
    </table>
</div>