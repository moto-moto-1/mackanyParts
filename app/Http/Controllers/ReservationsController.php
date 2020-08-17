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
use Illuminate\Support\Facades\Mail;
use App\Mail as myMail;

use Illuminate\Support\Facades\Session;


use Illuminate\Support\Facades\Storage;



class ReservationsController extends Controller
{

    public function getreservations(){

        $page_number=request()->input("pagenumber")-1;
        $filter_by=request()->input("filterby");
        $items_per_page=request()->input("perpage");
       
        if(Auth::user()->isAdmin()){
         $reservations_filtered=[];
         $reservations=Sites::select('reservations')->where('siteurl', Route::input("subDomain"))->first();
         $reservations=json_decode($reservations->reservations);

         foreach($reservations as $key=>$reservation){
           if($reservation->status==$filter_by){
            array_push($reservations_filtered,$reservation);
           }
           else if($filter_by=="all"){
            $reservations_filtered=$reservations;
           }
         }
         
        $length=count($reservations_filtered);

        if($length==0){return [];}

        if($length>=($page_number*$items_per_page)+$items_per_page){
            $last_item_position=($page_number*$items_per_page)+$items_per_page;
        }

        elseif($length<=$page_number*$items_per_page){
            return [];
        }
        
        else{
            $last_item_position=$length;
        }
         
         $reservations_to_send=[];

         for($i=($page_number*$items_per_page);$i<$last_item_position;$i++){
            array_push($reservations_to_send,$reservations_filtered[$i]);
         }

         return $reservations_to_send;



        }
    }
    
   

    public function newreservation($domain,Request $request){

        if ($request->input("filtering")){
            $reservationstoreturn['reservationsreceived']=true;
            $reservationstoreturn['reservations']=$this->getreservations();
            
            return response()->json($reservationstoreturn);
        }


        $newreservation = $request->except('_token');
        $newreservation['date']=date('D M d Y O');
        $newreservation['datetime']=date('D M d Y H:i:s O');
        $newreservation['time']=date('H:i:s O');
        $newreservation['status']="reserved";
        $newreservation['reservations']=json_decode($newreservation['reservations']);

        if(strlen($newreservation['address'])>5 && Auth::check()){
            if($newreservation['address']!=Auth::user()->otheraddress){
                $user=Auth::user();
                $user->otheraddress=$newreservation['address'];
                $user->save();

            }
        }
        if(strlen($newreservation['telephone'])>5 && Auth::check()){
            if($newreservation['telephone']!=Auth::user()->othertelephone){
                $user=Auth::user();
                $user->othertelephone=$newreservation['telephone'];
                $user->save();
            }
        }

        
        $reservations=json_decode(Sites::select('reservations')->where('siteurl', $domain)->first()->reservations);
        
        $services=json_decode(Sites::select('sitejson')->where('siteurl', $domain)->first()->sitejson)->pages->services;

        // return response()->json();
    //    return response()->json($newreservation['reservations']);
        
       foreach($newreservation['reservations'] as $number => $reservation){
        
        $service=$this->getservicefromid($reservation->serviceid,$services);

        $appointment['date']=$reservation->date;
        $appointment['time']=$reservation->time;

        
        
        //this line is for testing 
        // return $this->checkappointmentInService($appointment,$service);

        if($this->checkappointmentInService($appointment,$service)){

            //test ToDate 
            if(array_key_exists('Dates',$reservation)){
            $all_dates_clear=false;
            foreach($reservation->Dates as $key=>$date){
                $appointment['date']=$date->Date;
                $appointment['time']=$date->Time;
                if($this->checkappointmentInService($appointment,$service)){

                }else{
                    return response()->json("appointment already taken");
                }

            }
        }
        }else {return response()->json("appointment already taken");}

       }
       
        if(strlen(json_encode($reservations))<10){
            $newreservation['reservationid']=1;
            $newreservation['clienreservationid']=1;
            $reservations=[];
            array_push($reservations,$newreservation);
        }

        else{
            // $reservations=json_decode($reservations);
            $lastreservation=end($reservations);
            $newreservation['reservationid']=$lastreservation->reservationid+1;
            $newreservation['clienreservationid']=($lastreservation->clienreservationid+1)%9999;
            array_push($reservations,$newreservation);
             }
             Sites::where('siteurl', $domain)
             ->update(['reservations' => $reservations]);

             $this->send_notification($newreservation['email'],$newreservation['telephone'],[
                "name"=>$newreservation['name'],
                "id"=>$newreservation['reservationid'],
                "clientid"=>$newreservation['clienreservationid'],
                "message"=>"hi, you have new reservation ",             
                ]);
    
                // return response()->json($newreservation);
             return response()->json($this->userReservations($newreservation));
            //  return response()->json($this->userorders($orders,$neworder));
    }

