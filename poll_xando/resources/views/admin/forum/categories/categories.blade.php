@extends('layouts.dashboard')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Admin forum - Categories</div>
                <div id='links'>
                    <a href="{{ url('/admin/forum/categories/create') }}">Create a category</a><br>
                </div>
                <table class="table table-striped table-bordered sorted_table" style="width:100%">
                    <tr>
                        <th>Category</th>
                        <th>Amount of topics</th>
                        <th>Actions</th> 
                    </tr>
                <?php 
                foreach($cats as $cat){
                      ?>                
                        <tr id='catRow<?php echo $cat->id ?>'>
                            <td><a href="/admin/forum/categories/<?php echo $cat->id;?>"><?php echo $cat->name; ?></a></td>
                            <td><?php echo count($cat->topics) ?></td> 
                            <td>
                                <button type="button" id="remove_cat" data-toggle="modal" data-target="#deleteModal<?php echo $cat->id?>"  class="btn btn-danger btn-sm">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </button>
                            </td>
                            <?php
                            //delete
                            echo Form::open(array('action' => array('ForumController@deleteCat'),'id'=>'form-catdelete' .$cat->id));
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
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            echo Form::close(); ?>  
                        </tr>  
                        <script>
                            $(function() {                           
                                $("#delete-btn<?php echo $cat->id?>").click(function(){                           
                                    $.ajax({
                                        url: '/admin/forum/categories/delete',
                                        type: "post",
                                        data: {'cat_id': <?php echo $cat->id ?>, 
                                                '_token': $('input[name=_token]').val(),  
                                            },
                                        success: function(){
                                                $('#catRow<?php echo $cat->id ?>').remove();
                                            }   
                                    });  
                                });
                            });
                    </script>  
                    <?php
                }
                ?>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
