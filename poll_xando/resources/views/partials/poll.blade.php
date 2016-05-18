<!--dit is partial die een poll toont waarop kan op gestemd worden en na het stemmen de resultaten toont-->
<?php
$totaleScore = 0;
$options = $poll->options;
$percentageSetting = App\Settings::where('name','percentages')->first();    
?>
<div id='compl<?php echo $poll->id?>'>
<div class='pollvenster<?php echo $poll->id ?>' id='<?php echo $poll->id ?>'>
    <table id='mytable<?php echo $poll->id ?>' class='pollTable table table-sm'> <?php
        echo Form::open(array('id' => 'my-form','action' => array('PollController@postVote', $poll->id)));
        echo Form::hidden('pollId', $poll->id,array('id'=>$poll->id));
        if(count($options)>0){   
        ?>
            <tr>
                <th colspan="3">
                    <h3 class='pollTitle'><a href='/p/<?php echo $poll->id ?>'> <?php echo $poll->name; ?></a></h3>
                </th>
            </tr>
            <?php
            foreach($options as $option){  
                ?>
                <tr> <?php
                $totaleScore += $option->score;
                ?>
                    <td class="col-md-1 pollRadio"> <?php
                    if(Auth::check()){
                        echo Form::radio('votedOption', $option->id, in_array($option->id, $votedByUser),array('id'=>$poll->id));
                    }else{
                        echo Form::radio('votedOption', $option->id, false ,array('id'=>$poll->id, 'disabled'=>true));
                    }
                ?>  </td>
                    <td class="col-md-2">
                        <?php   echo $option->name; ?>
                    </td>
                    <td class="col-md-3">
                        <span class="badge">   
                            <?php
                            if($option->score != 0){  
                            echo Form::label('score', $option->score); 
                            }
                            ?>
                        </span>
                    </td>
                <?php
            }           
        }
        else{  
            ?>
            <tr>
                <th colspan="3">
                    <h3 class='pollTitle'><a href='/p/<?php echo $poll->id ?>'> <?php echo $poll->name; ?></a></h3>
                </th>
            </tr>
            <tr>
                <td>Geen opties voor deze poll!</td>
            </tr>
            <?php    
        }?>          
            <tr> 
                <td colspan="3"> <?php
                    if($percentageSetting->value == 'true'){
                        if($poll->maxVotes != 0 && $poll->maxVotes != -1){
                            echo Form::label('Percentage', floor(($totaleScore/$poll->maxVotes)*100) . "%",array('id'=>'percent'.$poll->id));?> <br> <?php
                        }else if($poll->maxVotes == -1){
                            echo Form::label('Percentage', "no maximum given",array('id'=>'percent'.$poll->id));?> <br> <?php
                        }else{
                            echo Form::label('Percentage', "100%",array('id'=>'percent'.$poll->id));?> <br> <?php
                        }
                    } ?>          
                </td>
            </tr> <?php
        echo Form::close();           
        ?>
        </table>
    </div> 
</div>
<script type="text/javascript">   
$('input[name=votedOption][id="<?php echo $poll->id?>"]:radio').on('change',function( event ) {
    event.preventDefault();           
    $.ajax({
        url: '/poll/vote',
        type: "post",
        data: {'pollId':$('input[name=pollId][id=<?php echo $poll->id ?>]').val(), 
                'votedOption':$('input[name=votedOption][id=<?php echo $poll->id ?>]:checked').val(),
                '_token': $('input[name=_token]').val()},
        success: function(data){
                var html='<table><th><h3 class="pollTitle"><a href="/p/<?php echo $poll->id ?>"><?php echo $poll->name; ?></a></h3></th>';
                var checked = $('input[name=votedOption][id="<?php echo $poll->id?>"]:checked').val();
                <?php 
                $pol = json_encode($options);
                if(Auth::check()){
                    $previous = json_encode($votedByUser);
                }                   
                ?>
                var options = <?php echo $pol; ?>;
                var prevs = <?php if(Auth::check()){echo $previous;}else{echo 'null';}?>;
        
                var totalScore = 0;
                for(var i=0; i< options.length; i++){
                    
                    if(prevs.indexOf(options[i].id) != -1){
                        options[i].score -=1;    
                    }
                    
                    if(options[i].id == checked){
                        options[i].score +=1;   
                    }

                    totalScore += options[i].score;
                }
            
                for(var i=0; i< options.length; i++){
                
                    html += '<tr><td>'+ options[i].name +':</td><td><img src="{{ URL::to("/") }}/images/poll.gif" width="'+(options[i].score/totalScore)*100+'" height="20"></td><td>'+ Math.round(((options[i].score/totalScore)*100)*100)/100 +'%</td></tr>';
                }
                html += '<tr><td></td></tr>';
                
                $(".pollvenster"+<?php echo $poll->id ?>)
                .html($('#mytable<?php echo $poll->id ?>').html(html));     
        }
    });      
}); 
<?php $setting= App\Settings::where('name', 'completedPollLook')->first(); ?>
var percentage = $('#percent'+<?php echo $poll->id?>).text();
// console.log(percentage);
if(<?php echo $setting->value?> == '1' && percentage =='100%' ){
    $('#compl'+<?php echo $poll->id?>).addClass('completed');
}
</script>        

         
    