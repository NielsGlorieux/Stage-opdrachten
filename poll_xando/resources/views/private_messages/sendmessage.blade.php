@extends('layouts.app')

@section('content')
@include('partials.inbox')
<div class="container">   
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default"> 
                <div class="panel-heading">Send message</div>
                <div class="panel-body">                     
                <h2>Send a message</h2> 
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
                    echo Form::label('', 'To: (username) Press space to add another user'); ?> <br>
                    <div name='div' class="multiple-val-input" id='to'>
                        <ul>
                    <?php
                            echo Form::text('usernames'); //username[][name] ?> <br>
                            <span class="input_hidden"></span>
                        </ul>
                    </div>
                    <?php
                    echo Form::label('', 'Title:'); ?> <br> <?php
                    echo Form::text('title',null, array('class'=>'form-control')); ?> <br> <?php
                    echo Form::label('', 'Message:'); ?> <br> <?php
                    echo Form::textarea('body',null, array('class'=>'form-control')); ?> <br> <?php
                    echo Form::submit('Send',array('class'=>'btn btn-primary')); ?> <br> <?php
                    echo Form::close();  
                ?>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.multiple-val-input{
    /*height: auto;*/
    min-height: 35px;
    overflow: hidden;
    cursor: text;
    border: 1px solid #aaaaaa;
    
}
.multiple-val-input ul{
    float: left;
    padding: 0;
    margin: 0;
}
.multiple-val-input ul li{
    list-style: none;
    float: left;
    padding: 3px 5px 3px 5px;
    margin-bottom: 3px;
    margin-right: 3px;
    position: relative;
    line-height: 13px;
    cursor: default;
    border: 1px solid #aaaaaa;
    border-radius: 3px;
    background-clip: padding-box;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    
}
.multiple-val-input ul li a{
    display: inline;
    color: #333;
    text-decoration: none;
}
.multiple-val-input ul li a, .multiple-val-input ul li div{
    display: inline;
    margin-left: 3px;
}
.multiple-val-input input[type="text"]{
    float: left;
    border: none;
    outline: none;
    height: 20px;
    min-width: 5px;
    width: 5px;
}
.multiple-val-input span.input_hidden{
    font-size: 14px;
    position: absolute;
    clip: rect(0,0,0,0);
}

#to{
    border-radius: 6px;
    border: 1px solid #bfbfbf;
    padding: 5px;
}
</style>
@endsection
@section('page-script')
<script>
$('.multiple-val-input').on('click', function(){
            $(this).find('input:text').focus();
});
$('.multiple-val-input ul input:text').on('input propertychange', function(){
    $(this).siblings('span.input_hidden').text($(this).val());
    var inputWidth = $(this).siblings('span.input_hidden').width();
    $(this).width(inputWidth);
});
$('.multiple-val-input ul input:text').on('keypress', function(event){
    if(event.which == 32 || event.which == 44 || event.which == 13){
        var toAppend = $(this).val();
        if(toAppend!=''){
            $('<li><a href="#">×</a><div><input name="username[][name]" value="'+toAppend+'" type="hidden">'+toAppend+'</input></div></li>').insertBefore($(this));
            $(this).val('');
        } else {
            return false;
        }
        return false;
    };
});

$('.multiple-val-input ul input:text').on('focusout', function(event){
        var toAppend = $(this).val();
        if(toAppend!=''){
            $('<li><a href="#">×</a><div><input name="username[][name]" value="'+toAppend+'" type="hidden">'+toAppend+'</input></div></li>').insertBefore($(this));
            $(this).val('');
        };
});

$(document).on('click','.multiple-val-input ul li a', function(e){
    e.preventDefault();
    $(this).parents('li').remove();
});

$('form').submit(function( event ) {
    var users = new Array();
    $('div[name=div] ul li').each(function(i){
        users.push($(this).find('div input').val());
    }); 
    event.preventDefault();  
             
    $.ajax({
        url: '/sendMessage',
        type: "post",
        data: { '_token': $('input[name=_token]').val(),
                'usernames':users,
                'title':$('input[name=title]').val(),
                'body':$('textarea[name=body]').val(),     
            },
            success: function(){
                window.location.replace('/inbox') 
            },
        error :function( jqXhr ) {
        console.log(jqXhr);
            if( jqXhr.status === 422 ) {
            $errors = jqXhr.responseJSON; 
            errorsHtml = '<div class="alert alert-danger"><ul>';   
            $.each( $errors, function( key, value ) {
                errorsHtml += '<li>' + value[0] + '</li>';
            });
            errorsHtml += '</ul></di>';    
            $( '#form-errors' ).html( errorsHtml ); 
            }
        }

    });      
}); 
</script>
@endsection