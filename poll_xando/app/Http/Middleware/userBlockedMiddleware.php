<?php

namespace App\Http\Middleware;
use Illuminate\Http\Response;
use Closure;
use Auth;
use Illuminate\Support\Facades\Redirect;
class userBlockedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check() && Auth::user()->blocked == true){
            Auth::logout();
            echo '<div style="color:red"><b>You have been blocked.</b></div>';
            // return redirect('/redirect');
        }
        
        return $next($request);
    }
}
