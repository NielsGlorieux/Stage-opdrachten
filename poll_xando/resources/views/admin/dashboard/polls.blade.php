@extends('layouts.dashboard')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Polls</div>
                <div>
                    <!-- Navigatie van tabs -->
                    <ul class="nav nav-tabs" id='myTab' role="tablist">
                        <li role="presentation" class="active"><a href="#polltab" aria-controls="home" role="tab" data-toggle="tab">Poll</a></li>
                        <li role="presentation"><a href="#categorytab" aria-controls="profile" role="tab" data-toggle="tab">Categories</a></li>
                        <li role="presentation"><a href="#settingstab" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <!--Poll tab-->
                        <div role="tabpanel" class="tab-pane active" id="polltab">   
                            <!--deze button activeert de modal op een poll te creeeren-->
                            <div class="panel-body"> 
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createPoll">Create a poll</button>
                            </div>
                             <!--poll zoeken-->
                            <div id='searchPoll'>
                                    <?php
                                    echo Form::open(array('action' => array('AdminController@searchPoll')));
                                    echo Form::text('term', null, array('class'=>'form-control', 'placeholder'=> 'search a poll..', 'style'=>'width: 250px;'));
                                    echo Form::close();
                                    ?>
                                    
                                    <a class='' href='/admin/polls'>All results</a>
                            </div> 
                            <div class="panel-body">
                                <div id="createPoll" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Create a new poll</h4>
                                            </div>
                                        <div class="modal-body">
                                            <?php
                                            // poll creatie
                                            echo Form::open(array('action' => array('PollController@createPoll')));
                                            echo Form::label('', 'Name:');?><br><?php
                                            echo Form::text('name','',array('class'=>'form-control'));?><br><?php
                                            echo Form::label('', 'Choose a category:');?><br><?php
                                            ?>
                                            <select id='drop' class='form-control' name='cat'>
                                                <option disabled selected value> -- select a category -- </option>
                                                <?php 
                                                foreach($categories as $category){  
                                                    ?>
                                                    <option value='<?php echo $category->id ?>'><?php echo $category->name ?></option> 
                                                    <?php 
                                                }
                                                ?>
                                            </select>
                                            <br>
                                            <?php 
                                            echo Form::label('', 'Give the options:');?><br><?php
                                            ?>                    
                                            <ul id='options' name='options'>
                                                <li>
                                                    <div class='form-group'>
                                                        <label>Option1</label>
                                                        <input type='text' name='option[]' id='option1' class='form-control'></input>
                                                    </div>
                                                </li>
                                            </ul>
                                            <?php
                                         
                                            ?>
                                        </div>
                                        <div class="modal-footer">
                                            <?php
                                            echo Form::submit('Add poll', array('class'=>'btn btn-default'));
                                            echo Form::close();
                                            ?>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--dit script dient om dynamisch inputs toe te voegen om opties aan de poll te geven-->
                        <script type="text/javascript">
                            var teller = 2;                                                           
                            $('body').on('keydown', '#options li:last' , function(i){
                                    $('#options').append($("<li id='option'><div class='form-group'><label>Option "+ teller +"</label><input type='text' name='option[]' id='option"+ teller + "' class='form-control'></input></div></li>"));                            
                                    teller ++;                                                                                
                            });
                        </script> 
                        <div class="panel-body">
                            <!--de tabel die alle polls toont-->
                            <table id='pollTable' class="table table-striped table-bordered sorted_table">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Poll name</th>
                                        <th>Category</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>  
                                    <?php
                                    foreach($polls as $poll){
                                        ?>
                                        <tr>
                                            <td>
                                                <input id='bulkcheck' type="checkbox" name="bulkpoll[]" value="<?php echo $poll->id?>"><br>
                                            </td>
                                            <!--poll naam-->
                                            <td id='pollNameCol<?php echo $poll->id ?>'><?php echo $poll->name ?></td>
                                            <!--poll category-->
                                            <td id='pollCatCol<?php echo $poll->id?>'><?php
                                            if(isset($poll->category()->first()->name)){
                                                echo $poll->category()->first()->name;
                                            }else{
                                                echo 'geen category';
                                            }
                                             ?></td>
                                            <!--verwijderen, editten, bekijken van poll-->
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <!--opent de verwijder modal-->
                                                    <button type="button" id="remove_user" data-toggle="modal" data-target="#deleteModal<?php echo $poll->id?>"  class="btn btn-danger btn-sm">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                    <!--opent de edit modal-->
                                                    <button type="button" id="edit_poll<?php echo $poll->id?>" data-target="#editModal<?php echo $poll->id?>"  class="btn btn-warning btn-sm">
                                                        <span class="glyphicon glyphicon-edit"></span>
                                                    </button>
                                                    <!--toont de poll-->
                                                    <a href='/p/<?php echo $poll->id ?>' type="button" id=""<?php echo $poll->id?>" class="btn btn-primary btn-sm">
                                                        <span class="glyphicon glyphicon-eye-open"></span>
                                                    </a>
                                                    
                                                </div>
                                                <?php
                                                //delete modal
                                                echo Form::open(array('action' => array('AdminController@deletePoll'),'id'=>'form-polldelete'. $poll->id.''));
                                                echo Form::hidden('poll_id',$poll->id);
                                                ?>
                                                <div id="deleteModal<?php echo $poll->id?>" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h4 class="modal-title">Are you sure?</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Do you want to delete poll <b><?php echo $poll->name ?></b>?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type='submit' id="delete-btn<?php echo $poll->id?>" class="btn btn-danger odom-submit" data-dismiss="modal">Delete</button>
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                echo Form::close();    
                                                ?>   
                                                <script>
                                                    $('#delete-btn<?php echo $poll->id?>').on('click', function(e){
                                                        var $form=$('#form-polldelete<?php echo $poll->id?>');  
                                                        $form.submit();
                                                    });
                                                </script>
                                                <!--voor editten modal-->
                                                <?php 
                                                echo Form::open(array('action' => array('AdminController@editPoll'),'id'=>'form-edit'. $poll->id.''));
                                                echo Form::hidden('poll_id'. $poll->id,$poll->id);
                                                ?>                       
                                                <div id="editModal<?php echo $poll->id?>" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h4 class="modal-title">Edit poll <b><?php echo $poll->name ?></b></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div id='editErrors<?php echo $poll->id?>' class="alert alert-danger">
                                                                    <ul>
                                                                    </ul>
                                                                </div>   
                                                                <?php
                                                                echo Form::label('', 'Name:');?><br><?php
                                                                echo Form::text('name'. $poll->id, $poll->name, array('id'=> 'nameEdit'. $poll->id ,'class'=>'form-control'));   
                                                                echo Form::label('', 'Category:');?><br><?php
                                                                ?> 
                                                                <select id='cats<?php echo $poll->id ?>' class='form-control' name='cat'>
                                                                    <?php
                                                                    if($poll->category_id == '0'){
                                                                    ?>
                                                                        <option disabled selected value> -- select a category -- </option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    <?php 
                                                                    foreach($categories as $category){  
                                                                        if($poll->category_id == $category->id){
                                                                            ?>
                                                                            <option selected value='<?php echo $category->id ?>'> <?php echo $category->name ?> </option> 
    <?php
                                                                        }else{
                                                                        ?>
                                                                        <option value='<?php echo $category->id ?>'><?php echo $category->name ?></option> 
                                                                        <?php 
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select><br>
                                                                <?php
                                                                echo Form::label('', 'Options:');?><br>
                                                                <!--deze tabel dient om opties toe te voegen, aan te passen en te verwijderen-->
                                                                    <table id='options<?php echo $poll->id?>' class='table table-striped table-bordered sorted_table'>
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Value</th>
                                                                                <th>Score</th>
                                                                                <th>Delete</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php 
                                                                            // toont alle huidige opties
                                                                            foreach($poll->options as $option){
                                                                                ?>
                                                                                    <tr id='optionRow<?php echo $option->id ?>'>
                                                                                        <td><?php 
                                                                                            echo Form::text('oldoptions[]', $option->name, array('class'=>'form-control','id'=>$option->id)); 
                                                                                        ?></td>
                                                                                        <td><?php echo $option->score; ?></td>
                                                                                        <td>
                                                                                            <?php delete($option); ?>  
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                            <tr id='addrow1'>
                                                                                <td> <?php echo Form::text('options[]', '', array('class'=>'form-control', 'placeholder'=>'Add a new option')); ?>  </td>
                                                                                <td>0</td>
                                                                                <td><a id='removeNew1' class='btn btn-danger glyphicon glyphicon-remove btn-sm'></a></td>
                                                                            </tr>
                                                                            <script>
                                                                                $("#removeNew1").on("click",function(){
                                                                                    $("table#options<?php echo $poll->id?> tr#addrow1").remove();
                                                                                });                                                                   
                                                                            </script>
                                                                            <script type="text/javascript">    
                                                                                var teller = 2;                                       
                                                                                $('body').on('keydown', '#options<?php echo $poll->id?> tr:last input' , function(i){    
                                                                                    var html ='<tr id="addrow'+ teller +'"><td>' + '<?php echo Form::text("options[]", "", array("class"=>"form-control", "placeholder"=>"Add a new option")); ?>' + '</td><td>0</td><td><a id="removeNew'+teller+'" class="btn btn-danger glyphicon glyphicon-remove btn-sm"></a></td></tr>';
                                                                                    html += '<script>$("#removeNew'+ teller+'").on("click",function(){$("table#options<?php echo $poll->id?> tr#addrow'+teller+'").remove();});<\/script>';
                                                                                    $('#options<?php echo $poll->id?> tr:last').after(html);        
                                                                                    teller++;                                             
                                                                                });    
                                                                            </script>
                                                                        </tbody>
                                                                    </table>  
                                                            </div>
                                                            <div class="modal-footer">
                                                                <?php echo Form::submit('Edit poll', array('id'=>'edit-btn'.$poll->id,'class'=>'btn btn-warning'));
                                                                echo Form::close(); ?>
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <script>
                                                    $('#editErrors<?php echo $poll->id?>').hide();
                                                    $('#edit_poll<?php echo $poll->id?>').on('click',function( event ) {
                                                        $('#editModal<?php echo $poll->id?>').modal('show');
                                                    });
                                                    $('#edit-btn<?php echo $poll->id?>').on('click',function( event ) {
                                                        var options = $('table#options<?php echo $poll->id?> input[name^=options]').map(function(idx, elem) {
                                                            return $(elem).val();
                                                        }).get();
                                                        
                                                        var huidigeOptions = new Array();
                                                        var oldOptions = $('table#options<?php echo $poll->id?> input[name^=oldoptions]').map(function(idx, elem) {
                                                            var subarray = [$(elem).attr('id'),$(elem).val()];
                                                            huidigeOptions.push(subarray);
                                                            return $(elem).val();
                                                        }).get();
                                                        event.preventDefault(); 
                                                        var cat = $('#cats<?php echo $poll->id?>').val();
                                                        $.ajax({
                                                            url: '/admin/polls/editPoll',
                                                            type: "post",
                                                            data: {'name':$('input[name=name'+ <?php echo $poll->id?> +']').val(), 
                                                                    'cat':cat,
                                                                    'options': options,
                                                                    'oldoptions':huidigeOptions,
                                                                    '_token': $('input[name=_token]').val(),
                                                                    'poll_id':$('input[name=poll_id'+ <?php echo $poll->id?> +']').val()
                                                                },
                                                                success: function(){
                                                                    $('#editModal<?php echo $poll->id?>').modal('hide');
                                                                    $('#pollCatCol<?php echo $poll->id?>').html($('#cats<?php echo $poll->id?> option:selected').text());
                                                                    $('#pollNameCol<?php echo $poll->id ?>').html($('#nameEdit<?php echo $poll->id?>' ).val());
                                                                },                           
                                                                error:function(message){     
                                                                    var data = message.responseJSON;
                                                                    
                                                                    $('#editErrors<?php echo $poll->id?>').show();
                                                                    $('#editErrors<?php echo $poll->id?> ul').empty();
                                                                    for(var k in data) {     
                                                                        $('#editErrors<?php echo $poll->id?> ul').append('<li>'+data[k]+'</li>');         
                                                                    }  
                                                                }      
                                                            });
                                                        });
                                                </script> 
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>  
                            <a class='btn btn-danger' style='margin:0px 0px 15px 15px;' id='bulk'>Delete selected polls</a>
                            <!--bulk-->
                            <script>  
                                $("#bulk").click(function(){
                                    var polls = $('#bulkcheck:checked').serializeArray();
                                    console.log(polls);
                                    $.ajax({
                                        url: '/admin/polls/bulkdelete',
                                        type: "post",
                                        data: {'poll_ids':polls , 
                                                '_token': $('input[name=_token]').val(),  
                                            },
                                        success: function(){
                                            location.reload();
                                            }
                                    });  
                                });
                            </script>
                        </div>   
                    </div>
                    <!--categories-->
                    <div role="tabpanel" class="tab-pane" id="categorytab"> 
                        <!--category aanmaken-->
                        <!--opent het modal om een category te maken-->
                        <div class="panel-body"> 
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#CreateCatModal">Create a category</button>
                        </div>
                        <!--het modal om een category te maken-->
                        <div class="panel-body">
                            <div id="CreateCatModal" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Create a new category</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div id='catcreateErrors' class="alert alert-danger">
                                            <ul>
                                            </ul>
                                        </div>
                                        <?php
                                        echo Form::open(array('action' => array('AdminController@postCategory')));
                                        echo Form::label('','Name:');
                                        echo Form::text('catname', null, array('class'=>'form-control'));
                                        ?>
                                    </div>
                                    <div class="modal-footer">
                                        <?php
                                            echo Form::submit('Add category', array('class'=>'btn btn-default','id'=>'createCat'));
                                        echo Form::close();
                                        ?>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                             <script>
                                $('#catcreateErrors').hide();
                                $('#createCat').on('click', function(e){
                                    var naam = $('input[name=catname]').val();
                                    e.preventDefault(); 
                                    $.ajax({
                                        url: '/admin/polls/createCategory',
                                        type: "post",
                                        data: { 'name':naam,
                                                '_token': $('input[name=_token]').val(),  
                                            },
                                        success: function(){
                                                $('#CreateCatModal').modal('hide');
                                                $('#catNameCol').append('<tr><td>'+naam+'</td><td>Refresh for actions</td></tr>');
                                            },
                                        error:function(message){     
                                                var data = message.responseJSON;
                                                $('#catcreateErrors').show();
                                                $('#catcreateErrors ul').empty();
                                                for(var k in data) {     
                                                $('#catcreateErrors ul').append('<li>'+data[k]+'</li>');         
                                            }  
                                        }  
                                    });  
                                });
                                
                            </script>
                            <!--einde category aanmaken-->
                            <table class="table table-striped table-bordered sorted_table">
                                <thead>
                                    <tr>
                                        <th>Category name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id='catNameCol'>
                                        <?php 
                                            foreach($categories as $cat){
                                                ?>
                                                <tr>
                                                    <td id="name<?php echo $cat->id?>"><?php echo $cat->name?></td>
                                                    <td>   
                                                        <div class="btn-group" role="group">
                                                            <button type="button" id="" data-toggle="modal" data-target="#deleteModal<?php echo $cat->id?>"  class="btn btn-danger btn-sm">
                                                                    <span class="glyphicon glyphicon-trash"></span>
                                                            </button>
                                                            <button type="button" id="" data-toggle="modal" data-target="#editModal<?php echo $cat->id?>"  class="btn btn-warning btn-sm">
                                                                    <span class="glyphicon glyphicon-edit"></span>
                                                            </button>
                                                        </div>
                                                        <!--category aanpassen-->
                                                        <?php 
                                                        echo Form::open(array('action' => array('AdminController@editCategory'),'id'=>'form-edit'. $cat->id.''));
                                                        echo Form::hidden('cat_id',$cat->id);
                                                        
                                                        ?>   
                                                        <div id="editModal<?php echo $cat->id?>" class="modal fade" role="dialog">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                        <h4 class="modal-title">Edit category <?php echo $cat->name ?></h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div id='cateditErrors' class="alert alert-danger">
                                                                            <ul>
                                                                            </ul>
                                                                        </div>
                                                                        <?php
                                                                        echo Form::label('', 'Name:');?><br><?php
                                                                        echo Form::text('name'. $cat->id, $cat->name, array('class'=>'form-control'));  
                                                                        ?>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type='submit' id="edit-btn<?php echo $cat->id?>" class="btn btn-warning odom-submit" data-dismiss="modal">Edit</button>
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        echo Form::close();    
                                                        ?>   
                                                        <script>
                                                            $('#cateditErrors').hide();
                                                            $('#edit-btn<?php echo $cat->id?>').on('click', function(e){
                                                                var naam = $('input[name=name<?php echo $cat->id ?>]').val();
                                                                e.preventDefault(); 
                                                                $.ajax({
                                                                    url: '/admin/polls/editCat',
                                                                    type: "post",
                                                                    data: {'cat_id': <?php echo $cat->id ?>, 
                                                                            'name':naam,
                                                                            '_token': $('input[name=_token]').val(),  
                                                                        },
                                                                    success: function(){
                                                                            $('#cateditErrors').hide();
                                                                            $('#name<?php echo $cat->id?>').text(naam);
                                                                            $('#editModal<?php echo $cat->id?>').modal('hide');
                                                                        },
                                                                    error:function(message){     
                                                                            $('#editModal<?php echo $cat->id?>').modal('show');
                                                                            var data = message.responseJSON;
                                                                            $('#cateditErrors').show();
                                                                            $('#cateditErrors ul').empty();
                                                                            for(var k in data) {     
                                                                            $('#cateditErrors ul').append('<li>'+data[k]+'</li>');   
                                                                            }      
                                                                        }  
                                                                });  
                                                            });
                                                            
                                                        </script>
                                                        <!--einde category aanpassen-->
                                                        <!--category verwijderen-->                
                                                        <?php 
                                                        echo Form::open(array('action' => array('AdminController@deleteCategory'),'id'=>'form-delete'. $cat->id.''));
                                                        echo Form::hidden('cat_id',$cat->id);
                                                        ?> 
                                                        <div id="deleteModal<?php echo $cat->id?>" class="modal fade" role="dialog">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                        <h4 class="modal-title">Are you sure?</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Do you want to delete category <b><?php echo $cat->name ?></b>?</p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type='submit' id="delete-btn<?php echo $cat->id?>" class="btn btn-danger odom-submit" data-dismiss="modal">Delete</button>
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        echo Form::close();    
                                                        ?>   
                                                        <script>
                                                            $('#delete-btn<?php echo $cat->id?>').on('click', function(e){
                                                                var $form=$('#form-delete<?php echo $cat->id?>');  
                                                                $form.submit();
                                                            });
                                                        </script>
                                                        <!--einde category verwijderen-->
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        ?>
                                </tbody>           
                            </table>   
                        </div>
                    </div>
                    <!--settings-->
                    <div role="tabpanel" class="tab-pane" id="settingstab">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Carry out</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">Enable/disable percentages</th>
                                    <th> <?php 
                                        echo Form::open(array('action' => array('AdminController@disablePercentage')));
                                        if($percentageSetting->value == 'true'){
                                            echo Form::checkbox('disable', 'Yes',true,['onChange'=>'this.form.submit()']);
                                        }else{
                                            echo Form::checkbox('disable', 'Yes',null,['onChange'=>'this.form.submit()']);
                                        }
                                        echo Form::token();
                                        echo Form::close();    
                                        ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row">Give completed polls a different look</th>
                                    <td> <?php 
                                        echo Form::open(array('action' => array('AdminController@changePollLookAfterComplete')));                    
                                        if($lookSetting->value == '1'){
                                            echo Form::checkbox('disable', 'Yes',true,['onChange'=>'this.form.submit()']);
                                        }else{
                                            echo Form::checkbox('disable', 'Yes',null,['onChange'=>'this.form.submit()']);
                                        }
                                        echo Form::close();    
                                        ?>
                                    </td>                           
                                </tr>
                            </tbody>
                        </table>  
                    </div>
                    </div>  
                    <!--einde van tab content-->
                  
                </div>
            </div>
        </div>
    </div>
</div>
<?php
function delete($option){
?>
    <button id='deleteOption<?php $option->id ?>' type="button" data-toggle="modal" data-target="#deleteOptionModal<?php echo $option->id?>" class="btn btn-danger btn-sm">
        <span class="glyphicon glyphicon-trash"></span>
    </button>
    <div id="deleteOptionModal<?php echo $option->id?>" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Are you sure?</h4>
                </div>
                <div class="modal-body">
                    <p>Do you want to permanently delete option <b><?php echo $option->name ?></b>?</p>
                </div>
                <div class="modal-footer">
                    <button type='button' id="deleteOption-btn<?php echo $option->id?>" class="btn btn-danger ">Delete</button>
                    <button type="button" id="canceldeleteOption-btn<?php echo $option->id?>" class="btn btn-default">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $("#canceldeleteOption-btn<?php echo $option->id?>").on('click', function(){
                $('#deleteOptionModal<?php echo $option->id?>').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            });
            
            $("#deleteOption-btn<?php echo $option->id?>").click(function(){
                $('#deleteOptionModal<?php echo $option->id?>').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
               
                $.ajax({
                    url: '/admin/polls/deleteOption',
                    type: "post",
                    data: {'option_id': <?php echo $option->id ?>, 
                            '_token': $('input[name=_token]').val(),  
                        },
                    success: function(){
                         $('#optionRow<?php echo $option->id ?>').remove();
                        }
                });  
            });
        });
    </script>  
    <?php
}
?>
@endsection

