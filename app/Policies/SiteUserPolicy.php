<?php

namespace App\Policies;

use App\SiteUser;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;



class SiteUserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any site users.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if(Auth::check()){return User::isOwner();}
        else return false;
    }

    /**
     * Determine whether the user can view the site user.
     *
     * @param  \App\User  $user
     * @param  \App\SiteUser  $siteUser
     * @return mixed
     */
    public function view(User $user, SiteUser $siteUser)
    {

        

    }

    /**
     * Determine whether the user can create site users.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if(Auth::check()){return User::isOwner();}
        else return false;

        
        //
    }

    /**
     * Determine whether the user can update the site user.
     *
     * @param  \App\User  $user
     * @param  \App\SiteUser  $siteUser
     * @return mixed
     */
    public function update(User $user, SiteUser $siteUser)
    {
        if(Auth::check()){return User::isOwner();}
        else return false;
    }

    /**
     * Determine whether the user can delete the site user.
     *
     * @param  \App\User  $user
     * @param  \App\SiteUser  $siteUser
     * @return mixed
     */
    public function delete(User $user, SiteUser $siteUser)
    {
        // return true;
        // return response()->json("hi");
        return $user->isOwner();
     
    }

    /**
     * Determine whether the user can restore the site user.
     *
     * @param  \App\User  $user
     * @param  \App\SiteUser  $siteUser
     * @return mixed
     */
    public function restore(User $user, SiteUser $siteUser)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the site user.
     *
     * @param  \App\User  $user
     * @param  \App\SiteUser  $siteUser
     * @return mixed
     */
    public function forceDelete(User $user, SiteUser $siteUser)
    {
        //
       
    }
}
