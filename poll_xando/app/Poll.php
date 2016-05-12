<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Option;
class Poll extends Model
{
    
     protected $fillable = array('name', 'user_id','score','percentage');
     
    //  protected $casts = [
    //     'options' => 'array'
    //  ];
    
     public function options() {
        return $this->hasMany('App\Option');
    }
    
     public function usersVoted() {
        return $this->belongsToMany('App\User', 'users_polls', 'poll_id', 'user_id');
    }
    
     public function comments()
    {
        return $this->hasMany('App\Comment');
    }
    
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
    
    protected static function boot() {
        parent::boot();

        static::deleting(function($poll) { // before delete() method call this
             $poll->options()->delete();
             // do the rest of the cleanup...
        });
        
        static::deleting(function($poll) { // before delete() method call this
             $poll->comments()->delete();
             // do the rest of the cleanup...
        });
    }
    public function scopeSearchByKeyword($query, $keyword)
    {
        if ($keyword!='') {
            $query->where(function ($query) use ($keyword) {
                $query->where("name", "LIKE","%$keyword%");
            });
        }
        return $query;
    }

}
