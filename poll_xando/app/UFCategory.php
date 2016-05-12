<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UFCategory extends Model
{
     public function topics()
    {
        return $this->hasMany('App\UFTopic');
    }
    protected static function boot() {
        parent::boot();

        static::deleting(function($cat) { 
             $cat->topics()->delete();
        });
    }
}
