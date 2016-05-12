<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artisan;
use App\User;
use App\Http\Requests;
use App\Role;
class InstallerController extends Controller
{
    public function setup(){
        return view('installer.setup');
    }
    
    //stelt .env in
    public function databaseSetup(Request $request){
        //oude config
        // DB_CONNECTION=mysql
        // DB_HOST=127.0.0.1
        // DB_PORT=3306
        // DB_DATABASE=homestead
        // DB_USERNAME=homestead
        // DB_PASSWORD=secret
        
        $this->validate($request,[
            'db_host'=> 'required',
            'db_port'=>'required',
            'db_database'=>'required',
            'db_username'=>'required',
            'db_password'=>'required'
        ]);
        
        $path = base_path('.env');
  
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                'DB_HOST='.env('DB_HOST', 'forge'), 'DB_HOST='. $request->db_host, file_get_contents($path)
            ));
        }
        
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                'DB_PORT='.env('DB_PORT', 'forge'), 'DB_PORT='. $request->db_port, file_get_contents($path)
            ));
        }
        
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                'DB_DATABASE='.env('DB_DATABASE', 'forge'), 'DB_DATABASE='. $request->db_database, file_get_contents($path)
            ));
        }
        
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                'DB_USERNAME='.env('DB_USERNAME', 'forge'), 'DB_USERNAME='. $request->db_username, file_get_contents($path)
            ));
        }
        
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                'DB_PASSWORD='.env('DB_PASSWORD', 'forge'), 'DB_PASSWORD='. $request->db_password, file_get_contents($path)
            ));
        }
       
        return redirect()->action('InstallerController@migrate');

    }
    
    //migreert alles
    public function migrate(){
        try {
            echo '<br>init migrate:install...';
            Artisan::call('migrate:install');
            echo 'done migrate:install';
            echo '<br>init with app tables migrations...';
            Artisan::call('migrate');
            echo '<br>done with app tables migrations';
            } catch (\Exception $e) {
                // Response::make($e->getMessage(), 500);
                return back()->withErrors(['Something went wrong! Fill in the right info!']);;

            }
        return redirect()->action('InstallerController@showCreateAdmin');
        
    }
    
    public function showCreateAdmin(){
        return view('installer.migrate');
    }
    
    //creeert de eerst admin bij installatie
    public function createAdmin(Request $request){
         $this->validate($request,[
         'username' => 'required|max:255',
           'email' => 'required|email|max:255|unique:users',
           'password' => 'required|confirmed|min:6',
        ]);
      
        $user = new User();
        $user->name = $request->username;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
            
        $role = Role::find(1)->first();
        $user->roles()->attach($role);
        
        return redirect()->action('HomeController@index');
    }
    
}
