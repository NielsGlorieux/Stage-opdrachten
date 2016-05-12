@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><?php echo $user->name; ?></div>
                <div class="panel-body">
                 <?php 
                 if(Auth::check() && Auth::user()->is('admin') == true){
                        echo Form::open(array('action' => array('AdminController@blockUser')));
                        echo Form::hidden('user_id',$user->id);
                        echo Form::label('', 'Block user');
                        if($user->blocked==true){
                            echo Form::checkbox('disable', 'Yes',true, ['onChange'=>'this.form.submit()']);
                        }else{
                            echo Form::checkbox('disable', 'Yes', null, ['onChange'=>'this.form.submit()']);
                        }
                        // echo Form::submit('Save', array('class'=>'btn btn-primary'));
                        echo Form::close();              
                 }
               ?>
               </div>
                <div class="panel-body">
                    <div class="col-lg-12 col-sm-12">
                        <div class="card hovercard"> 
                            <div class="card-info"> 
                                <span class="card-title"><?php echo $user->name?></span>
                                <span class="card-title">status: <?php if($user->is('admin') == true){
                                    echo 'Admin';
                                    }else{
                                    echo 'User';
                                    }
                                    ?></span>
                            </div>
                        </div>
                        <div class="btn-pref btn-group btn-group-justified btn-group-lg" role="group" aria-label="...">
                            <div class="btn-group" role="group">
                                <button type="button" id="stars" class="btn btn-primary" href="#tab1" data-toggle="tab">
                                    <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                                    <div class="hidden-xs"><?php echo $user->name?>'s polls</div>
                                </button>
                            </div>
                            <div class="btn-group" role="group">
                                <button type="button" id="favorites" class="btn btn-default" href="#tab2" data-toggle="tab">
                                    <span class="glyphicon glyphicon-heart" aria-hidden="true"></span>
                                    <div class="hidden-xs">voted on</div>
                                </button>
                            </div>
                            <?php if($user->id == Auth::user()->id){ ?>
                            <div class="btn-group" role="group">
                                <button type="button" id="following" class="btn btn-default" href="#tab3" data-toggle="tab">
                                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                                    <div class="hidden-xs">Zoek naar anderen</div>
                                </button>
                            </div>
                                <?php }?>
                        </div>
                        <div class="well">
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="tab1">
                                    <?php foreach ($polls as $poll){
                                    ?>
                                    @include('partials.poll')  
                                    <?php         
                                    } ?>
                                </div>
                                <div class="tab-pane fade in" id="tab2">
                                    <?php foreach ($user->pollsVoted as $poll) {     
                                    // echo $poll->name;
                                    ?> 
                                        @include('partials.poll')  <?php
                                    } ?>
                                </div>
                                <div class="tab-pane fade in" id="tab3">
                                        <!--user zoeken-->
                                    <div id='searchUser'>
                                            <?php
                                            echo Form::open(array('action' => array('HomeController@searchUser')));
                                            echo Form::text('term', null, array('class'=>'form-control','id'=>'search', 'placeholder'=> 'search a user..', 'style'=>'width: 250px;'));
                                            echo Form::close();
                                            ?>
                                    </div>
                                    <table id='searchTable' class='table'>
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>View profile</th>
                                            </tr>
                                        </thead>
                                        <tbody id='searchBody'>  
                                        </tbody>
                                    </table>    
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>  
@endsection   
@section('page-script')
<script type="text/javascript">
$(document).ready(function() {
    $(".btn-pref .btn").click(function () {
        $(".btn-pref .btn").removeClass("btn-primary").addClass("btn-default");
        $(this).removeClass("btn-default").addClass("btn-primary");   
    });
});

$('#search').keypress(function(e){
    if(e.which  == 13){
        e.preventDefault();    
        $.ajax({
            url: '/u/search',
            type: "post",
            data: {'term':$('input[name=term]').val(), 
                    '_token': $('input[name=_token]').val(),
                },
                success: function(data){
                    $('#searchBody').empty();
                    if(jQuery.isEmptyObject(data)){
                        $('#searchBody').append('<tr><td>geen resultaten..</td><td></td></tr>');    
                    }
                    $.each(data,function(i, item){
                        $('#searchBody').append('<tr><td>'+data[i].name+'</td><td><a class="glyphicon glyphicon-user" href="/u/'+data[i].id+'"></a></td></tr>');    
                    });
                }
        });
    }         
}); 
</script>
@endsection
