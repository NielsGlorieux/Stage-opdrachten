@extends('layouts.dashboard')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
                <div id="wrapper">
                    <div id ="chatcontent">
                        <?php
                        //haalt oude berichten op uit de database en plaatst ze in het chat venster
                        foreach($messages as $message){
                            $sender = App\User::find($message->sender_id);
                            if(isset($sender)){
                               if(Auth::user() == $sender){
                                    echo "<p id='self'><b>You</b>: " . $message->message ."</p>";
                                }else{
                                    echo "<p id='sender'><b>". $sender->name."</b>: " . $message->message ."</p>";
                                } 
                            }else{
                                echo "<p id='sender'><b>Deleted</b>: " . $message->message ."</p>";
                            }
                            
                        }
                        ?>
                    </div>
                    <div id="message">
                        <form id="userarea">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input  class='form-control textarea' id="textareamsg" name="messages" rows="2" cols="30" required="" placeholder="Typ een bericht.."></input>
                        </form>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection
@section('page-script')
<script>
    // voor firefox socket probleem
// $(window).on('beforeunload', function(){
//     socket.close();
// });
var objDiv = document.getElementById("chatcontent");
objDiv.scrollTop = objDiv.scrollHeight;
    // plaats eigen net getypte berichten in het chatvenster en verzend ze
    $(document).ready(function(){
        $('#chatcontent').append( $(this).data('value'));
        $('.textarea').bind("enterKey",function(event){
            var textValue = $('#textareamsg').val();
            if(textValue == "" || textValue == " " || textValue == "  " || textValue == "   "){
                alert('You have to write something in the textarea to post a message');
                return false;
            }else{
                $('#chatcontent').append("<p id='self'><b>You</b>: " + textValue + "</p>");

                $('#textareamsg').val('');

                event.preventDefault();           
                $.ajax({
                    url: '/admin/sendChat',
                    type: "post",
                    data: {'clientmsg':textValue,
                            '_token': $('input[name=_token]').val()
                        },
                }); 
            }
            var objDiv = document.getElementById("chatcontent");
            objDiv.scrollTop = objDiv.scrollHeight;
        });
        $('.textarea').keypress(function(e){
            if(e.keyCode == 13)
            {
                $(this).trigger("enterKey");
            }
        });    
    });
//luisterd naar nieuwe berichten van andere users en plaatst ze in het chat venster
$(function(){ 
    var socket = io('http://192.168.100.10:3000');
    socket.on("test-channel:App\\Events\\ChatMessageEvent", function(message){
        if(message.data.user != '{{ Auth::user()->name }}' ){
            $('#chatcontent').append("<p id='sender'><b>"+ message.data.user +"</b>: " + message.data.message + "</p>");
            var objDiv = document.getElementById("chatcontent");
            objDiv.scrollTop = objDiv.scrollHeight;
        }
    });
});
</script>
@endsection