<?php

namespace App\Http\Middleware;
use App\Poll;
use App\Settings;
use Closure;

class MaxVotesMiddleware
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
       
        
        $poll = Poll::where('id',$request->pollId)->first();
        $totaleScore = 0;
        
        foreach($poll->options as $option){
            $totaleScore += $option->score;
        }
        $percentageSetting = Settings::where('name','percentages')->first();
        // var_dump($percentageSetting);
        
        
        if($poll->maxVotes != -1 && $totaleScore >= $poll->maxVotes/* && $percentageSetting->value == 'true'*/){

            return back();
        }
        
        return $next($request);
    }

}
