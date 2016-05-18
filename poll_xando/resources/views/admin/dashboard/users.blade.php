@extends('layouts.dashboard')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Users</div>
                <div class="panel-body">  
                <!--user toevoegen (met ajax, zie onderaan)-->
                <!--button die modal voor user toevoegen toont-->
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Add user</button>
                </div>
                <!--het modal om een user toe te voegen-->
                <div id="myModal" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Add a new user</h4>
                        </div>
                        <div class="modal-body">    
                               <div id='errors' class="alert alert-danger">
                                    <ul>
                                    </ul>
                                </div>    
                            <?php 
                            echo Form::open(array('action' => array('AdminController@addUser')));
                            echo Form::label('', 'Name:');?><br><?php
                            echo Form::text('name', null, array('class'=>'form-control'));
                            echo Form::label('', 'Email:');?><br><?php
                            echo Form::text('email', null, array('class'=>'form-control'));
                            echo Form::label('', 'Roles:');?><br><?php
                            foreach($roles as $role){
                                echo Form::checkbox('role[]', $role, null, array('id'=>'roleAdd'));  
                                echo Form::label('', $role);?><br><?php
                            }
                            echo Form::label('', 'Password:');?><br><?php
                            echo Form::password('password', array('class'=>'form-control'));
                            echo Form::label('', 'password confirmation:');
                            echo Form::password('password_confirmation',array('class'=>'form-control')); 
                            ?>
                        </div>
                        <div class="modal-footer">
                             <?php echo Form::submit('Add user', array('id'=>'add','class'=>'btn btn-default'));
                            echo Form::close(); ?>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                        </div>
                    </div>
                </div>
                <!--user zoeken-->
                <div id='searchUser'>
                        <?php
                        echo Form::open(array('action' => array('AdminController@searchUser')));
                        echo Form::text('term', null, array('class'=>'form-control', 'placeholder'=> 'search a user..', 'style'=>'width: 250px;'));
                        echo Form::close();
                        ?>
                        <a class='' href='/admin/users'>All results</a>

                </div> 
                
                <!--de tabel die alle users toont-->
                <table id='userTable' class="table table-striped table-bordered sorted_table">
                    <thead>
                        <tr>
                            <th>name</th>
                            <th>role</th>
                            <th>email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            foreach($users as $user){
                                ?>
                                <tr>
                                    <!--de naam-->
                                    <td id='name<?php echo $user->id?>'><?php echo $user->name?></td>
                                    <!--de rol-->
                                    <td id='role<?php echo $user->id?>'><?php 
                                    foreach($user->roles()->get() as $role){
                                       echo $role->name . ' ';
                                    }
                                    ?>
                                    <!--email-->
                                    </td>
                                    <td id='email<?php echo $user->id?>'>
                                       <?php echo $user->email;?> 
                                    </td>
                                    <!--blocken, verwijderen, editten, bekijken-->
                                    <td id='actions<?php echo $user->id?>'> 
                                      <?php actions($user, $roles); ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>           
                </table>
                <!--paginatie-->
                <div class="text-center">
                    <nav id='pag'>
                        <?php echo $users->links() ?>                       
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function actions($user, $roles){ 
    ?>
    <!--user blocken-->
    <?php 
    echo Form::open(array('action' => array('AdminController@blockUser')));
    echo Form::hidden('user_id',$user->id);
    echo Form::label('', 'Block user');
    if($user->blocked==true){
        echo Form::checkbox('disable', 'Yes',true,['onChange'=>'this.form.submit()']);
    }else{
        echo Form::checkbox('disable', 'Yes',null,['onChange'=>'this.form.submit()']);
    }
    echo Form::close();        
    ?>
    <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" id="remove_user" data-toggle="modal" data-target="#deleteModal<?php echo $user->id?>"  class="btn btn-danger btn-sm">
            <span class="glyphicon glyphicon-trash"></span>
        </button>
        <button type="button" id="edit_user<?php echo $user->id?>" data-target="#editModal<?php echo $user->id?>"  class="btn btn-warning btn-sm">
            <span class="glyphicon glyphicon-edit"></span>
        </button>
        <a href='/u/<?php echo $user->id ?>' type="button" class="btn btn-primary btn-sm">
            <span class="glyphicon glyphicon-eye-open"></span>
        </a>
    </div>
    
    <!--voor verwijderen-->
    <?php 
    echo Form::open(array('action' => array('AdminController@deleteUser'),'id'=>'form-delete'. $user->id.''));
    echo Form::hidden('user_id',$user->id);
    ?>
    <div id="deleteModal<?php echo $user->id?>" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Are you sure?</h4>
                </div>
                <div class="modal-body">
                    <p>Do you want to delete user <b><?php echo $user->name ?></b>?</p>
                </div>
                <div class="modal-footer">
                    <button type='submit' id="delete-btn<?php echo $user->id?>" class="btn btn-danger odom-submit" data-dismiss="modal">Delete</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <?php
    echo Form::close();    
    ?>   
    <script>
        $('#delete-btn<?php echo $user->id?>').on('click', function(e){
            var $form=$('#form-delete<?php echo $user->id?>');  
            $form.submit();
        });
    </script>

    <!--voor editten-->
    <?php 
    echo Form::open(array('action' => array('AdminController@editUser'),'id'=>'form-edit'. $user->id.''));
    echo Form::hidden('user_id'. $user->id,$user->id);
    ?>                       
    <div id="editModal<?php echo $user->id?>" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit user <b><?php echo $user->name ?></b></h4>
                </div>
                <div class="modal-body">
                    <div id='editErrors<?php echo $user->id?>' class="alert alert-danger">
                        <ul>
                        </ul>
                    </div>   
                    <?php
                    echo Form::label('', 'Name:');?><br><?php
                    echo Form::text('name'. $user->id, $user->name, array('class'=>'form-control'));
                    echo Form::label('', 'Email:');?><br><?php
                    echo Form::text('email'. $user->id, $user->email, array('class'=>'form-control'));
                    echo Form::label('', 'Roles:');?><br><?php
                    $huidigeRoles = $user->roles;
                    $rols = array();
                    foreach($huidigeRoles as $rol){
                        array_push($rols, $rol->name);
                    }
                    foreach($roles as $role){
                        if(in_array($role,$rols)){
                            echo Form::checkbox('role[]', $role, true, array('id'=>'roleEdit'. $user->id .''));  
                            echo Form::label('', $role);?><br><?php
                        }else{
                            echo Form::checkbox('role[]', $role, null, array('id'=>'roleEdit'. $user->id .''));  
                            echo Form::label('', $role);?><br><?php
                        }
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <?php echo Form::submit('Edit user', array('id'=>'edit-btn'.$user->id,'class'=>'btn btn-warning'));
                    echo Form::close(); ?>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--editten gebeurd met ajax-->
    <script>
        $('#editErrors<?php echo $user->id?>').hide();
        $('#edit_user<?php echo $user->id?>').on('click',function( event ) {
            $('#editModal<?php echo $user->id?>').modal('show');
        });
        $('#edit-btn<?php echo $user->id?>').on('click',function( event ) {
            event.preventDefault();      
            var data = new Array();
            $('#roleEdit<?php echo $user->id ?>:checked').each(function(){
                data.push($(this).val());
            });    
            $.ajax({
                url: '/admin/users/edit',
                type: "post",
                data: {'name':$('input[name=name'+ <?php echo $user->id?> +']').val(), 
                        'email':$('input[name=email'+ <?php echo $user->id?> +']').val(),
                        'roles':data,
                        '_token': $('input[name=_token]').val(),
                        'user_id':$('input[name=user_id'+ <?php echo $user->id?> +']').val()
                    },
                success:function() {
                    $('#editErrors<?php echo $user->id?>').hide();
                    $('#editModal<?php echo $user->id?>').modal('hide'); 
                    $('#name<?php echo $user->id?>').html($('input[name=name'+ <?php echo $user->id?> +']').val());
                    var newHTML ='';
                    $.each(data, function(index, value) {
                        newHTML += value + ' ';
                    });     
                    $('#role<?php echo $user->id?>').html(newHTML);    
                    $('#email<?php echo $user->id?>').html($('input[name=email'+ <?php echo $user->id?> +']').val());
                },
                error:function(message){     
                    var data = message.responseJSON;        
                    $('#editErrors<?php echo $user->id?>').show();
                    $('#editErrors<?php echo $user->id?> ul').empty();
                    for(var k in data) {     
                        $('#editErrors<?php echo $user->id?> ul').append('<li>'+data[k]+'</li>');         
                    }  
                }      
            });
        });
    </script>
<?php
}
?>
@endsection
@section('page-script')
<script type="text/javascript">   
$('#errors').hide();
// user add met ajax
$('#add').on('click',function( event ) {
    event.preventDefault();    
    
    var data = new Array();
    $('#roleAdd:checked').each(function(){
        data.push($(this).val());
    });   
    console.log(data);       
    $.ajax({
        url: '/admin/users/create',
        type: "post",
        data: {'name':$('input[name=name]').val(), 
                'email':$('input[name=email]').val(),
                'password':$('input[name=password]').val(),
                'password_confirmation':$('input[name=password_confirmation]').val(),
                // 'role':$('#roleDelete option:selected').val(),
                'roles':data,
                '_token': $('input[name=_token]').val()
            },
        success:function(data) {
            console.log(data);
            $('#errors').hide();
            $('#myModal').modal('hide'); 
            var name = $('input[name=name]').val(); 
            var email = $('input[name=email]').val();
            var role= $('#roleDelete option:selected').text();
            $('#userTable tr:last').after('<tr><td>'+name+'</td><td>'+role+'</td><td>'+email+'</td><td></td></tr>');//<a href="/u/'+name+'">View profile</a>
              
        },
        error:function(message){     
            var data = message.responseJSON;
            $('#errors').show();
            $('#errors ul').empty();
            for(var k in data) {               
                $('#errors ul').append('<li>'+data[k]+'</li>');         
            }  
        }      
    });
});
</script>
@endsection
