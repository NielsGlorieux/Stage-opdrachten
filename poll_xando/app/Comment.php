<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'name', 'body','level'
    ];
    
    public function poll()
    {
        return $this->belongsTo('App\Poll');
    }
    
     public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function parent()
    {
        return $this->belongsTo('App\Comment', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Comment', 'parent_id');
    }
}
