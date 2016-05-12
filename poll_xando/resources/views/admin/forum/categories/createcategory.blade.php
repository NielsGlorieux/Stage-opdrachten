@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">User forum - create category</div>
                    <div class='createCat'>
                        <h2>Create a new category</h2>
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
                        echo Form::open(array('action' => array('ForumController@createCategory')));
                        echo Form::label('', 'name:'); ?> <br>  
                        <div class="col-xs-3"> <?php
                        echo Form::text('name',null, array('class'=>'form-control input-xs')); ?>
                        </div> <br><br> <?php
                        echo Form::label('', 'description:'); ?> <br> <?php
                        echo Form::textarea('description',null, array('class'=>'form-control input-xs', 'rows'=>'3')); ?><br> <?php
                        echo Form::submit('Save', array('class'=>'btn btn-primary')); ?> <br> <?php
                        echo Form::close();  
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection