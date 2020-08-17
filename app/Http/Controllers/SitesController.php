<?php

namespace App\Http\Controllers;

// use App\Http\Controllers;
use App\Sites;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use \DateTime as MyDateTimeClass;

use Illuminate\Support\Facades\Session;


use Illuminate\Support\Facades\Storage;



class SitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($subDomain)
    {
         
        // $request=new Request;
        // return response()->json(request()->cookie());
        if(strlen(request()->cookie('jwt'))>20){
            
            Auth::setToken(request()->cookie('jwt'));}

        $JSONData=Sites::select('sitejson')->where('siteurl', $subDomain)->first();
        $jsonPHPObject=$JSONData->sitejson;
        $jsonPHPObject=json_decode($jsonPHPObject);
        $jsonPHPObject->UserData->csrfToken=csrf_token();//add csrf token

        if(Auth::check()){
        $jsonPHPObject->UserData->userType=Auth::user()->siteOwnership();//add user type
        $jsonPHPObject->UserData->signedin=true;//add user status
        if(strlen(request()->cookie('jwt'))>20){
            $jsonPHPObject->UserData->jwtToken=request()->cookie('jwt');
        }else $jsonPHPObject->UserData->jwtToken=explode("Bearer ",request()->header('Authorization'))[1];
        $jsonPHPObject->UserData->email= auth()->user()->email;
        $jsonPHPObject->UserData->name = auth()->user()->name;
        $jsonPHPObject->UserData->Telephone = auth()->user()->telephone;
        $jsonPHPObject->UserData->Address = auth()->user()->address;
        $jsonPHPObject->UserData->otherTelephone = auth()->user()->othertelephone;
        $jsonPHPObject->UserData->otherAddress = auth()->user()->otheraddress;

        $host=request()->getHost();
        $path=explode($host."/",request()->header('Referer'))[1];

        if (strpos($path, "reservation") !== false) {
        $jsonPHPObject->pages->reserve->reservations[0]= ReservationsController::get_reservation_from_id(explode("reservation/",$path)[1])["reservations"];
        $jsonPHPObject->pages->reserve->reservationsreceived=ReservationsController::get_reservation_from_id(explode("reservation/",$path)[1])["reservationsreceived"];
        }
        else{
        $jsonPHPObject->pages->reserve->reservations= ReservationsController::userReservations(null)['reservations'];
        $jsonPHPObject->pages->reserve->reservationsreceived=ReservationsController::userReservations(null)['reservationsreceived'];
        }

        if (strpos($path, "order") !== false){
            // return response()->json();
        $jsonPHPObject->pages->cart->orders[0]=OrdersController::get_order_from_id(explode("order/",$path)[1])["orders"];
        $jsonPHPObject->pages->cart->ordersreceived=OrdersController::get_order_from_id(explode("order/",$path)[1])["ordersreceived"];
        }
        else{
        $jsonPHPObject->pages->cart->orders=OrdersController::userorders(null,null)["orders"];
        $jsonPHPObject->pages->cart->ordersreceived=OrdersController::userorders(null,null)["ordersreceived"];
        }

        }else {
            $jsonPHPObject->UserData->userType="user";
            $jsonPHPObject->UserData->signedin=false;
            $jsonPHPObject->pages->cart->orders=[];
            $jsonPHPObject->pages->cart->ordersreceived=false;
            $jsonPHPObject->pages->reserve->reservations=[];
            $jsonPHPObject->pages->reserve->reservationsreceived=false;
        
        }

         return json_encode($jsonPHPObject);
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
    public function setdefaultvalues(array $data,$type) 
    {

        $defualtjsoncontent = json_decode(Storage::disk('local')->get('constants/defaultjson.json'), true);
        
        
        // $defualtjsoncontent->Header->style->language=$type;
        // if($type="en"){
        //     $defualtjsoncontent->Header->style->flxdir="row";
        //     $defualtjsoncontent->Header->style->direction="left";
        // }else if($type="ar"){
        //     $defualtjsoncontent->Header->style->flxdir="row-reverse";
        //     $defualtjsoncontent->Header->style->direction="right";
        // }
        

        
        $data['sitejson']=$defualtjsoncontent;
        $data['name']=$data['siteurl'];
        $data['enid']=null;
        $data['arid']=null;
        $data['about']=null;
        $data['web']=null;
        $data['email']=null;
        $data['address']=null;
        $data['telephone']=null;
        $data['telephone1']=null;
        $data['orders']='{}';
        $data['reservations']='{}';
        $data['status']=null;

        return $data;
    }
    
     public function store(Request $request) 
    {
        //  $newurl = $request->input('name');
        $newurl = request(["siteurl"]);
        $type = request(["type"]);

        if(!Auth::check()){return redirect()->away('https://mackany.com/register');}
        
        if(Sites::where('siteurl',$newurl['siteurl'])->exists()){ return view('landpage', ['jwt_token' => request(["token"])['token'],'username'=>Auth::user()->name,'status'=>'Name already taken!']);}
        
        $user_sites_count=0;
        $user_sites=Auth::user()->sites()->get();

        foreach($user_sites as $key=>$site){
             if($site->ownership->status=="owner"){$user_sites_count++;}
        }


        if($user_sites_count>10){ return view('landpage', ['jwt_token' => request(["token"])['token'],'username'=>Auth::user()->name,'status'=>'This account reached maximum sites']);}

        $this->validator($newurl)->validate();

       
         $newfulldata=$this->setdefaultvalues($newurl,$type); 
        
// return response()->json($newfulldata);

        $value=$this->createnewsite((array)$newfulldata);

        $site=Sites::where('siteurl',$newurl['siteurl'])->first();
        $user = Auth::user();
        
        $user->sites()->save($site, array('status' => 'owner'));
        // $request->Session::put('jwt', Auth::refresh());
        return redirect('https://'.$newurl['siteurl'].'.mackany.com')
        // ->header('Authorization', 'Bearer '.Auth::refresh());
          ->cookie("jwt", request(["token"])['token'] , 20160, "/", 'mackany.com', true, true);
        // return response()->redirect($newurl.".mackany.com");

        // return response()->json($value);

        //
    }

    
    protected function validator(array $data)
    {

        $error_messages = [
            'required'    => 'You must write your :attribute',
            'string'    => 'The :attribute must string',
            'max' => 'The :attribute must not exceed :max characters.',
            'unique'      => 'we already have the same URL :attribute in our database',
        ];
       
        return Validator::make($data, [
            'siteurl' => ['required', 'string', 'max:255','unique:sites'],
            
        ],$error_messages);

    }

    protected function createnewsite(array $data)
    {
               
        
        return Sites::create([
            'name' => $data['name'],
            'siteurl' => $data['siteurl'],
            'sitejson' => json_encode($data['sitejson']),
            'enid' => $data['enid'],
            'arid' => $data['arid'],
            'telephone' => $data['telephone'],
            'telephone1' => $data['telephone1'],
            'about' => $data['about'],
            'web' => $data['web'],
            'address' => $data['address'],
            'email' => $data['email'],
            'orders' => $data['orders'],
            'reservations' => $data['reservations'],
            
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\sites  $sites
     * @return \Illuminate\Http\Response
     */
    public function show(sites $sites)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\sites  $sites
     * @return \Illuminate\Http\Response
     */
    public function edit(sites $sites)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\sites  $sites
     * @return \Illuminate\Http\Response
     */
    public function update(sites $sites,Request $request,$subDomain)
    {
        if (Gate::allows('edit-sitejson')) {

            // $jsonin=(object)$request->data['pages']['cart'];
            // $jsonin->orders=[];
            // $jsonin->ordersreceived=false;

            

            $json_dB=json_decode(Sites::select('sitejson')->where('siteurl',Route::input("subDomain"))->first()->sitejson,false);

            
            $jsonin=json_decode(json_encode((Object)$request['data']),false);
           

            $jsonin->UserData=$json_dB->UserData;
            $jsonin->UserData->userType="user";
            $jsonin->UserData->signedin=false;
            $jsonin->pages->cart->orders=[];
            $jsonin->pages->cart->ordersreceived=false;
            $jsonin->pages->reserve->reservations=[];
            $jsonin->pages->reserve->reservationsreceived=false;



        $affected = $sites
              ->where('siteurl', $subDomain)
              ->update(['sitejson' => json_encode($jsonin)]);

        // return $request->data;
        }
        // else
          return $this->index($subDomain);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\sites  $sites
     * @return \Illuminate\Http\Response
     */
    public function destroy(sites $sites)
    {
        //
    }

}
