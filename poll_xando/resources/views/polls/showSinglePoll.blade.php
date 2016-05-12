@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">All polls</div>
                <div class="panel-body">
                    <?php 
                    //huidige aangemelde opvragen voor vorige stemmen te tonen
                    $user = Auth::user(); 
                    $votedByUser = array();
                    if(Auth::check()){
                        foreach($user->pollsVoted as $vote){
                            array_push($votedByUser, $vote->pivot->votedOption);  
                        }
                    }
                    //poll partial
                    ?>
                    @include('partials.poll')
                    <?php

                    if($poll->haveComments == 1){
                    ?>     
                    <h4>Comments</h4>
                    <div>
                    <?php
                    if(Auth::check()){
                    echo Form::open(array('action' => array('PollController@postComment', $poll->id)));
                    echo Form::hidden('pollIdee', $poll->id);
                    echo Form::hidden('level', 1);
                    echo Form::hidden('parent_id', 0);
                    echo Form::textarea('body',null, array('class'=>'form-control','placeholder'=>'post a comment', 'id'=>'comment_body'));
                    echo Form::submit('Post comment',array('id'=>'submit_button','class'=>'btn btn-primary'));
                    echo Form::close();
                    }else{
                        echo 'You must be logged in to comment.';
                    }
                     ?> 
                    </div>
                    <?php 
                    }                   
                    ?>
                    <?php  
                        $q = $comments->where('parent_id',0);
                        foreach($q as $row):  
                        getComments($row, $poll);  
                        endforeach;  
                        $teller = 0;
                        function getComments($row, $poll) {  
                            echo "<li class='comment'>";  
                            if(isset($row->user->name)){
                                echo "<div class='aut'>".$row->user->name."</div>"; 
                            }else{
                                echo "<div class='aut'>deleted</div>"; 
                            }
                            echo "<div class='comment-body'>".$row->body."</div>";  
                            echo "<div class='timestamp'>".$row->created_at."</div>";  
                            if($row->level < $poll->maxLevelComments){
                            echo "<a id='reply". $row->id ."'>Reply</a>";  
                             }//if
                                echo Form::open(array('class'=>'theform' ,'id'=>"theform". $row->id ,'action' => array('PollController@postComment', $poll->id)));
                                echo Form::hidden('pollIdee', $poll->id);
                                echo Form::hidden('level', $row->level+1);
                                echo Form::hidden('parent_id', $row->id);
                                echo Form::textarea('body',null, array('placeholder'=>'post a comment', 'id'=>'comment_body', 'class'=>'form-control')); 
                                echo Form::submit('Post comment',array('id'=>'submit_button', 'class'=>'btn btn-primary'));
                                echo Form::close();
                            ?>
                                <script>
                                    $("#reply<?php echo $row->id ?>" ).click(function() {
                                        $( "#theform<?php echo $row->id ?>" ).slideToggle( "fast", function() {
                                        });
                                    });
                                </script>
                            <?php
                            $q = $row->children; 
                            if(count($q)>0) 
                            {  
                            echo "<ul>";  
                            foreach($q as $c) {  
                            getComments($c, $poll);  
                            }  
                            echo "</ul>";  
                            }  
                            echo "</li>";        
                        }       
                    if(Auth::check() && Auth::user()->is('admin') == true){
                    ?>
                    <br>
                    <h3>Administration</h3>
                     <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Actie</th>
                                <th>Uitvoeren</th>
                                <th>Huidige instelling</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Zet comments aan/af</th>
                                <th> 
                                    <?php
                                    echo Form::open(array('action' => array('AdminController@disableComments', $poll->id)));
                                    if($poll->haveComments == 1){
                                        echo Form::checkbox('disable', 'Yes',true);
                                    }else{
                                        echo Form::checkbox('disable', 'Yes');
                                    }
                                    echo Form::submit('Save', array('class'=>'btn btn-primary'));
                                    echo Form::close();        
                                    ?>  
                                </th>
                            </tr>
                            <tr>
                                <th scope="row">Stel maximum aantal votes in</th>
                                <td>
                                    <?php
                                    echo Form::open(array('action' => array('AdminController@setMaxVotes', $poll->id)));
                                    echo Form::hidden('pollId', $poll->id);
                                    echo Form::text('maxVotes');
                                    echo Form::submit('Save', array('class'=>'btn btn-primary'));
                                    echo Form::close();        
                                    ?>
                                </td>
                                <td>
                                    <?php echo $poll->maxVotes; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Stel maximum level van comments in (1-..)</th>
                                <td>
                                   <?php    
                                    echo Form::open(array('action' => array('AdminController@setMaxLevel', $poll->id)));
                                    // echo Form::label('', '(1-..) ');
                                    echo Form::hidden('pollId', $poll->id);
                                    echo Form::text('maxLevel');
                                    echo Form::submit('Save', array('class'=>'btn btn-primary'));
                                    echo Form::close();        
                                    ?>
                                </td>
                                <td>
                                    <?php echo $poll->maxLevelComments; ?>
                                </td>
                            </tr>  
                        </tbody>
                    </table>
                    <?php } ?>                  
                </div>      
            </div>
        </div>
    </div>
</div>
<style>
    html, body, h1, h2, h3, h4, h5, h6, ul, ol, dl, li, dt, dd, p, blockquote,  
    pre, form, fieldset, table, th, td { margin: 0; padding: 0; }  
    
    #submit_button{
       margin-top: 10px; 
    }
    a, a:visited {  
    outline:none;  
    color:#2e6da4;  
    }  
    .btn{
        margin-left: 5px;
    }                         
</style>                  
@endsection