    public function getservicefromid($serviceId,$services){
        
        $service_main=$services->Services;
        $service_subpages=$services->SubPages;

        foreach ((array)$service_main as $key => $service) {
            if($service->ServiceId==$serviceId){
                return $service;
            }
        }
        foreach ((array)$service_subpages as $key1 => $subpage) {
            foreach ($subpage->Services as $key2 => $subService) {
                if($subService->ServiceId==$serviceId){
                    return $subService;
                }
            }   
        }
        

    }
    

    public function checkappointmentInService($clientappointment,$service){
        
        $datearray=explode("/",$clientappointment['date']);
        $timearray=explode(":",$clientappointment['time']);
        $day=$datearray[0];
        $month=$datearray[1];
        $year=$datearray[2];
        $hour=$timearray[0];
        $min=$timearray[1];

        $dayofweek=date('l',strtotime(date($year.'-'.$month.'-'.$day)));

        // return response()->json($service);
        //check if chosen Date is available for reservation
        foreach($service->Appointments as $key => $appointment){
           if($appointment->Day==$dayofweek){
                if($appointment->exists){
                    $daynumber=$key;
                break;
                }else{return false;}
            }
        }
        

        //Check if chosen time is inside the window
        $dayChosen=$service->Appointments[$daynumber];
        
               
        $AppointmentTime=MyDateTimeClass::createFromFormat('G:i',$hour.':'.$this->leadZero($min));
        $fromtime1=MyDateTimeClass::createFromFormat('G:i',$dayChosen->FromHour1.':'.$this->leadZero($dayChosen->FromMin1));
        $totime1=MyDateTimeClass::createFromFormat('G:i',$dayChosen->ToHour1.':'.$this->leadZero($dayChosen->ToMin1));
        $fromtime2=MyDateTimeClass::createFromFormat('G:i',$dayChosen->FromHour2.':'.$this->leadZero($dayChosen->FromMin2));
        $totime2=MyDateTimeClass::createFromFormat('G:i',$dayChosen->ToHour2.':'.$this->leadZero($dayChosen->ToMin2));
        
        
        if(($AppointmentTime>=$fromtime1&&$AppointmentTime<=$totime1)
             ||($AppointmentTime>=$fromtime2&&$AppointmentTime<=$totime2)){
                
    }else {return false;}
    
    //check if time is not taken
        $newAppointment=MyDateTimeClass::createFromFormat('d/m/Y G:i',$clientappointment['date'].' '.$clientappointment['time']);
        // dump($service->TakenAppointments);    
    foreach($service->TakenAppointments as $key => $takenApointment){
        // dump($takenApointment);
        // dump($takenApointment->Date);
        // dump($key);
        $appointInFormat=MyDateTimeClass::createFromFormat('d/m/Y G:i',$takenApointment->Date.' '.$takenApointment->Time);
       
        if($newAppointment==$appointInFormat){
         if($takenApointment->number<$dayChosen->ServingLines&&$dayChosen->ServingLines>1){
             //increase number
         $service->TakenAppointments[$key]->number=$service->TakenAppointments[$key]->number+1;
         $this->updateservicebyid($service->ServiceId,$service);
            return true;
         }else{return false;}
        
        
        }
    }
    //insert new appointment in taken appointments
$newappointment2insert['Date']=$clientappointment['date'];
$newappointment2insert['Time']=$clientappointment['time'];
$newappointment2insert['number']=1;
$takenAppointments=$service->TakenAppointments;
array_push($takenAppointments,(object)$newappointment2insert);
$service->TakenAppointments=$takenAppointments;

$this->updateservicebyid($service->ServiceId,$service);
    return true;

    }

