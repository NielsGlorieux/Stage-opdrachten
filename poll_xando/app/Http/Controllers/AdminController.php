<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Role;
use App\Poll;
use App\Settings;
use App\Page;
use File;
use App\Category;
use App\Option;
class AdminController extends Controller
{
    //in deze controller zitten alle functies die enkel door users met een admin role kunnen uitgevoerd worden
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    // //toon 
    // public function admin(){
    //     $percentageSetting = Settings::where('name','percentages')->first();
    //     return view('admin/adminpage', ['percentageSetting'=> $percentageSetting]);
    // }
    
    //toont landing pagina van backend
    public function adminPage(){
        return view('theme::admin/dashboard/adminpage');
    }
    
    //zet commenting af bij polls
    public function disableComments(Request $request, $id){
        $poll = Poll::where('id',$id)->first();
        if($request->disable == 'Yes'){
           
            $poll->haveComments = true;
            $poll->save();
        }else{
            $poll->haveComments = false;  
            $poll->save();
        }
        return back();
    }
    
    //zet het maximum aantal stemmen een vote kan krijgen
    public function setMaxVotes(Request $request){
        $poll = Poll::where('id',$request->pollId)->first();
        $poll->maxVotes = $request->maxVotes;
        $poll->save();
        return back();
    }
    
    //zet het maximum aantal geneste comments
    public function setMaxLevel(Request $request){
        $poll = Poll::where('id',$request->pollId)->first();
        $poll->maxLevelComments = $request->maxLevel;
        $poll->save();
        return back();
    }
    
    //zet het tonen van percentage bij polls af of aan
    public function disablePercentage(Request $request){
        
        $settings = Settings::where('name','percentages')->first();
        if($request->disable == 'Yes'){      
            $settings->value = 'true';
            $settings->save();
        }else{
            $settings->value = 'false';  
            $settings->save();
        }
        return back();
    }
    
    //view voor custom pages te creeeren
    public function createPage(){
        return view('theme::admin.page.createpage');
    }
    
    //toont het users dashboard
    public function users(){
        $users = User::paginate(15);
        $rol = Role::all();
        $roles = array();
        foreach($rol as $role => $v){
             $roles[$v->id] = $v['name'];
        }
        
        return view('theme::admin/dashboard/users',['users'=> $users, 'roles'=>$roles]);
    }
    
    //toont het pages dashboard
    public function showPageDashboard(){
        $pagesSetting = Settings::where('name','navOrder')->first()->value;
            if($pagesSetting == ''){
                $pages = Page::all();        
            }else{
                $settingPages = json_decode($pagesSetting);
                $placeholders = implode(',',array_fill(0, count($settingPages), '?')); 
                $pages = Page::whereIn('id', $settingPages)->orderByRaw("field(id,{$placeholders})", $settingPages)->get();    
            }
        return view('theme::admin/dashboard/pagedashboard',['pages'=>$pages]);
    }
    
    //hiermee kan je de volgorde van de navigatiebalk aanpassen
    public function changeNavMenu(Request $request){
        $data = $request->pages;
       
        var_dump($data);
        $pages = explode(',',$data);
         if(($key = array_search('', $pages)) !== false) {
            var_dump($key);
            unset($pages[$key]);
        }
        $pags = json_encode($pages);
        var_dump($pags);
        
        $setting = Settings::where('name', 'navOrder')->first();
        $setting->value = $pags;
        $setting->save();
    }
    
    //toont het theme dashboard
    public function themes(){
        //huidige theme
        $theme = Settings::where('name', 'theme')->first()->value;
        
        //andere themes
        $path = base_path() . '/public/themes';
        $directories = array_map('basename', File::directories($path));
        
        return view('theme::admin/dashboard/themes',['themes'=>$directories, 'huidigTheme' =>$theme]);
    }
    
    //Om een theme te kiezen
    public function chooseTheme(Request $request){
        $setting = Settings::where('name', 'theme')->first();
        $setting->value = $request->chosenTheme;
        $setting->save();
        return back();
    }
    
    //om een user te blokkeren, dit kan vanaf het users dashboard en de user's profile
    public function blockUser(Request $request){
        $user = User::find($request->user_id);
        if($request->disable == 'Yes'){
           
            $user->blocked = true;
            $user->save();
        }else{
            $user->blocked = false;  
            $user->save();
        }
        return back();
    } 
    
    //toont het poll dashboard
    public function adminPolls(){
        $percentageSetting = Settings::where('name','percentages')->first();
        $lookSetting = Settings::where('name','completedPollLook')->first();
        $categories = Category::all();
        $polls = Poll::all();
        return view('theme::admin/dashboard/polls',['percentageSetting'=> $percentageSetting,'lookSetting'=>$lookSetting,'categories'=>$categories,'polls'=>$polls]); 
    }
    
    //Stelt in of de look van een poll moet veranderen als hij compleet is of niet. 
    //Een poll is compleet als het maximum aantal stemmen bereikt is.
    public function changePollLookAfterComplete(Request $request){
        $setting = Settings::where('name', 'completedPollLook')->first();
        if($request->disable == 'Yes'){
           
            $setting->value = '1';
            $setting->save();
        }else{
            $setting->value = '0';  
            $setting->save();
        }
        return back();
    }
    
    //Voeg een user toe vanuit de backend
    public function addUser(Request $request){
        $this->validate($request,[
            'name' => 'required|max:255|unique:users,name',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
 
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);      
        $user->save();

        if(!empty($request->roles)){    
            foreach($request->roles as $rol){
                $role = Role::where('name', $rol)->first();
                $user->roles()->attach($role->id);               
            }
            $user->save();
        }
        return back();
    }
    
    //verwijder een user vanuit de backend
    public function deleteUser(Request $request){
        $user = User::find($request->user_id);    
        $user->delete();
        
        return back();
    }
    
    //pas een user aan vanuit de backend
    public function editUser(Request $request){
        $this->validate($request,[
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
        ]);

        $user = User::find($request->user_id);
        if( $user->name != $request->name)
        $user->name = $request->name;
        if($user->email != $request->email)
        $user->email = $request->email;
        
        $user->roles()->detach();
        if(!empty($request->roles)){    
            foreach($request->roles as $rol){
                $role = Role::where('name', $rol)->first();
                $user->roles()->attach($role->id);               
            }
            $user->save();
        }
        return back();
    }
    
    //om een poll te verwijderen vanuit de backend
    public function deletePoll(Request $request){
        $poll = Poll::find($request->poll_id);
        $poll->delete();
        return back();
    }
    
    //om een poll aan te passen vanuit de backend
    public function editPoll(Request $request){
        $this->validate($request,[
            'name' => 'required|max:255',
        ]);
        $poll = Poll::find($request->poll_id);
        $poll->name = $request->name;
        $poll->category_id = $request->cat;
        
        //$request->options zijn de nieuwe toegevoegde opties 
        if(!empty($request->options)){ 
             foreach($request->options as $option){
                 if($option != ''){
                    $opt = new Option();
                    $opt->name = $option;
                    $opt->score = 0;
                    $opt->poll_id = $poll->id;
                    $poll->options()->save($opt);     
                 }   
             }
        }
        //$request->oldoptions zijn de oude opties, deze kunnen eventueel aangepast zijn door de gebruiker, als dit zo is 
        //zal dit worden aangepast in de database, anders word de oude waarde behouden in de db 
        if(!empty($request->oldoptions)){
            foreach($request->oldoptions as $option){
                $opt = Option::find($option[0]);
                $opt->name = $option[1];
                $opt->save();
                var_dump($option[0]);
                
            }
        }
        $poll->save();
        return back();
    }
    
    // creeert een category voor polls
    public function postCategory(Request $request){
        $this->validate($request,[
            'name' => 'required|max:255',
        ]);
        $name = $request->name;
        $category = new Category;
        $category->name = $name;
        $category->save();
        return back();
    }
    //verwijder een category van polls, polls zullen dan category_id 0 krijgen en niet meer
    //getoond worden in All Polls. Je kan wel de category aanpassen door de poll te editen
    public function deleteCategory(Request $request){
        $cat = Category::find($request->cat_id);
        $polls = Poll::where('category_id', $request->cat_id)->update(array('category_id'=> ''));
        $cat->delete();
        
        return back();
    }
    public function editCategory(Request $request){
        $this->validate($request,[
            'name' => 'required|max:255',
        ]);
        $cat = Category::find($request->cat_id);
        $cat->name = $request->name;
        // $polls = Poll::where('category_id', $request->cat_id)->update(array('category_id'=> ''));
        // $cat->delete();
        $cat->save();
        return back();
    }
    
    public function searchUser(Request $request){
        $keyword = $request->term;
        $users = User::SearchByKeyword($keyword)->paginate(15);
        // foreach($users as $user){
        //     var_dump($user);
        // }
        $rol = Role::all();
        $roles = array();
        foreach($rol as $role => $v){
             $roles[$v->id] = $v['name'];
        }
        
        return view('theme::admin/dashboard/users',['users'=> $users, 'roles'=>$roles]);
      
    }
     public function searchPoll(Request $request){
        $keyword = $request->term;
        $polls = Poll::SearchByKeyword($keyword)->paginate(15);
        $percentageSetting = Settings::where('name','percentages')->first();
        $lookSetting = Settings::where('name','completedPollLook')->first();
        $categories = Category::all();
        return view('theme::admin/dashboard/polls',['percentageSetting'=> $percentageSetting,'lookSetting'=>$lookSetting,'categories'=>$categories,'polls'=>$polls]); 
    }
    
    public function bulkDeletePoll(Request $request){
        foreach($request->poll_ids as $poll){
            //  var_dump($poll['value']);
            Poll::destroy($poll['value']);
        }
        // var_dump($request->poll_ids);
    }
      
    
}
