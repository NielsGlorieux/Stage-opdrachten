<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Http\Requests;
use App\UFCategory;
use App\UFTopic;
use App\UFReply;
use Illuminate\Http\RedirectResponse;

class UserForumController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }
    
    public function index(){
        
        return view('theme::forum.forum');
    }
    
    //toont tabel met alle categorieen
    public function showForumCategories(){
        
        $cats = UFCategory::all();
        return view('theme::forum.categories.categories', ['cats' => $cats] );
    }
    
    //toont create form voor nieuwe category
    public function showCreateCategory(){
        return view('theme::forum.categories.createcategory');
    }
    
    //creeert nieuwe category
    public function createCategory(Request $request){
        $this->validate($request,[
            'name'=> 'required',
        ]);
        $cat = new UFCategory;
        $cat->name = $request->name;
        $cat->description = $request->description;
        $cat->zichtbaar = true;
        $cat->save();
        return redirect()->action('UserForumController@showCategory', [$cat->id]);
     }
     
    //toont toebehorende topics van category
    public function showCategory($id){
        $cat = UFCategory::where('id',$id)->first();
        $topics =$cat->topics;
        return view('theme::forum.categories.categorydetail', ['cat'=>$cat, 'topics' => $topics]);  
     }
     
    //creeer een nieuw topic
    public function showCreateTopic($id){
        $cat = UFCategory::find($id)->first();
        
        return view('theme::forum.topics.createtopic',['cat'=>$cat]);
     }
     
    //creeert topic 
    public function createTopic(Request $request){
        $this->validate($request,[
            'subject'=> 'required',
        ]);
        
        $topic = new UFTopic;
        $topic->subject = $request->subject;
        $topic->content = $request->content;
        $topic->user_id = Auth::user()->id;
        $topic->u_f_category_id = $request->cat;
        $topic->save();
       
        return redirect()->action('UserForumController@showTopic', [$request->cat,$topic->id]);
     }
     
    //toont topic en commentaren
    public function showTopic($name, $topic){
        $top = UFTopic::where('id', $topic)->first();
        $replies = $top->replies;
        
        return view('theme::forum.topics.topicdetail',['topic'=>$top, 'replies' => $replies]);
     }
    
    //post comment op forum bericht
    public function createReply(Request $request){
        
        $this->validate($request,[
            'body'=> 'required',
        ]);
         
        $reply = new UFReply;
        $reply->user_id = Auth::user()->id;
        $reply->u_f_topic_id = $request->topic_id;
        $reply->content = $request->body;
        $reply->save();
        
        return back();
     }
    
    //stel in of category mag gezien worden door guests
    public function changeCatWatch(Request $request){
        $cat = UFCategory::where('id', $request->cat_id)->first();
  
        if($cat->zichtbaar == true){
            $cat->zichtbaar = false;
            $cat->save();
        }
        else{
            $cat->zichtbaar = true;
            $cat->save();
        }
        
        return back();
     }
    
    //verwijder een category
    public function deleteCat(Request $request){
        UFCategory::destroy($request->cat_id);
        
        return back();    
    }
    
    //verwijder een topic 
    public function deleteTopic(Request $request){
        UFTopic::destroy($request->topic_id);
        
        return back();
     }
}
