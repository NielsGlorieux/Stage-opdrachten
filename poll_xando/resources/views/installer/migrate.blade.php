@extends('layouts.installer')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Make an admin</div>
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
                    <h1>The first admin account</h1>
                     <?php
                    echo Form::open(array('action' => array('InstallerController@createAdmin')));
                    echo Form::label('', 'username:');
                    echo Form::text('username', null, array('class'=>'form-control'));
                    
                    echo Form::label('', 'email:');
                    echo Form::text('email', null, array('class'=>'form-control'));
                
                    echo Form::label('', 'password:');
                    echo Form::password('password',array('class'=>'form-control'));                 
                    
                    echo Form::label('', 'password confirmation:');
                    echo Form::password('password_confirmation',array('class'=>'form-control')); 
                                     
                    echo Form::submit('Next', array('class'=>'form-control'));
                    echo Form::close();            
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
