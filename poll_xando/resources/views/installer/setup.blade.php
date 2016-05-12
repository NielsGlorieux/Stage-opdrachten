@extends('layouts.installer')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Welcome</div>

                <div class="panel-body">
                    Database settings:
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
                    echo Form::open(array('action' => array('InstallerController@databaseSetup')));
                    echo Form::label('', 'database host:');
                    echo Form::text('db_host', null, array('class'=>'form-control'));
                    echo Form::label('', 'database port:');
                    echo Form::text('db_port', null, array('class'=>'form-control'));                 
                    echo Form::label('', 'database name:');
                    echo Form::text('db_database', null, array('class'=>'form-control'));
                    echo Form::label('', 'database username:');
                    echo Form::text('db_username', null, array('class'=>'form-control'));
                    echo Form::label('', 'database password:');
                    echo Form::password('db_password', array('class'=>'form-control'));
                    echo Form::submit('Save and migrate', array('class'=>'form-control'));
                    echo Form::close();            
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
