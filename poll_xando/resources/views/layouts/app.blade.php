<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
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
                    Polls
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <?php 
                    $pagesSetting = App\Settings::where('name','navOrder')->first()->value;
                    $settingPages = json_decode($pagesSetting);
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

                    if(Auth::check()){   ?>
                        <li><a href="/u/<?php echo Auth::user()->id ?>">My profile</a></li>
                    <?php
                    }

                    if(Auth::check() && Auth::user()->is('admin') == true){?>
                        <li><a href="/admin">Admin <span id ='navChat' class="badge"></a></li>
                    <?php
                    }
                    ?>    
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    @yield('content')

    <!-- JavaScripts -->

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.4.5/socket.io.js"></script>
    <?php if(Auth::check() && Auth::user()->is('admin') == true){ ?>
    <script>
        $(function(){ 
            var socket = io('http://192.168.100.10:3000');
            socket.on("test-channel:App\\Events\\ChatMessageEvent", function(message){
                if(message.data.user != '{{ Auth::user()->name }}' ){
                    // $('#chatNav').html('New message!');
                    $('#navChat').html('new message!');
                }
            });
        });
    </script>
    <?php } ?>
    @yield('page-script')
</body>
</html>
