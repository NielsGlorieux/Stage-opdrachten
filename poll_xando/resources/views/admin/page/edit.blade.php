@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <?php 
        if($page->isForm == true){ ?>
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
        <?php } ?>
        <div id='pagewrapper' class="col-md-15 col-md-offset-1"> 
            <div class="panel panel-default">
                <div class="panel-heading">Edit <?php echo $page->title ?></div>
                    <a href='/admin/pages' class='btn btn-primary' id='naarDash' style='margin:15px;'>Ga naar dashboard</a>
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="panel-body">
                        <?php 
                        //om het page type te veranderen
                        echo Form::open(array('action' => 'PageController@changeType','id'=>'typeform'));
                        echo Form::hidden('page_id', $page->id);
                        echo Form::label('', 'Enable/disable form tools'); ?> <br> <?php
                        if($page->isForm == true){
                            echo Form::checkbox('type', 'Yes', true, array('data-toggle'=>'modal','data-target'=>'#typeModal')); 
                        }else{
                            echo Form::checkbox('type', 'Yes', null, array('data-toggle'=>'modal','data-target'=>'#typeModal')); 
                        } ?> 
                        <div id="typeModal" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Bent u zeker?</h4>
                                    </div>
                                    <div class="modal-body">
                                        <p>Heeft u uw aanpassingen opgeslagen?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id='save' class="btn btn-default" data-dismiss="modal">Change type</button>
                                        <button type="button" id='close' class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>   
                        <?php
                        echo Form::close(); 
                        ?>
                        <script>
                            $('#save').on('click', function(e){
                                var $form=$('#typeform');  
                                $form.submit();
                            });
                            $('#close').on('click',function(){
                                var checkBoxes = $('input[name=type]');
                                checkBoxes.prop("checked", !checkBoxes.prop("checked"));
                            });
                        </script>
                        <?php
                        
                        if($page->isForm == false){
                            // voor gewone pagina's
                        ?>
                        <?php
                        echo Form::open(array('action' => 'PageController@edit'));
                        echo Form::hidden('oldSlug', $page->slug);
                        echo Form::label('', 'Slug van de pagina (wordt de url)'); ?> <br> <?php
                        echo Form::text('slug', $page->slug, array('class'=>'form-control'));
                        echo Form::label('', 'Title van de pagina'); ?> <br> <?php
                        echo Form::text('title', $page->title, array('class'=>'form-control')); ?> <br> <?php
                        echo Form::label('', 'De content van de pagina'); ?> <br> <?php
                        echo Form::textarea('content', $page->content, array('class' => '')); ?> <br> <?php
                        echo Form::submit('Edit page',array('name'=>'btnSubmit','id'=>'btnSubmit','class'=>'btn btn-primary'));
                        echo Form::close(); 
                        }
                        else 
                        {
                        ?>
                        <!--voor forms-->
                        <div id='contactSettings'>
                            <?php
                            $finalArray = explode('>', $page->content);
                            foreach($finalArray as &$part){
                                $part = $part . '>';
                            }
                            //voor mail te verkrijgen om mail input in te vullen
                            $tags = substr($finalArray[0], 35);
                            $parts = explode('"', $tags);
                            $mail = $parts[0];
                            //voor verder de form tags weg te halen en als één string in de editor te plaatsen
                            unset($finalArray[0]);
                            array_pop($finalArray);
                            array_pop($finalArray);
                            $formlessContent = implode('',$finalArray);
                            $decodeContent = htmlentities($formlessContent);
                        
                            echo Form::open(array('action' => 'PageController@edit', 'id'=>'sendForm'));
                            echo Form::hidden('oldSlug', $page->slug);
                            echo Form::hidden('contentb', null, array('id'=>'contentb')); ?> <br> <?php
                            echo Form::label('', 'Slug van de pagina (wordt de url)'); ?> <br> <?php
                            echo Form::text('slug', $page->slug, array('class'=>'form-control')) ?> <br> <?php
                            echo Form::label('', 'Title van de pagina'); ?> <br> <?php
                            echo Form::text('title', $page->title, array('class'=>'form-control')); ?> <br> <?php
                            echo Form::label('', 'De content van de pagina');
                            ?>   
                            <textarea name="contentbox" id='contentbox'><?php echo $decodeContent ?></textarea>
                            <?php 
                            echo Form::submit('Edit page',array('name'=>'btnSubmit','id'=>'btnSubmit','class'=>'btn btn-primary'));
                            echo Form::close(); 
                            ?>
                            <h3>Wordt gemaild naar</h3>
                            <input type='text' id='eigenMail' class='form-control' placeholder='<?php echo $mail?>' value='<?php echo $mail?>'></input>
                        </div>
                    <!--STYLE moet hier blijven want mag enkel gebruikt worden als dit een form page is-->
                    <style>
                        #contentbox{
                            float:left;
                            width: 40%;
                        }
                        #fields{
                            float: left;
                            width: 20%;
                            margin-top: 500px;
                        }
                        #pagewrapper{
                            float:right;
                            width: 80%;
                        }
                        .container{
                            margin: 0px;
                            margin-right: 0px;
                        }
                    </style>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-script')
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script>
$(document).ready(function(){
    // $('#eigenMail').val('');
});

//voor gewone pages
tinymce.init({
    mode : "exact",  
    elements : 'content',
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
    content_css: [
        '{{ url('/') }}/css/syle.css',
        'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css',
    ]
 });

//voor forms
tinymce.init({
    mode : "exact",    
    elements : 'contentbox',
    forced_root_block : false,
    init_instance_callback : 
    function(editor) {  
        $('#textfield').on('click',function(){
            $label = $('#textfieldLabel').val();
            editor.insertContent('<label for="'+$label+'">'+$label+'</label>');
            editor.insertContent('<input style="width:450px" class="form-control" name="'+$label+'" class="'+$label+' form-control" type="text"></input><br>');
         
         });
         
        $('#textarea').on('click',function(){
            $label = $('#textareaLabel').val();  
            editor.insertContent('<label for="'+$label+'">'+$label+'</label>');
            editor.insertContent('<textarea style="width:450px" class="form-control" name="'+$label+'" class="'+$label+' form-control"></textarea><br>');         
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
        $('#btnSubmit').on('click',function(e){
                e.preventDefault();
                $form = $("#contentb");
                $mail = $('#eigenMail').val();
                $form.val('<form id="mailform" action="MAILTO:'+$mail+'" method="post" enctype="text/plain">' + editor.getContent() + '</form>');           
                $('#sendForm').submit();

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
    content_css: [
        '{{ url('/') }}/css/syle.css',
        'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css',
    ]
 });
</script>

@endsection

