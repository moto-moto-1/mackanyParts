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



class OrdersController extends Controller
{

    public function getorders(){

        $page_number=request()->input("pagenumber")-1;
        $filter_by=request()->input("filterby");
        $items_per_page=request()->input("perpage");
       
        if(Auth::user()->isAdmin()){
         $orders_filtered=[];
         $orders=Sites::select('orders')->where('siteurl', Route::input("subDomain"))->first();
         $orders=json_decode($orders->orders);

         foreach($orders as $key=>$order){
           if($order->status==$filter_by){
            
           
            array_push($orders_filtered,$order);
           }
           else if($filter_by=="all"){
            $orders_filtered=$orders;
           }
         }
         
        $length=count($orders_filtered);

        // dump("length ".$length);
        // dump("page number ".$page_number);
       
        // dump("per number ".$items_per_page);


        if($length>=($page_number*$items_per_page)+$items_per_page){
            $last_item_position=($page_number*$items_per_page)+$items_per_page;
        }

        elseif($length<=$page_number*$items_per_page){
            return [];
        }
        
        else{
            $last_item_position=$length;
        }

        // dump("last item ".$last_item_position);
        // dump($filter_by);
         $orders_to_send=[];

         for($i=($page_number*$items_per_page);$i<$last_item_position;$i++){
            array_push($orders_to_send,$orders_filtered[$i]);
         }

        //  return response()->json($orders_to_send);
        return $orders_to_send; 


        }
    }
    
    public function neworder($domain,Request $order)
    {
        //

        if ($order->input("filtering")){
            $orderstoreturn['ordersreceived']=true;
            $orderstoreturn['orders']=$this->getorders();
            return response()->json($orderstoreturn);
        }


        $neworder = $order->except('_token');
        $neworder['date']=date('D M d Y O');
        $neworder['datetime']=date('D M d Y H:i:s O');
        $neworder['time']=date('H:i:s O');
        $neworder['order']=json_decode($neworder['order']);
        $orders=Sites::select('orders')->where('siteurl', $domain)->first();
        $neworder['status']="preparing";

        if(User::where('email', '=', $neworder['email'])->exists() && !Auth::check()){//users in dB must login before ordering
            return response()->json();}
        

        if(strlen($neworder['address'])>5 && Auth::check()){
            if($neworder['address']!=Auth::user()->otheraddress){
                $user=Auth::user();
                $user->otheraddress=$neworder['address'];
                $user->save();

            }
        }
        if(strlen($neworder['telephone'])>5 && Auth::check()){
            if($neworder['telephone']!=Auth::user()->othertelephone){
                $user=Auth::user();
                $user->othertelephone=$neworder['telephone'];
                $user->save();
            }
        }


        if(strlen($orders->orders)<10){
            $neworder['orderid']=1;
            $neworder['clientorderid']=1;
            $orders=[];
            array_push($orders,$neworder);
        }

        else{
            $orders=json_decode($orders->orders);
            $lastorder=end($orders);
            $neworder['orderid']=$lastorder->orderid+1;
            $neworder['clientorderid']=($lastorder->clientorderid+1)%9999;
            array_push($orders,$neworder);
             }
        

          $this->send_notification($neworder['email'],$neworder['telephone'],"owner","new_order",[
              "name"=>$neworder['name'],
              "id"=>$neworder['orderid'],
              "clientid"=>$neworder['clientorderid'],
              "message"=>"you have got a new order",             
              ]);


              if(!$this->update_product_store($neworder)){
                return response()->json("quantity not suffecient");
              }

              Sites::where('siteurl', $domain)
              ->update(['orders' => $orders]);
    
          
          return response()->json($this->userorders($orders,$neworder));
        //   $userorders[0]=$neworder;

    }

