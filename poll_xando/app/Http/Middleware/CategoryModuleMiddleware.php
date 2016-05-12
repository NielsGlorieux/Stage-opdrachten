<?php

namespace App\Http\Middleware;

use Closure;

class CategoryModuleMiddleware
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
        $response = $next($request);
        
        if(!method_exists($response, 'content')):
            return $response;
        endif;
        $cats = App\Category::all()->select('name');
        $content = str_replace('[categories]','lol', $response->content());  

        $response->setContent($content);
        return $response;
    }
}
