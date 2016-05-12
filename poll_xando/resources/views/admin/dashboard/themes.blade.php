@extends('layouts.dashboard')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Themes</div>
                <div class="panel-body">
                    <?php 
                    echo Form::open(array('id' => 'my-form','action' => array('AdminController@chooseTheme')));       
                    foreach($themes as $theme){
                        if($huidigTheme == $theme){
                            echo Form::radio('chosenTheme', $theme, true);
                        }else{
                            echo Form::radio('chosenTheme', $theme);
                        }
                        echo Form::label('', $theme); ?> <br> <?php
                    }
                    if($huidigTheme ==''){
                        echo Form::radio('chosenTheme', '' , true);
                    }else{
                        echo Form::radio('chosenTheme', '' );
                    }
                    echo Form::label('', 'Geen theme'); ?> <br> <?php
                    echo Form::submit('Change theme', array('class'=>'btn btn-primary'));
                    echo Form::close();           
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
