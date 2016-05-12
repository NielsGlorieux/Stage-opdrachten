<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AFTopic extends Model
{
    public function replies()
    {
        return $this->hasMany('App\AFReply');
    }
    
    public function category() {
        return $this->belongsTo('App\AFCategory');
    }
     public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    protected static function boot() {
        parent::boot();

        static::deleting(function($topic) { 
             $topic->replies()->delete();
        });
    }
}