    public function updateservicebyid($serviceId,$servicein){
        
        $SiteJson=json_decode(Sites::select('sitejson')->where('siteurl', Route::input("subDomain"))->first()->sitejson); 
        
        $service_main=$SiteJson->pages->services->Services;
        $service_subpages=$SiteJson->pages->services->SubPages;

         
        foreach ($service_main as $key => $service) {
            if($service->ServiceId==$serviceId){
                $SiteJson->pages->services->Services[$key]=$servicein;
            }
        }
        foreach ($service_subpages as $key1 => $subpage) {
            foreach ($subpage->Services as $key2 => $subService) {
                if($subService->ServiceId==$serviceId){
                    $SiteJson->pages->services->SubPages[$key1]->Services[$key2]=$servicein;
                }
            }   
        }
        

        Sites::select('sitejson')->where('siteurl', Route::input("subDomain"))
              ->update(['sitejson' => json_encode($SiteJson)]);

    }


    
private function leadZero($int){if($int<9&&strlen($int)<2){return "0".$int;}}

public function changereservationstatus($domain,Request $request){
        
    $reservationid=$request['reservationid'];
    $reservationstatus=$request['status'];

if(strlen($reservationstatus)<20){

    $reservations=Sites::select('reservations')->where('siteurl', $domain)->first()->reservations;
    
    $reservations=json_decode($reservations, true);
    foreach ($reservations as $key => $reservation) {
        if($reservation['reservationid']==$reservationid){
            $reservationcurrneededstatus['current']=$reservation['status'];
            $reservationcurrneededstatus['needed']=$reservationstatus;

            if(($reservationstatus=="paid"&&Auth::user()->isAdmin())||($reservationstatus=="creditpaid")){
                $reservations[$key]['paymentstatus'] = $reservationstatus;
                $reservationchanged=$reservations[$key];
                break;
            }

            if(!auth()->check()){    //checking a not authinticated user
                if(User::where('email', '=', $reservation['email'])->exists()){return response()->json();} //if user is in dB don't allow change
                else{//if user is not in dB allow change
                    if($reservationstatus=="cancel" && ($reservation['status']=="reserved"||$reservation['status']=="preparing")){
                        $reservations[$key]['status'] = $reservationstatus;
                        $reservationchanged=$reservations[$key];
                        $this->cancelAppointments($reservations[$key]['reservations']);
                    }
                }
            }
            else if (Gate::allows('change_ord_reserve_status',json_encode($reservationcurrneededstatus))) {
                // if(Auth::user()->isAdmin()){
                    if($reservationstatus=="cancel"){
                    $this->cancelAppointments($reservations[$key]['reservations']);}
                    $reservations[$key]['status'] = $reservationstatus;
                // }
                // else if($orderstatus=="cancel"){
                    // $reservations[$key]['status'] = $reservationstatus;
                // }
                $reservationchanged=$reservations[$key];
            }
            else {return response()->json();}
            
        }
    }

    Sites::where('siteurl', $domain)
    ->update(['reservations' => $reservations]);

   return response()->json($this->userReservations($reservationchanged));
}
}

public function cancelAppointments($reservations){
    $services=json_decode(Sites::select('sitejson')->where('siteurl', Route::input("subDomain"))->first()->sitejson)->pages->services;
    
    foreach($reservations as $key_for_reservtions=>$service_chosen){
        $service=$this->getservicefromid($service_chosen['serviceid'],$services);

        foreach($service->TakenAppointments as $key=>$appointment){
               //remove single main date
            if($appointment->Date==$service_chosen['date']&&$appointment->Time==$service_chosen['time']){
                if($appointment->number>1){
                    $service->TakenAppointments[$key]->number=$service->TakenAppointments[$key]->number-1;
                }
                else {
                    array_splice($service->TakenAppointments, $key, 1);
                }
            }
                //removing multiple dates for whole day
           if(array_key_exists('Dates',$service_chosen)&&count($service_chosen['Dates'])>0){
            foreach($service_chosen['Dates'] as $chos_key=>$App_Date){
                if($appointment->Date==$App_Date['Date']&&$appointment->Time==$App_Date['Time']){
                    if($appointment->number>1){
                        $service->TakenAppointments[$key]->number=$service->TakenAppointments[$key]->number-1;
                    }
                    else {
                        array_splice($service->TakenAppointments, $key, 1);
                    }
                }
            }
           }



        }
        $this->updateservicebyid($service_chosen['serviceid'],$service);
    }
   

}

   
    public static function userReservations($newreservation){
        
        $reservationstoreturn['reservationsreceived']=true;
        $reservationstoreturn['reservations']=[];
        if(strlen(json_encode($newreservation))>20){
            $reservationstoreturn['lastreservationid']=$newreservation['reservationid'];
            }

        $reservations=json_decode(Sites::select('reservations')->where('siteurl',Route::input("subDomain"))->first()->reservations);
       
        if(strlen(json_encode($reservations))<20){
            $reservationstoreturn['reservationsreceived']=false;
            return $reservationstoreturn;
        }  

        
        

        if(Auth::check()){
            $user=Auth::user();
              if($user->isAdmin()){$reservationstoreturn['reservations']=$reservations;} //user is admin
              
              else {                                                   //user is client
                  foreach($reservations as $reservation) {
                      $reservation=(array)$reservation;
                        if($reservation['email']==$user->email){
                      array_push($reservationstoreturn['reservations'],$reservation);}                    
                    }
              }  
          }else {
              if(strlen($newreservation['email'])<5 ){ //user didn't put address
                  array_push($reservationstoreturn['reservations'],$newreservation);
              } 
              else if(strlen($newreservation['email'])>5 && !User::where("email",$newreservation['email'])->exists()){ //user not in dB but in reservations
                  foreach($reservations as $reservation) {
                      $reservation=(array)$reservation;
                        if($reservation['email']==$newreservation['email']){
                      array_push($reservationstoreturn['reservations'],$reservation);}                    
                    }
              }
              else if(User::where("email",$newreservation['email'])->exists())    //user email is present in dB but not loged in
              {array_push($reservationstoreturn['reservations'],$newreservation);}  
              
          }

          if(strlen(json_encode($reservationstoreturn['reservations']))<20){
            $reservationstoreturn['reservationsreceived']=false;
            return $reservationstoreturn;
        }

          return $reservationstoreturn;
          
           
    }

