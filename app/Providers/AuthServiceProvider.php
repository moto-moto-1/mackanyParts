<?php

namespace App\Providers;

use App\Policies\SiteUserPolicy;
use App\SiteUser;
use App\User;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        SiteUser::class => SiteUserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('edit-sitejson', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('edit-managers', function ($user) {
            return $user->isOwner();
        });

        Gate::define('change_ord_reserve_status', function ($user,$status) {
           $status=json_decode($status);
            $current_status=$status->current;
            $needed_status=$status->needed;

            if($needed_status=="reserved"||$needed_status=="preparing"){
                return false;
            }            
            else if($user->isAdmin()){
                if(($needed_status=="waiting"||$needed_status=="ondelivery"||$needed_status=="done"||$needed_status=="cancel") 
                && ($current_status=="reserved"||$current_status=="preparing")){return true;}
                else if(($needed_status=="done"||$needed_status=="cancel") 
                && ($current_status=="waiting"||$current_status=="ondelivery")){return true;}
                else{return false;}
        }
        if($needed_status=="cancel" && ($current_status=="reserved"||$current_status=="preparing")){
return true;
        }
           
            else{return false;}
        });

        //
    }
}