    public function update_product_store($neworder){
        $neworders=$neworder['order'];
        $all_products=[];
        $site_json=Sites::select('sitejson')->where('siteurl', Route::input("subDomain"))->first();
        $site_json=json_decode($site_json->sitejson);
        $products_page=$site_json->pages->products;

        // array_push($all_products,$products_page->Products);

        // foreach ($site_json->pages->products->SubPages as $key => $subpage){
        //     array_push($all_products,$subpage->Products)
        // }

        foreach ($neworders as $key => $order) {

            foreach ($products_page->Products as $key => $product) {
                if($order->productid==$product->ProductId){
                    if($order->quantity<=$product->cart->QuantityAvailable){
                        $products_page->Products[$key]->cart->QuantityAvailable=$products_page->Products[$key]->cart->QuantityAvailable-$order->quantity;
                    }
                    else{
                        return false;
                    }
                }
            }

                foreach ($products_page->SubPages as $keySub => $subpage){
                    foreach ($subpage->Products as $keyPro => $product) {
                        if($order->productid==$product->ProductId){
                            if($order->quantity<=$product->cart->QuantityAvailable){
                                $products_page->SubPages[$keySub]->Products[$keyPro]->cart->QuantityAvailable=$product->cart->QuantityAvailable-$order->quantity;
                            }
                            else{
                                return false;
                            }
                        }
                    }
                }

        }

        $site_json->pages->products=$products_page;

        Sites::where('siteurl', Route::input("subDomain"))
        ->update(['sitejson' => json_encode($site_json)]);  
        return true;

    }


    public function changeorderstatus($domain,Request $request){
        
        $orderid=$request['orderid'];
        $orderstatus=$request['status'];


if(strlen($orderstatus)<20){

        $orders=Sites::select('orders')->where('siteurl', $domain)->first()->orders;
        
        $orders=json_decode($orders, true);
        foreach ($orders as $key => $order) {
            if($order['orderid']==$orderid){
                $ordercurrneededstatus['current']=$order['status'];
                $ordercurrneededstatus['needed']=$orderstatus;

                if(($orderstatus=="paid"&&Auth::user()->isAdmin())||($orderstatus=="creditpaid")){
                    $orders[$key]['paymentstatus'] = $orderstatus;
                    $orderchanged=$orders[$key];
                    break;
                }
               
                if(!auth()->check()){    //checking an not authinticated user
                    if(User::where('email', '=', $order['email'])->exists()){return response()->json();} //if user is in dB don't allow change
                    else{//if user is not in dB allow change
                        if($orderstatus=="cancel" && ($order['status']=="reserved"||$order['status']=="preparing")){
                            $orders[$key]['status'] = $orderstatus;
                            $orderchanged=$orders[$key];
                            $this->cancelOrders($orders[$key]['order']);
                        }
                    }
                }
     
                else if (Gate::allows('change_ord_reserve_status',json_encode($ordercurrneededstatus))) {
                // if(Auth::user()->isAdmin()){
                    $orders[$key]['status'] = $orderstatus;
                    if($orderstatus=="cancel"){
                        $this->cancelOrders($orders[$key]['order']);}
                // }
                // else if($orderstatus=="cancel"){
                    // $orders[$key]['status'] = $orderstatus;
                // }
                $orderchanged=$orders[$key];
            }


            else {return response()->json();}
        
        }
        }
if($orderstatus="ondelivery"){
        $this->send_notification($orderchanged['email'],$orderchanged['telephone'],"client","order_state",[
            "name"=>$orderchanged['name'],
            "id"=>$orderchanged['orderid'],
            "clientid"=>$orderchanged['clientorderid'],
            "message"=>"hi, your order is ready ",             
            ]);}

        Sites::where('siteurl', $domain)
        ->update(['orders' => $orders]);

       return response()->json($this->userorders($orders,$orderchanged));
    }
    }

    public static function cancelOrders($orders){
        $sitejson=json_decode(Sites::select('sitejson')->where('siteurl', Route::input("subDomain"))->first()->sitejson);
        $products=$sitejson->pages->products;
        foreach($orders as $key_for_orders=>$product_chosen){

        $productId=$product_chosen['productid'];

        $product_main=$sitejson->pages->products->Products;
        $product_subpages=$sitejson->pages->products->SubPages;

         
        foreach ($product_main as $key => $product) {
            if($product->ProductId==$productId){
                $sitejson->pages->products->Products[$key]->cart->QuantityAvailable=$sitejson->pages->products->Products[$key]->cart->QuantityAvailable+$product_chosen['quantity'];
            }
        }
        foreach ($product_subpages as $key1 => $subpage) {
            foreach ($subpage->Products as $key2 => $subProduct) {
                if($subProduct->ProductId==$productId){
                    $sitejson->pages->products->SubPages[$key1]->Products[$key2]=$sitejson->pages->products->SubPages[$key1]->Products[$key2]+$product_chosen['quantity'];
                }
            }   
        }
           
        }
       
        Sites::select('sitejson')->where('siteurl', Route::input("subDomain"))
        ->update(['sitejson' => json_encode($sitejson)]);

    }
    

