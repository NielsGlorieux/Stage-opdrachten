<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PrivateMessage;
use App\Http\Requests;
use Auth;
use App\User;
class PrivateMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    //toont inbox pagina
     public function inbox(){
        $user = Auth::user();
        $inboxMessages = $user->ReceivedMessages()->orderBy('created_at', 'desc')->paginate(15);
        return view('theme::private_messages.inbox',['user'=>$user,'inbox'=> $inboxMessages]);
    }
    // toont outbox pagina
    public function outbox(){
        $user = Auth::user();
        $outboxMessages = $user->SendMessages()->where('gelezen', 0)->orderBy('created_at', 'desc')->paginate(15);
        return view('theme::private_messages.outbox',['user'=>$user,'outbox'=> $outboxMessages]);
    }
    // toont sendbox pagina
    public function sendbox(){
        $user = Auth::user();
        $sendboxMessages = $user->SendMessages()->where('gelezen', 1)->orderBy('created_at', 'desc')->paginate(15);
        return view('theme::private_messages.sendbox',['user'=>$user,'sendbox'=> $sendboxMessages]);
    }
    // toont een bericht
    public function message($id){
        
        $message= PrivateMessage::find($id);
        
        return view('theme::private_messages.message', ['message'=>$message]);
    }
    // toont pagina om bericht te sturen
    public function sendMessagepage(){
        
        return view('theme::private_messages.sendmessage');
    }
    // vertuurt bericht
    public function sendMessage(Request $request){

        $this->validate($request,[
            'usernames.*'=> 'bail|required|exists:users,name',
            'title'=>'required',
            'body'=>'required'
        ]);
        var_dump($request->usernames);
        foreach($request->usernames as $u){
            if($u != ''){
            var_dump($u);
            $message = new PrivateMessage;
          
            $user = User::where('name', $u)->first();
           
            $message->ontvanger_id = $user->id;
            $message->verstuurder_id = Auth::user()->id;
            $message->title = $request->title;
            $message->body = $request->body;
            $message->gelezen = false;
            $message->save(); 
            }
        }
        return back();
    }
    
    
}
