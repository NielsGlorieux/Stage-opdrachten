<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
     protected $fillable = [
        'sender_id', 'message'
    ];
}
