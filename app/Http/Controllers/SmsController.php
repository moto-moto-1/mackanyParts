<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Sites;

use Illuminate\Support\Facades\Route;

class SmsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function news($value)
    {
        return "view('welcome')";
    }

    public function index()
    {
        return view('home');
    }
    public static function sendsms($from,$to,$messagetosend){

        $basic  = new \Nexmo\Client\Credentials\Basic(getenv("CLIENT_ID"), getenv("CLIENT_IDs"));
        $client = new \Nexmo\Client($basic);


        $site=Sites::where('siteurl', Route::input("subDomain"))->first();

        $site_users=$site->user()->get();
        
        $owner_user="";
        
        foreach($site_users as $key=>$user){
            if($user->ownership->status=="owner"){$owner_user=$user;break;}
        }

        $user_plan=json_decode(json_decode($owner_user,false)->plan,false);

        // return response()->json($user_plan);

        if(  !(strlen(json_decode($owner_user,false)->plan)>5)  ){//user have no plans
            return ;
          }
  

        if($user_plan->sms>1){

            $message = $client->message()->send([
                'to' => $to,
                'from' => 'Mackany',
                'type' =>'unicode',
                'text' => $messagetosend,
            ]);

            $user_plan->sms=$user_plan->sms-1;
            $owner_user->plan= json_encode($user_plan);
            $owner_user->save();

               }
else{
    return response()->json($user_plan->sms);

}


return response()->json($message);
    }

    
}
