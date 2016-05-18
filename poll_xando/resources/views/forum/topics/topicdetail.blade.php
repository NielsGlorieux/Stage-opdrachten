@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">User forum - Topic - <?php echo $topic->subject?></div> 
                    <div id='links'>
                        <a href='/forum/categories/<?php echo $topic->u_f_category_id; ?>'>Go back</a>
                    </div>
                    <div id="topicnav">
                        From: <?php 
                            if(isset($topic->user->name)){
                                echo $topic->user->name;
                            }else{
                                echo 'deleted';
                            }
                        ?><br>
                        Amount of posts: <?php 
                            if(isset($topic->user)){
                                echo count($topic->user->topics()); 
                            }else{
                                echo 'unknown';
                            }
                        ?><br>
                    </div>
                    <div id="topiccontent">
                        <h2><?php echo $topic->subject; ?></h2>
                        <?php
                        echo $topic->content; ?>
                    </div>    
                    <div id='comments'>
                        <div>      
        <?php               if(Auth::check()){
                            echo Form::open(array('action' => array('UserForumController@createReply')));
                            echo Form::hidden('topic_id', $topic->id);
                            echo Form::textarea('body',null, array('placeholder'=>'type your comment here..', 'class'=>'form-control', 'rows'=>'3'));
                            echo Form::submit('Post comment',array('class'=>'btn btn-primary','id'=>'commentBtn'));
                            echo Form::close();
                            }else{
                                echo 'Login to comment';
                            }
                            ?> 
                        </div>
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <?php
                        foreach($replies as $reply){
                                    ?>
                            <li class="comment">
                                <div class="aut"><?php
                                    if(isset($reply->user->name)){
                                        echo $reply->user->name;
                                    }else{
                                        echo 'deleted';
                                    }
                                ?></div>
                                <div class="comment-body"><?php echo $reply->content; ?></div>
                                <div class="timestamp"><?php echo $reply->created_at; ?></div>                   
                            </li>    
                                        <?php                       
                            }
?>           
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>                       
<style>
html, body, div, h1, h2, h3, h4, h5, h6, ul, ol, dl, li, dt, dd, p, blockquote,  
pre, form, fieldset, table, th, td { 
    margin: 0; padding: 0; 
}  
    
body {  
    font-size: 14px;  
    line-height:1.3em;  
}  
</style>
@endsection
