<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--<meta name="csrf-token" content="{{ csrf_token() }}" />-->

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
        <link rel="stylesheet" href="{{ url('/') }}/css/syle.css" type="text/css"/>
    @yield('page-css')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    Terug
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <!--<li><a href="{{ url('/home') }}">Home</a></li>
                    <li><a href="{{ url('/polls') }}">All polls</a></li>
                    <li><a href="{{ url('/poll/create') }}">New poll</a></li>
                    <li><a href="{{ url('/forum') }}">Forum</a></li>
                    <li><a href="{{ url('/inbox') }}">Inbox</a></li>-->
                    <?php 
                    $pagesSetting = App\Settings::where('name','navOrder')->first()->value;
                   
                        $settingPages = json_decode($pagesSetting);
                        // var_dump(json_encode($settingPages));
                        $placeholders = implode(',',array_fill(0, count($settingPages), '?')); 
                        $pags = App\Page::whereIn('id', $settingPages)->orderByRaw("field(id,{$placeholders})", $settingPages)->get();
                        foreach($pags as $page){
                            if($page->isStandard == true){
                            ?>
                                <li><a href="/<?php echo $page->slug; ?>"><?php echo $page->title; ?></a></li>
                            <?php
                            }
                            else{
                                ?>
                                <li><a href="/page/<?php echo $page->slug; ?>"><?php echo $page->title; ?></a></li>
                            <?php
                            }                      
                        }
                    // }
                    ?>

                    <li><a href="/u/<?php echo Auth::user()->id ?>">My profile</a></li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

  
    <div id="header">
    <h1>Dashboard</h1>
    </div>

    <div id="nav">
    <a href="/admin">Home</a><br>
    <a href="/admin/pages">Pages</a><br>
    <a href="/admin/users">Users</a><br>
    <a href="/admin/polls">Polls</a><br>
    <a href="/admin/themes">Themes</a><br>
    <a href="/admin/forum">Forum</a><br>
    <a href="/admin/chat">Chat</a><br>
    </div>

    <div id="section">
    
        @yield('content')
    </div>


                
                <style>
                #header {
                    /*background-color:black;*/
                    color:black;
                    text-align:center;
                    padding:5px;
                }
                #nav {
                    line-height:30px;
                    background-color:#eeeeee;
                    height:100%;
                    width:100px;
                    float:left;
                    padding:5px; 
                }
                #section {
                    width:350px;
                    float:left;
                    padding:10px; 
                }
                #footer {
                    background-color:black;
                    color:white;
                    clear:both;
                    text-align:center;
                    padding:5px; 
                }
                </style>


    <!-- JavaScripts -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
   
    @yield('page-script')
</body>
</html>
