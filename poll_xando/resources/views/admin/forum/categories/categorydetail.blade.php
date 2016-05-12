@extends('layouts.dashboard')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Admin forum - Topics of <?php echo $cat->name?></div>
                <div id='links'>
                    <a href='/admin/forum/categories'>Ga terug naar categorieën</a><br>
                    <a href="/admin/forum/categories/<?php echo $cat->name;?>/create">Create a topic</a><br>
                </div>
                <?php
                    echo '<p>' . $cat->description . '</p>';
                ?>
                <table class="table table-striped table-bordered sorted_table" style="width:100%">
                <tr>
                    <th>Topic</th>
                    <th>Aangemaakt door</th>
                    <th>Actions</th>
                </tr>
                <?php 
                foreach($topics as $topic){
                      ?>   
                        <tr id='topicRow<?php echo $topic->id ?>'>             
                            <td><a href="/admin/forum/categories/<?php echo $cat->id;?>/<?php echo $topic->id; ?>"><?php echo $topic->subject; ?></a></td>  
                            <td><?php 
                            if(isset($topic->user->name)){
                                echo $topic->user->name;
                            }else{
                                echo 'deleted';
                            }
                            ?></td>
                             <td>
                                <button type="button" id="remove_topic" data-toggle="modal" data-target="#deleteModal<?php echo $topic->id?>"  class="btn btn-danger btn-sm">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </button> 
                            </td>
                            <?php
                            //delete
                            echo Form::open(array('action' => array('ForumController@deleteTopic'),'id'=>'form-topicdelete' .$topic->id));
                            echo Form::hidden('topic_id',$topic->id);
                            ?>
                            <div id="deleteModal<?php echo $topic->id?>" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Are you sure?</h4>
                                        </div>
                                        <div class="modal-body">
                                            <p>Do you want to delete topic <b><?php echo $topic->subject ?></b>?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type='submit' id="delete-btn<?php echo $topic->id?>" class="btn btn-danger odom-submit" data-dismiss="modal">Delete</button>
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
                                $("#delete-btn<?php echo $topic->id?>").click(function(){                           
                                    $.ajax({
                                        url: '/admin/forum/categories/deleteTopic',
                                        type: "post",
                                        data: {'topic_id': <?php echo $topic->id ?>, 
                                                '_token': $('input[name=_token]').val(),  
                                            },
                                        success: function(){
                                                $('#topicRow<?php echo $topic->id ?>').remove();
                                            }
                                            
                                    }); 
                                });
                            });
                        </script>  
                    <?php
                }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection