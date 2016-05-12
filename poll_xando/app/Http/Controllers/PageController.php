<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Page;
use App\Http\Requests;
use App\Settings;

class PageController extends Controller
{
    //creeert een nieuwe custom page
    public function create(Request $request)
    {
        $page = new Page();
        $page->slug = $request->slug;
        $page->title = $request->title;
        $page->content = $request->content;
        $page->isForm = $request->isForm;
        $page->isStandard = '0';
        $page->save();
        
        //setting van nav orde aanpassen zodat nieuwe page ook getoond wordt in de lijst bij pages dashboard
        $pagesSetting = Settings::where('name','navOrder')->first();
        $settingPages = json_decode($pagesSetting->value);
        array_push($settingPages,$page->id);
        $setting = json_encode($settingPages);
        $pagesSetting->value = $setting;
        $pagesSetting->save();
        
        return back();
    }
    
    //toont custom pages
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->first();
        return view('theme::page.index', ['page'=> $page]);
    }
    
    //om custom pages te editten
    public function showEdit($slug)
    {
        $page = Page::where('slug', $slug)->first();
     
        return view('theme::admin.page.edit', ['page'=> $page]);
    }
    
    //past de custom page aan
    public function edit(Request $request)
    {
        $this->validate($request, [
            'slug' => 'required|max:255',
        ]);
        
        $page = Page::where('slug', $request->oldSlug)->first();
        $page->slug = $request->slug;
        $page->title = $request->title;
        if($request->content != ''){
          $page->content = $request->content;  
        }else if($request->contentb != ''){
            $page->content = $request->contentb;
        }else{
            $page->content = '';
        }
       
        $page->save();
        return redirect()->action('AdminController@showPageDashboard');
    }
    
    //past het type van de pagina aan
    public function changeType(Request $request){
        $page = Page::find($request->page_id);
        
        if($request->type == 'Yes'){
           
            $page->isForm = true;
            $page->save();
        }else{
           $page->isForm = false;
            $page->save();
        }
        return back();
    }
    
    public function deletePage(Request $request){
        Page::destroy($request->page_id);
        return back();
    }


}
