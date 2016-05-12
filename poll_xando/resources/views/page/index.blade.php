@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><?php echo $page->title ?></div>
                    <div class="panel-body">
                        <?php echo $page->content ?>
                    </div>
                    <?php
                        if(Auth::check() && Auth::user()->is('admin') == true){
                            ?> <br>
                            <a href='/admin/edit/<?php echo $page->slug ?>'>edit page</a>
                            <?php
                        }
                        ?>
                </div>
            </div>
        </div>
    </div>
</div>             
   
@endsection
