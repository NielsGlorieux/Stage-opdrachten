@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Create a page</div>
                <div class="panel-body">
                  <label for='contactCheck'><input type="checkbox" id='contactCheck' name="contactCheck" style="float: left; margin-top: 5px;"> Deze pagina is een contact formulier</label> 
                    <div id='contactSettings'>
                        <?php
                        echo Form::open(array('action' => 'PageController@create', 'id'=>'sendForm'));
                        echo Form::hidden('isForm','1');
                        echo Form::label('', 'Slug van de pagina (wordt de url)');
                        echo Form::text('slug',null, array('class'=>'form-control')); 
                        echo Form::label('', 'Title van de pagina');
                        echo Form::text('title',null, array('class'=>'form-control'));
                        echo Form::label('', 'De content van de pagina'); ?>    
                        <?php
                        echo Form::hidden('content', null, array('id'=>'content')); ?> <br> <?php
                        echo Form::submit('Create formpage',array('name'=>'btnSubmit','id'=>'btnSubmit','class'=>'btn btn-primary'));
                        echo Form::close(); 
                        ?> 
                        <h2>Result</h2>
                        <textarea name="contentbox" id='contentbox'></textarea>
                        <table id='fields' class='table table-striped'>
                            <thead>
                                <tr>
                                    <th>Input type</th>
                                    <th>Label text</th>
                                    <th>Add</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Normal Textfield</td>
                                    <td><input type='text' class='form-control' id='textfieldLabel'></input></td>
                                    <td><button type="button"  id='textfield' class="btn btn-primary"><span class='glyphicon glyphicon-plus'></span></button></td>
                                </tr>
                                <tr>
                                    <td>Email Textfield</td>
                                    <td><input type='text' class='form-control' id='emailtextfieldLabel'></input></td>
                                    <td><button type="button" id='emailtextfield' class="btn btn-primary"><span class='glyphicon glyphicon-plus'></span></button></td>
                                </tr>
                                <tr>
                                    <td>Textarea</td>
                                    <td><input type='text' class='form-control' id='textareaLabel'></input></td>
                                    <td><button type="button" id='textarea' class="btn btn-primary"><span class='glyphicon glyphicon-plus'></span></button></td>
                                </tr>
                                <tr>
                                    <td>Submit button</td>
                                    <td><input type='text' class='form-control' id='buttonText'></input></td>
                                    <td><button type="button" id='submitbutton' class="btn btn-primary"><span class='glyphicon glyphicon-plus'></span></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <h3>Wordt gemaild naar</h3>
                        <input type='text' id='eigenMail'></input>
                    </div>
                    <div id='normalPage'>
                        <?php
                        echo Form::open(array('action' => 'PageController@create'));
                        echo Form::hidden('isForm','0');
                        echo Form::label('', 'Slug van de pagina (wordt de url)'); 
                        echo Form::text('slug',null, array('class'=>'form-control')); 
                        echo Form::label('', 'Title van de pagina'); 
                        echo Form::text('title',null, array('class'=>'form-control')); 
                        echo Form::label('', 'De content van de pagina'); 
                        echo Form::textarea('content', null, array('class' => '','id'=>'normalContent')); ?><br><?php
                        echo Form::submit('Create page',array('class'=>'btn btn-primary'));
                        echo Form::close(); 
                        ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-script')
<script>
    $(document).ready(function(){
        $("#contactSettings").hide();
        $("#normalPage").show();
        
    });
    $('#contactCheck').change(function () {
        $("#normalPage").toggle();
        $("#contactSettings").toggle();
        
    });
</script>


<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>

<script>
  tinymce.init({
    mode : "exact",    
    elements : 'contentbox',
    forced_root_block : false,
    setup : function(editor){
    },
    init_instance_callback : 
    function(editor) { 
        $('#textfield').on('click',function(){
            $label = $('#textfieldLabel').val();
            editor.insertContent('<label for="'+$label+'">'+$label+'</label>');
            editor.insertContent('<input style="width:450px" name="'+$label+'" class="'+$label+' form-control" type="text"></input><br>');
         
         });
         
        $('#textarea').on('click',function(){
            $label = $('#textareaLabel').val();  
            editor.insertContent('<label for="'+$label+'">'+$label+'</label>');
            editor.insertContent('<textarea style="width:450px" name="'+$label+'" class="'+$label+' form-control"></textarea><br>');         
        });
        
        $('#emailtextfield').on('click',function(){
            $label = $('#emailtextfieldLabel').val();
            editor.insertContent('<label for="'+$label+'">'+$label+'</label>');
            editor.insertContent('<input style="width:450px" name="'+$label+'"  class="'+$label+' form-control" type="email"></input><br>');
        });
        
        $('#submitbutton').on('click',function(){
            $label = $('#buttonText').val();
            editor.insertContent('<input class="btn btn-primary" type="submit" value="'+$label+'">');
        });

        $('#sendForm').on('submit',function(e){
                e.preventDefault();
                console.log('ja');
                $form = $("#content");  
                $mail = $('#eigenMail').val();
                console.log($mail);
                $form.val('<form id="mailform" action="MAILTO:'+$mail+'" method="post" enctype="text/plain">' + editor.getContent() + '</form>');

                this.submit();
        });  
    },
    height: 500,
    theme: 'modern',
    plugins: [
        'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars code fullscreen',
        'insertdatetime media nonbreaking save table contextmenu directionality',
        'emoticons template paste textcolor colorpicker textpattern imagetools'
    ],
    toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
    toolbar2: 'print preview media | forecolor backcolor emoticons',
    image_advtab: true,
    templates: [
        { title: 'Test template 1', content: 'Test 1' },
        { title: 'Test template 2', content: 'Test 2' }
    ],
    content_css: [
        '{{ url('/') }}/css/syle.css'
    ]
 });
 
tinymce.init({
    mode : "exact",  
    elements : 'normalContent',
    height: 500,
    theme: 'modern',
    plugins: [
        'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars code fullscreen',
        'insertdatetime media nonbreaking save table contextmenu directionality',
        'emoticons template paste textcolor colorpicker textpattern imagetools'
    ],
    toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
    toolbar2: 'print preview media | forecolor backcolor emoticons',
    image_advtab: true,
    templates: [
        { title: 'Test template 1', content: 'Test 1' },
        { title: 'Test template 2', content: 'Test 2' }
    ],
});
</script>
@endsection