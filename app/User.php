<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Route;


class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','address', 'telephone', 'othertelephone','otheraddress',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


     // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
        // return ["name"=>"","address"=>"address","email"=>"email"];
    }


    public function sites()
    {
        return $this->belongsToMany('App\Sites', 'site_user', 'user_id', 'site_id')
        ->withPivot(['status','created_at','updated_at'])->as('ownership')->withTimestamps()->using('App\SiteUser'); 
       }

    
    public function isAdmin(){
        // $sites=$this->sites();
        foreach ($this->sites as $site) {
            if($site->siteurl==Route::input("subDomain") && 
            ($site->ownership->status=="manager"||$site->ownership->status=="owner"))
            {return true;}
        }

        return false;
        
    }

    public function isManager(){
        foreach ($this->sites as $site) {
            if($site->siteurl==Route::input("subDomain") && $site->ownership->status=="manager"){return true;}
        }
        
        return false;
            }

    public function isOwner(){
        foreach ($this->sites as $site) {
            if($site->siteurl==Route::input("subDomain") && $site->ownership->status=="owner"){return true;}
        }
        
        return false;
            }

    public function siteOwnership(){
      
       foreach ($this->sites as $site) {
        //    dump($site);
                    if($site->siteurl==Route::input("subDomain")){return $site->ownership->status;}
                }
                
                return "client";
                    }
    public function orders()
    {
        return $this->hasMany('App\Order');
    }

}