    public static function userorders($orders,$neworder){
        
        $orderstoreturn['ordersreceived']=true;
        $orderstoreturn['orders']=[];
        if(strlen(json_encode($neworder))>20){
        $orderstoreturn['lastorderid']=$neworder['orderid'];
        }
               
        $orders=json_decode(Sites::select('orders')->where('siteurl', Route::input("subDomain"))->first()->orders, true);
        
        if(strlen(json_encode($orders))<20){
            $orderstoreturn['ordersreceived']=false;
            return $orderstoreturn;
        }        

        if(Auth::check()){
          $user=Auth::user();
            if($user->isAdmin()){$orderstoreturn['orders']=$orders;} //user is admin
            
            else {                                                   //user is client
                foreach($orders as $ord) {
                    $ord=(array)$ord;
                      if($ord['email']==$user->email){
                    array_push($orderstoreturn['orders'],$ord);}                    
                  }
            }  
        }else {
            if(strlen($neworder['email'])<5 ){ //user didn't put address
                array_push($orderstoreturn['orders'],$neworder);
            } 
            else if(strlen($neworder['email'])>5 && !User::where("email",$neworder['email'])->exists()){ //user not in dB but in orders
                foreach($orders as $ord) {
                    $ord=(array)$ord;
                      if($ord['email']==$neworder['email']){
                    array_push($orderstoreturn['orders'],$ord);}                    
                  }
            }
            else if(User::where("email",$neworder['email'])->exists())    //user email is present in dB but not loged in
            {array_push($orderstoreturn['orders'],$neworder);}  
            
        }
        
        
        return $orderstoreturn;
        
         
    }


public static function get_order_from_id($order_id){

    $orderstoreturn['ordersreceived']=true;
        $orderstoreturn['orders']=[];
                       
        $orders=json_decode(Sites::select('orders')->where('siteurl', Route::input("subDomain"))->first()->orders, true);
        
        if(strlen(json_encode($orders))<20){
            $orderstoreturn['ordersreceived']=false;
            return $orderstoreturn;
        }     

  
        foreach ($orders as $key => $order) {
            if($order['orderid']==$order_id){
                if(Auth::check()){
                if(Auth::user()->isAdmin() || Auth::user()->email==$order['email']){

                    $orderstoreturn['orders']=$order;
                    return $orderstoreturn;

                }
            }else if(!User::where('email', '=', $order['email'])->exists()){
                $orderstoreturn['orders']=$order;
                return $orderstoreturn;
            }else return $orderstoreturn;

           

            }
        }
    
}


    public static function send_notification($mail,$phone,$to_type,$message_type,$message){

        $sitejson=json_decode(Sites::select('sitejson')->where('siteurl', Route::input("subDomain"))->first()->sitejson, false);
        $cart=$sitejson->pages->cart;

        $sms2owner=$cart->notifyownerbysms;
        $email2owner=$cart->notifyownerbyemail;
        $sms2client=$cart->notifyclientbysms;
        $email2client=$cart->notifyclientbyemail;
        $owner_phone=$cart->ownernotificationphone;
        $owner_mail=$cart->ownernotificationemail;

        if($to_type=="owner"){
            if($sms2owner && Self::check_balance('sms')){
                SmsController::sendsms("Mackany",$owner_phone,$message['message']." [id:".$message['clientid']."] ".'https://'.Route::input("subDomain").'.mackany.com/order/'.$message['id']);
//send sms to owner that their is new order
            }
            if($email2owner&&$message_type=="new_order" && Self::check_balance('email')){
                Mail::to($owner_mail)->send(new myMail\NotificationMail("notify@mackany.com",[
                    "name"=>$message['name'],
                    "id"=>$message['id'],
                    "clientid"=>$message['clientid'],
                    "order_url"=>'https://'.Route::input("subDomain").'.mackany.com',
                ],$message_type));
//send email to owner that their is new order
            }

        }
        else if($to_type=="client"){
            if($sms2client && Self::check_balance('sms')){
                SmsController::sendsms("Mackany",$phone,$message['message']." [id:".$message['clientid']."] ".'https://'.Route::input("subDomain").'.mackany.com/order/'.$message['id']);
//send sms to client that his order is on delivery
            }
            if($email2client&&$message_type="order_state" && Self::check_balance('email')){
                Mail::to($mail)->send(new myMail\NotificationMail("notify@mackany.com",[
                    "name"=>$message['name'],
                    "id"=>$message['id'],
                    "order_url"=>'https://'.Route::input("subDomain").'.mackany.com',
                    "clientid"=>$message['clientid'],
                ],$message_type));
//send email to client that his order is on delivery
            }

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
