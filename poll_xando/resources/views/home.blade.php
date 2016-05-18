@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Home</div>
                <div class="panel-body">
                    <!--[shortcode_hello]-->

                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa glyphicon glyphicon-th-large fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"></div>
                                    <div>Polls</div>
                                </div>
                                </div>
                            </div>
                            <a href="/polls">
                                <div class="panel-footer">
                                <span class="pull-left">View Polls</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                     
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa glyphicon glyphicon-check fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"></div>
                                    <div>New Poll</div>
                                </div>
                                </div>
                            </div>
                            <a href="/poll/create">
                                <div class="panel-footer">
                                <span class="pull-left">Make a new poll</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                     
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa glyphicon glyphicon-book fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div>Forum</div>
                                </div>
                                </div>
                            </div>
                            <a href="/forum">
                                <div class="panel-footer">
                                <span class="pull-left">View Forum</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>       
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
