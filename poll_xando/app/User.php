<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','blocked'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function pollsVoted() {
        return $this->belongsToMany('App\Poll', 'users_polls', 'user_id', 'poll_id')->withPivot('votedOption');
    }
    
    public function roles()
    {
        return $this->belongsToMany('App\Role','users_roles','user_id','role_id');
    }
    
    public function is($roleName){
        foreach($this->roles()->get() as $role){
            if($role->name == $roleName){
                return true;
            }
        }
        return false;
    }
    
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }
    
    public function replies()
    {
        return $this->hasMany('App\AFReply');
    }
    
    public function topics()
    {
        return $this->hasMany('App\AFTopic');
    }
    
    public function ReceivedMessages(){
        return $this->hasMany('App\PrivateMessage','ontvanger_id')->orderBy('gelezen');
    }
    
    public function SendMessages(){
        return $this->hasMany('App\PrivateMessage','verstuurder_id');
    }
    
    protected static function boot() {
        parent::boot();

        static::deleting(function($user) { // before delete() method call this
             $user->roles()->detach();
             // do the rest of the cleanup...
        });
    }
    
    public function scopeSearchByKeyword($query, $keyword)
    {
        if ($keyword!='') {
            $query->where(function ($query) use ($keyword) {
                $query->where("name", "LIKE","%$keyword%")
                    ->orWhere("email", "LIKE", "%$keyword%");
            });
        }
        return $query;
    }
}
