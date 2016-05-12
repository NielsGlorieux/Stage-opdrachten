<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UFTopic extends Model
{
    public function replies()
    {
        return $this->hasMany('App\UFReply');
    }
    
    public function category() {
        return $this->belongsTo('App\UFCategory');
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
