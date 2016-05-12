@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="pages panel panel-default">
                <div class="panel-heading">Page dashboard</div>
                    
                <div class="panel-body">
                    <a href="{{ url('/admin/pages/create') }}"><button type="button" class="btn btn-primary">Create a new page</button></a>   
                </div>
                <meta name="csrf-token" content="{{ csrf_token() }}">               
                <h3>Edit pages and Navigation</h3>
                <h5><i>Versleep de pagina's in de tabel om hun positie te veranderen in het navigatie menu.</i></h5>
                <table id="sortFixed" class="grid table table-striped table-bordered sorted_table">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Page</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pages as $page){ 
                            ?>
                            <tr>
                                <td id='id'>
                                    <?php echo $page->id?>
                                </td>
                                <td>
                                    <?php echo $page->title ?>
                                </td>
                                <td>
                                    <?php if($page->isForm == true){
                                        echo 'Page with form';
                                    }else{
                                        echo 'Normal page';
                                    }?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <?php
                                        if($page->isStandard == '0'){
                                        ?>
                                        <button type="button" id="" onclick="window.location.href='/page/<?php echo $page->slug ?>'"  class="btn btn-primary btn-sm">
                                            <span class="glyphicon glyphicon-eye-open"></span>
                                        </button>
                                        <button type="button" id="edit_page" onclick="window.location.href='/admin/edit/<?php echo $page->slug ?>'"  class="btn btn-warning btn-sm">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </button>
                                        <button type="button" id="deletePage" onclick="" data-toggle="modal" data-target="#deleteModal<?php echo $page->id?>"  class="btn btn-danger btn-sm">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </button>
                                        <?php
                                        }else{
                                            ?>
                                            <button type="button" id="" onclick="window.location.href='/<?php echo $page->slug ?>'"  class="btn btn-primary btn-sm">
                                                <span class="glyphicon glyphicon-eye-open"></span>
                                            </button>
                                        <?php
                                        }
                                        ?>
                                    </div><?php
                                    echo Form::open(array('action' => array('PageController@deletePage'),'id'=>'form-pagedelete'. $page->id.''));
                                    echo Form::hidden('page_id',$page->id);
                                    ?>
                                    <div id="deleteModal<?php echo $page->id?>" class="modal fade" role="dialog">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">Are you sure?</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Do you want to delete page <b><?php echo $page->title ?></b>?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type='submit' id="delete-btn<?php echo $page->id?>" class="btn btn-danger odom-submit" data-dismiss="modal">Delete</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <?php
                                    echo Form::close();    
                                    ?> 
                                    <script>
                                        // dit moet erbij omdat de delete button in de modal anders niet submit
                                        $('#delete-btn<?php echo $page->id?>').on('click', function(e){
                                            var $form=$('#form-pagedelete<?php echo $page->id?>');  
                                            $form.submit();
                                        });
                                    </script>                      
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-script')
<script  src="https://code.jquery.com/ui/1.12.0-rc.2/jquery-ui.min.js"   integrity="sha256-55Jz3pBCF8z9jBO1qQ7cIf0L+neuPTD1u7Ytzrp2dqo="   crossorigin="anonymous"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.0/knockout-min.js'></script>
<script>
$('td, th', '#sortFixed').each(function () {
    var cell = $(this);
    cell.width(cell.width());
});

$('#sortFixed tbody').sortable({
    axis: 'y',
    update: function (event, ui) {
        var data ='';
        var table = $('#sortFixed td#id').each(function(){
            data += $(this).context.outerText + ',';
        });
        console.log(data);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            data: {'pages':data},    
            type: 'POST',
            url: '/admin/pages'
        });
    }
}).disableSelection();
</script>

@endsection