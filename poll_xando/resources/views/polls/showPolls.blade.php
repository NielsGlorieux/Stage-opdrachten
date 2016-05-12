@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">All polls</div>
                <div id='addPoll'>
                    <a href='/poll/create' class='glyphicon glyphicon-plus'></a>
                </div>
                <?php 
                //huidige aangemelde opvragen voor huidige stemmen te tonen, dit moet voor de partial gebeuren, anders wordt dit elke keer opnieuw gequeried
                if(Auth::check()){
                    $user = Auth::user(); 
                    $votedByUser = array();
                    foreach($user->pollsVoted as $vote){
                        array_push($votedByUser, $vote->pivot->votedOption);  
                    }
                }
                ?>
                <div class="panel-body"> 
                    <?php               
                    //polls per category             
                    foreach ($categories as $cat){     
                        ?>
                        <h3><?php echo $cat->name?></h3>
                        <?php
                        foreach($cat->polls()->get() as $poll){
                                ?>
                            @include('partials.poll')
                    <?php
                            }               
                        }  
                    ?>  
                    <div class="text-center">
                        <nav>   
                            <?php 
                            echo $categories->links() ?>                       
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
