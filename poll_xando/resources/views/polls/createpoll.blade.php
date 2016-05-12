@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Create a new poll</div>
                <div class="panel-body createPoll">
                    <h3>New poll</h3>
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
                    echo Form::open(array('action' => 'PollController@createPoll'));        
                    echo Form::label('', 'Name:');?><br><?php
                    echo Form::text('name','',array('class'=>'form-control'));?><br><?php
                    echo Form::label('', 'Choose a category:');?><br><?php
                     ?>
                    <select id='drop' class='form-control' name='cat'>
                        <option disabled selected value> -- select an option -- </option>
                        <?php 
                        foreach($categories as $category){  
                            ?>
                            <option value='<?php echo $category->id ?>'><?php echo $category->name ?></option> 
                            <?php 
                        }
                        ?>
                    </select>
                    <br>
                    <?php 
                    echo Form::label('', 'Give the options:');?><br><?php
                    ?>                    
                    <ul id='options' name='options'>
                        <li>
                            <div class='form-group'>
                                <label>Option1</label>
                                <input type='text' name='option[]' id='option1' class='form-control'></input>
                            </div>
                        </li>
                    </ul>
                    <?php
                    echo Form::submit('Add poll', array('class'=>'btn btn-primary'));
                    echo Form::close();
                    ?>  
                </div>
            </div>
        </div>
    </div>
</div>             
@endsection
@section('page-script')
        <script type="text/javascript">
            var teller = 2;                                                           
            $('body').on('keydown', '#options li:last' , function(i){
                $('#options').append($("<li id='option'><div class='form-group'><label>Option "+ teller +"</label><input type='text' name='option[]' id='option"+ teller + "' class='form-control'></input></div></li>"));                            
                teller ++;          
            });
        </script> 
@endsection
<style>
    input[type='text'], #drop{
        width:23%;
    }
</style>



                                    