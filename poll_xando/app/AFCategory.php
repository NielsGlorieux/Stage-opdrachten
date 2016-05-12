<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AFCategory extends Model
{
    public function topics()
    {
        return $this->hasMany('App\AFTopic');
    }
    protected static function boot() {
        parent::boot();

        static::deleting(function($cat) { 
                $cat->topics()->delete();
        });
    }
   
}
