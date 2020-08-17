<?php

namespace App\Http\Controllers;

use App\SiteUser;
use App\User;
use App\Sites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;



class SiteUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user=Auth::user();
        
        if(!$user->isOwner()){return response()->json();}
        
        $site=Sites::where("siteurl",Route::input("subDomain"))->first();

        $users=$site->user;
        // return response()->json($users);
        $managers=[];
        
foreach ($users as $key => $user) {
    
if($user->isManager()){
    array_push($managers,$user->email);
}else {continue;}
    
   }

    return response()->json($managers);
    

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user=Auth::user();
        if(!$user->isOwner()){return response()->json();}
        //  return response()->json($request);
        //
        $newMangerMail=$request["mail"];
        // return response()->json($newMangerMail);
        $user=User::where("email",$newMangerMail)->first();
        // return response()->json($user);
        $site=Sites::where("siteurl",Route::input("subDomain"))->first();
        
        //    return response()->json($user->sites()==$site);
        if(!($user->sites()==$site)){
        $user->sites()->save($site, array('status' => 'manager'));}
        

        return $this->index();
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\SiteUser  $siteUser
     * @return \Illuminate\Http\Response
     */
    public function show(SiteUser $siteUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SiteUser  $siteUser
     * @return \Illuminate\Http\Response
     */
    public function edit(SiteUser $siteUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SiteUser  $siteUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SiteUser $siteUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SiteUser  $siteUser
     * @return \Illuminate\Http\Response
     */

    

    public function destroy( $SubDomain,$userToDelete)
    {
        // if(!Auth::check()){return response()->json();}
        // $user=Auth::user();
        // if ($user->cant('delete',$user,SiteUser::class)) {
        //    return response()->json();
        // }
        
        $user=Auth::user();
        if(!$user->isOwner()){return response()->json();}
        //
        // return response()->json([$userToDelete]);
        $site=Sites::where("siteurl",Route::input("subDomain"))->first();
        $users=$site->user;
        foreach ($users as $key => $user) {
            
            if($user->email==$userToDelete){
                
          $user->sites()->detach($site->id);
            }
        }

        return $this->index();


    }
}