    public static function get_reservation_from_id($reservation_id){

        $reservationstoreturn['reservationsreceived']=true;
        $reservationstoreturn['reservations']=[];

        $reservations=json_decode(Sites::select('reservations')->where('siteurl',Route::input("subDomain"))->first()->reservations,true);
       
        if(strlen(json_encode($reservations))<20){
            $reservationstoreturn['reservationsreceived']=false;
            return $reservationstoreturn;
        }  
      
            foreach ($reservations as $key => $reservation) {
                if($reservation['reservationid']==$reservation_id){
                    if(Auth::check()){
                    if(Auth::user()->isAdmin() || Auth::user()->email==$reservation['email']){
    
                        $reservationstoreturn['reservations']=$reservation;
                        return $reservationstoreturn;
    
                    }
                }else if(!User::where('email', '=', $reservation['email'])->exists()){
                    $reservationstoreturn['reservations']=$reservation;
                    return $reservationstoreturn;
                }else return $reservationstoreturn;
    
               
    
                }
            }
        
    }
    

    public static function send_notification($mail,$phone,$message){

        $sitejson=json_decode(Sites::select('sitejson')->where('siteurl', Route::input("subDomain"))->first()->sitejson, false);
        $reserve=$sitejson->pages->reserve;

        $sms2owner=$reserve->notifyownerbysms;
        $email2owner=$reserve->notifyownerbyemail;
        $sms2client=$reserve->notifyclientbysms;
        $email2client=$reserve->notifyclientbyemail;
        $owner_phone=$reserve->ownernotificationphone;
        $owner_mail=$reserve->ownernotificationemail;

       
            if($sms2owner && Self::check_balance('sms')){
                return SmsController::sendsms("Mackany",$owner_phone,$message['message']." [id:".$message['clientid']."] ".'https://'.Route::input("subDomain").'.mackany.com/reservation/'.$message['id']);
//send sms to owner that their is new reservation
            }
            if($email2owner && Self::check_balance('email')){
                Mail::to($owner_mail)->send(new myMail\NotificationMail("notify@mackany.com",[
                    "name"=>$message['name'],
                    "id"=>$message['id'],
                    "clientid"=>$message['clientid'],
                    "order_url"=>'https://'.Route::input("subDomain").'.mackany.com',
                ],"new_reservation"));
//send email to owner that their is new reservation
            }

        
         
            if($sms2client && Self::check_balance('sms')){
                SmsController::sendsms("Mackany",$phone,$message['message']." [id:".$message['clientid']."] ".'https://'.Route::input("subDomain").'.mackany.com/reservation/'.$message['id']);
//send sms to client that their is new reservation
            }
            if($email2client && Self::check_balance('email')){
                Mail::to($mail)->send(new myMail\NotificationMail("notify@mackany.com",[
                    "name"=>$message['name'],
                    "id"=>$message['id'],
                    "order_url"=>'https://'.Route::input("subDomain").'.mackany.com',
                    "clientid"=>$message['clientid'],
                ],"new_reservation"));
//send email to client that their is new reservation
            }

        

    }

    public static function check_balance($type){ //check banalnce for e-mail or sms
        $site=Sites::where('siteurl', Route::input("subDomain"))->first();

        $site_users=$site->user()->get();
        
        $owner_user="";
        
        foreach($site_users as $key=>$user){
            if($user->ownership->status=="owner"){$owner_user=$user;break;}
        }

        $user_plan=json_decode(json_decode($owner_user,false)->plan,false);

        // return response()->json($user_plan);

        if(  !(strlen(json_decode($owner_user,false)->plan)>5)  ){//user have no plans
          return false;
        }

        if(($user_plan->email>1&&$type=="email")||($user_plan->sms>1&&$type=="sms")){
            return true;
        }else return false;
    }




}
