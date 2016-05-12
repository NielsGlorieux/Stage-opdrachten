<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Category;
use App\Poll;
use App\Comment;
use App\Option;
use Auth;
use DB;
class PollController extends Controller
{
    //in deze controller zitten alle poll gerelateerde functies die door alle users kunnen gebruikt worden
    public function __construct()
    {
        // $this->middleware('auth');
    }

    //toont de poll creatie view
    public function create()
    {
        $categories = Category::all();
        
        return view('theme::polls.createpoll' ,['categories'=>$categories]);
    }
    
    //voor poll creatie
    public function createPoll(Request $request)
    {
        $this->validate($request,[
            'name'=> 'required|max:100|regex:/^[(a-zA-Z\s!?.,)]+$/u',
            'cat'=>'required'
        ]);

        $id = Auth::user()->id;  
        
        $poll = new Poll;
        $poll->user_id = $id;
        $poll->name = $request->name;
        $poll->category_id = $request->cat;  
        $poll->haveComments = 1;
        $poll->maxVotes = -1;
        $poll->save();
        
        //dit voegt de ingevoerde opties toe aan de nieuwe poll
        //als er geen opties zijn aangegeven zal een optie worden gemaakt met dezelfde naam als de title van de poll
        if(!count(array_filter($request->option)) == 0){
            foreach($request->option as $option){  
                if($option != ''){
                    $option1 = new Option;
                    $option1->name = $option;
                    $option1->poll_id = $poll->id;
                    $poll->options()->save($option1);
                }
                
            }
        }else{
            $option1 = new Option;
            $option1->name = $request->name;
            $option1->poll_id = $poll->id;
            $poll->options()->save($option1);
        }  
        
        return redirect()->action('PollController@showPoll', [$poll->id]);

    }
    
    
    
    //In de edit modal kunnen ook bestaande opties worden verwijdert
    public function deleteOption(Request $request){
        Option::destroy($request->option_id);
        DB::table('users_polls')->where('votedOption', '=', $request->option_id)->delete();
        return back();
    }
    
    //toont de All polls pagina
    public function showPolls()
    {   
        $categories = Category::has('polls')->paginate(1);
        return view('theme::polls.showPolls',['categories'=>$categories]);
    }
    
    //toont een bepaalde poll met zijn comments
    public function showPoll($id)
    {
        $poll = Poll::where('id',$id)->first();
        $comments = $poll->comments;      
        return view('theme::polls.showSinglePoll',['poll'=>$poll, 'comments'=>$comments]);
    }
    
    //om te stemmen op een poll
    public function postVote(Request $request){
        $user = Auth::user();
        $pollId = $request->pollId;
        $poll = Poll::find($pollId);

        $usersPoll = array();
        foreach($poll->usersVoted as $u){
            array_push($usersPoll, $u->name);
        }
        
        //huidige geselecteerde optie van user, zodat deze kan worden verwijdert als de user op een andere optie stemt
        if(in_array($user->name, $usersPoll)){
            $prevSelected = $user->pollsVoted()->where('users_polls.poll_id', $pollId)->first()->pivot->votedOption;
        }
             
        //als de user nog niet eerder gestemd had op deze poll
        if(!in_array($user->name, $usersPoll)){
            if($request->votedOption != ''){
                $optionVoted = $request->votedOption;
                
                $optie = Option::find($optionVoted);
                $optie->score += 1;
                $optie->save(); 
                $poll->usersVoted()->attach($user->id, ['votedOption' => $optionVoted]);
            }
           
        }
        //als de user wel al eerder gestemd had wordt zijn oude stem verwijdert en zijn nieuwe toegevoegd 
        else if($request->votedOption != strval($prevSelected)){
            //vorige zijn score verminderen
            $vorige = Option::find($prevSelected);
            $vorige->score -=1;
            $vorige->save();
            //nieuwe zijn score vermeerderen
            $optionVoted = $request->votedOption;
            $optie = Option::find($optionVoted);
            $optie->score += 1;
            $optie->save(); 
            $poll->usersVoted()->updateExistingPivot($user->id, ['votedOption' => $optionVoted]);
        }  

        return back();
    }
    
    //om een commentaar te plaatsen bij een poll
    public function postComment(Request $request){
        $comment = new Comment;
        $userId = Auth::user()->id;
        $body = $request->body;
        $comment->body= $body;
        $pollId = $request->pollIdee;
        $comment->poll_id = $pollId;
        $comment->user_id = $userId;
        $comment->level = $request->level;
        $comment->parent_id = $request->parent_id;
        $comment->save();
    
        return back();
    }
    
  
    
}
