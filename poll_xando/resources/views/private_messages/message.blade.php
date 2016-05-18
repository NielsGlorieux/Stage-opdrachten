@extends('layouts.app')
@section('content')
@include('partials.inbox')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Message from <?php echo App\User::find($message->verstuurder_id)->name ?></div>
                <?php echo '<h1>' .$message->title .'</h1>';
                echo '<div id="content"><p>Message:</p> <p>' . $message->body . '</p></div>';
                if(Auth::user()->id == $message->ontvanger_id){
                    $message->gelezen = true;
                    $message->save();
                   ?>
                <div class="panel-body">
                    <p>Answer:</p>
                      @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul id="err">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                    <div id='form-errors'>
                    </div>
                    <?php
                    echo Form::open(array('action' => array('PrivateMessageController@sendMessage')));
                    echo '<input type="hidden" name="usernames[]" value="'. App\User::find($message->verstuurder_id)->name . '">'; 
                    echo Form::label('', 'Title:'); ?> <br> <?php
                    echo Form::text('title','Re:'.$message->title, array('class'=>'form-control')); ?> <br> <?php
                    echo Form::label('', 'Message:'); ?> <br> <?php
                    echo Form::textarea('body',null, array('class'=>'form-control')); ?> <br> <?php
                    echo Form::submit('Send',array('class'=>'btn btn-primary')); ?> <br> <?php
                    echo Form::close();  
                ?>
                </div>
                <?php 
                } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
h1,p{
    margin: 15px;
}
#content{
    margin: 15px;
    border: 1px solid gray; 
}
</style>
@endsection
