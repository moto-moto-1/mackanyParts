<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sites extends Model
{

    protected $fillable = [
        'name', 'email', 'password','address', 'telephone', 'telephone1','siteurl',
        'sitejson','enid','arid','about','web','orders','reservations'
    ];


    //

    public function user()
    {
        return $this->belongsToMany('App\User', 'site_user', 'site_id', 'user_id')
        ->withPivot(['status','created_at','updated_at'])->as('ownership')->withTimestamps()->using('App\SiteUser'); 
       }

}
