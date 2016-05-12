<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Poll;
use App\User;
use Response;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('theme::home');
    }

    public function showProfile($id){
        $user = User::find($id);
        $id = $user->id;
        $polls = Poll::with('options')->where('user_id',$id)->get();
        $votedByUser = array();
        foreach($user->pollsVoted as $vote){
            array_push($votedByUser, $vote->pivot->votedOption);  
        }
        return view('theme::userprofile', ['user'=>$user, 'polls'=>$polls, 'votedByUser'=>$votedByUser]);
    }

   
   public function searchUser(Request $request){
        $keyword = $request->term;
        $users = User::SearchByKeyword($keyword)->get();
        return  Response::json($users);

   }
    
}
