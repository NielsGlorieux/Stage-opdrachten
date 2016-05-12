<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = 'polloptions';
    protected $fillable = array('name');

    public function poll() {
        return $this->belongsTo('App\Poll');
    }
    
}
