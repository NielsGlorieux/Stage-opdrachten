<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Http\Requests;
use App\AFCategory;
use App\AFTopic;
use App\AFReply;
use Illuminate\Http\RedirectResponse;
class ForumController extends Controller
{
   
    public function showForumCategories(){
        
        $cats = AFCategory::all();
        return view('theme::admin.forum.categories.categories', ['cats' => $cats] );
    }
    public function showCreateCategory(){
        return view('theme::admin.forum.categories.createcategory');
    }
    
     public function createCategory(Request $request){
        $this->validate($request,[
            'name'=> 'required',
        ]);
        $cat = new AFCategory;
        $cat->name = $request->name;
        $cat->description = $request->description;
        $cat->save();
        return redirect()->action('ForumController@showCategory', [$cat->id]);
     }
     
     public function showCategory($id){
         $cat = AFCategory::where('id',$id)->first();
        //  var_dump($id);
        $topics =$cat->topics;
      
      
         return view('theme::admin.forum.categories.categorydetail', ['cat'=>$cat, 'topics' => $topics]);
         
     }
     
     public function showCreateTopic($name){
        $cat = AFCategory::where('name', $name)->first();
        
        return view('theme::admin.forum.topics.createtopic',['cat'=>$cat]);
     }
     
    public function createTopic(Request $request){
        $this->validate($request,[
            'subject'=> 'required',
        ]);
        $topic = new AFTopic;
        $topic->subject = $request->subject;
        $topic->content = $request->content;
        $topic->user_id = Auth::user()->id;

        $topic->a_f_category_id = $request->cat;
        $topic->save();
       
        return redirect()->action('ForumController@showTopic', [$request->cat,$topic->id]);
     }
     
    public function showTopic($name, $topic){
        $top = AFTopic::where('id', $topic)->first();
        $replies = $top->replies;
        return view('theme::admin.forum.topics.topicdetail',['topic'=>$top, 'replies' => $replies]);
    }
    
    public function createReply(Request $request){
        $this->validate($request,[
        'body'=> 'required',
        ]);
        
        $reply = new AFReply;
        $reply->user_id = Auth::user()->id;
        $reply->a_f_topic_id = $request->topic_id;
        $reply->content = $request->body;
        $reply->save();
        return back();
    }
    public function deleteCat(Request $request){
        AFCategory::destroy($request->cat_id);
        return back();    
    }
        
    public function deleteTopic(Request $request){
        AFTopic::destroy($request->topic_id);
        return back();
    }
}
