<table class='table table-striped table-bordered sorted_table' style=''>
    <tr>
        <th>Title</th>
        <th>From</th>
        <th>Has been seen</th>
    </tr>
     <?php foreach($messages as $m){
       ?>
    <tr>
        <?php if($m->gelezen == false){
            ?>
            <td><b><a href='/message/<?php echo $m->id ?>'><?php echo $m->title?></a></b></td>
 <?php
        }else {?>
            <td><a href='/message/<?php echo $m->id ?>'><?php echo $m->title?></a></td>
<?php
        }
        ?>
        <td><?php
            $user = App\User::find($m->verstuurder_id);
            if(isset($user)){
                echo $user->name; 
            }else{
                echo 'deleted';
            }
        ?></td>
        <td><?php
            if($m->gelezen == true){
                echo 'yes';
            }else{
                echo 'no';
            }
            ?>
         </td>
    </tr>
    <?php } ?>
</table>

<style>
table {
    /*width: 50%;
    margin: 15px;*/
    /*float: right;*/
}

</style>