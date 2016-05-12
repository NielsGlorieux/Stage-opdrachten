<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Http\Requests;
use App\AFCategory;
use App\AFTopic;
use App\AFReply;
use App\ChatMessage;
use Illuminate\Http\RedirectResponse;
use App\Events\ChatMessageEvent;

class ChatController extends Controller
{
    //toont de chat pagina in backend
    public function index(){
        $messages = ChatMessage::all();
        return view('theme::admin/dashboard/chat', ['messages'=>$messages]);
    }
    
    //verstuurt een message met gebruikt van nodejs, socket.io en redis
    public function sendMessage(Request $request){
        $m = new ChatMessage;
        $m->sender_id = Auth::user()->id;
        $m->message = $request->clientmsg;
        $m->save();
        
        event(new ChatMessageEvent($request->clientmsg,Auth::user()->name));

    }
  
  
}


