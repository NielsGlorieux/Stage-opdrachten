@extends('layouts.dashboard')
@section('page-css')
<script src="{{ url('/') }}/js/chat.js"></script>

@endsection
@section('content')
<div class="container">
   <div class="row">
      <div class="col-md-10 col-md-offset-1">
         <div class="panel panel-default">
            <div class="panel-heading">Dashboard</div>

            <div class="panel-body">
               <div class="col-lg-3 col-md-6">
                  <div class="panel panel-default">
                     <div class="panel-heading">
                        <div class="row">
                           <div class="col-xs-3">
                              <i class="fa glyphicon glyphicon-th-large fa-5x"></i>
                           </div>
                           <div class="col-xs-9 text-right">
                              <div class="huge"><?php echo App\Page::count();  ?></div>
                              <div>Pages</div>
                           </div>
                        </div>
                     </div>
                     <a href="/admin/pages">
                        <div class="panel-footer">
                           <span class="pull-left">View Pages</span>
                           <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                           <div class="clearfix"></div>
                        </div>
                     </a>
                  </div>
               </div>
               
               <div class="col-lg-3 col-md-6">
                  <div class="panel panel-danger">
                     <div class="panel-heading">
                        <div class="row">
                           <div class="col-xs-3">
                              <i class="fa glyphicon glyphicon-user fa-5x"></i>
                           </div>
                           <div class="col-xs-9 text-right">
                              <div class="huge"><?php echo App\User::count();  ?></div>
                              <div>Users</div>
                           </div>
                        </div>
                     </div>
                     <a href="/admin/users">
                        <div class="panel-footer">
                           <span class="pull-left">View Users</span>
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
                              <div class="huge"><?php echo App\Poll::count();  ?></div>
                              <div>Polls</div>
                           </div>
                        </div>
                     </div>
                     <a href="/admin/polls">
                        <div class="panel-footer">
                           <span class="pull-left">View Polls</span>
                           <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                           <div class="clearfix"></div>
                        </div>
                     </a>
                  </div>
               </div>
               
               <div class="col-lg-3 col-md-6">
                  <div class="panel panel-success">
                     <div class="panel-heading">
                        <div class="row">
                           <div class="col-xs-3">
                              <i class="fa glyphicon glyphicon-picture fa-5x"></i>
                           </div>
                           <div class="col-xs-9 text-right">
                              <?php $path = base_path() . '/public/themes';
                                 $directories = array_map('basename', File::directories($path));
                                 
                                 ?>
                              <div class="huge"><?php echo count($directories);?></div>
                              <div>Themes</div>
                           </div>
                        </div>
                     </div>
                     <a href="/admin/themes">
                        <div class="panel-footer">
                           <span class="pull-left">View Themes</span>
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
                     <a href="/admin/forum">
                        <div class="panel-footer">
                           <span class="pull-left">View Forum</span>
                           <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                           <div class="clearfix"></div>
                        </div>
                     </a>
                  </div>
               </div>
               
               <div class="col-lg-3 col-md-6">
                  <div class="panel panel-warning">
                     <div class="panel-heading">
                        <div class="row">
                           <div class="col-xs-3">
                              <i class="fa glyphicon glyphicon-list-alt fa-5x"></i>
                           </div>
                           <div class="col-xs-9 text-right">
                              <div>Chat</div>
                           </div>
                        </div>
                     </div>
                     <a href="/admin/chat">
                        <div class="panel-footer">
                           <span class="pull-left">View Chat</span>
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
@section('page-script')

@endsection