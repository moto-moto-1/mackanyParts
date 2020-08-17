<?php


namespace App\Http\Controllers;

// require __DIR__ . '/../../vendor/autoload.php';
use App\Http\Controllers\PayPalClient;

use Illuminate\Support\Facades\Storage;
use App\Order;
use App\User;


use PayPalCheckoutSdk\Orders\OrdersGetRequest;

use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class CaptureOrder
{

    /**
     * This function can be used to capture an order payment by passing the approved
     * order id as argument.
     * 
     * @param orderId
     * @param debug
     * @returns
     */

     

    public static function captureOrder($orderId, $debug=false)
    {

        if(!auth()->check()){return response()->json("can't complete the transaction, you must login");}

        //return response()->json($orderId);
        $orderId=request()->input('orderID');
        $order=json_decode(request()->input('order'));

         $client = PayPalClient::client();
         $OrderResponse = $client->execute(new OrdersGetRequest($orderId));
        //  return response()->json($OrderResponse);


         $orderAmount=$OrderResponse->result->purchase_units[0]->amount->value;
         $orderCurrency=$OrderResponse->result->purchase_units[0]->amount->currency_code;

         $orderPricetotal= explode(".",$orderAmount)[0];
         if($orderCurrency!='USD'){return response()->json("can't complete the transaction");}


          $orderDescription=$OrderResponse->result->purchase_units[0]->description;
        //  $plans=explode(" & ",$orderDescription);
        //  $Clientpricetotal= explode(".",$orderAmount)[0];
        // $clientsms=explode("sms",$plans[0])[1];
        // $clientemail=explode("email",$plans[1])[1];
        // $clientimage=explode("image",$plans[2])[1];
       
        // logic to check product amounts with total price
       $Clientpricetotal=$order->price;
        $clientsms=$order->sms;
        $clientemail=$order->email;
        $clientimage=$order->image;

        


       $localpricetotl=0;

        $plan=json_decode(Storage::disk('local')->get('constants/pricing.json'), false);

        foreach($plan->sms as $key=>$sms){
            if($clientsms==0){break;}
            if($sms->plan==$clientsms){
                $localpricetotl=$localpricetotl+$sms->priceUS;
            break;
            }
        }
        foreach($plan->email as $key=>$email){
            if($clientemail==0){break;}
            if($email->plan==$clientemail){
                $localpricetotl=$localpricetotl+$email->priceUS;
            break;
            }}
        foreach($plan->storage as $key=>$storage){
            if($clientimage==0){break;}
            if($storage->plan==$clientimage){$localpricetotl=$localpricetotl+$storage->priceUS;break;}
        }


        if($localpricetotl!=$Clientpricetotal){return response()->json("can't complete the transaction");}
        
        if($localpricetotl!=$orderPricetotal){return response()->json("can't complete the transaction");}
        
        $request = new OrdersCaptureRequest($orderId);
        $response = $client->execute($request);

        if($response->result->status!='COMPLETED'){return response()->json("transaction is pending");}

// $neworder=new Order();
$user=auth()->user();

$neworder = new Order([
    'orderid' => $orderId,
    'orderdescription' => $orderDescription,
    'ordercaptureresponse' => json_encode($response),
    'status' => $response->result->status  
    ]);

// $neworder->orderid=$orderId;
// $neworder->orderdescription=$orderDescription;
// $neworder->ordercaptureresponse=json_encode($response);
// $neworder->status=$response->result->status;

 $user->orders()->save($neworder);



if(strlen($user->plan)<5){ $user->plan=json_encode($order);}
else{
    $user->plan=json_decode($user->plan);
    $user->plan->sms=$user->plan->sms+$clientsms;
    $user->plan->email=$user->plan->email+$clientemail;
    $user->plan->image=$user->plan->image+$clientimage;
    $user->plan->price=$user->plan->price+$orderPricetotal;
    $user->plan=json_encode($user->plan);
}



$user->save();

if($response->result->status=='COMPLETED'){return response()->json("transaction is completed successfuly");}


        if ($debug)
        {
            print "Status Code: {$response->statusCode}\n";
            print "Status: {$response->result->status}\n";
            print "Order ID: {$response->result->id}\n";
            print "Links:\n";
            foreach($response->result->links as $link)
            {
                print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
            }
            print "Capture Ids:\n";
            foreach($response->result->purchase_units as $purchase_unit)
            {
                foreach($purchase_unit->payments->captures as $capture)
                {    
                    print "\t{$capture->id}";
                }
            }
            // To toggle printing the whole response body comment/uncomment below line
            echo json_encode($response->result, JSON_PRETTY_PRINT), "\n";
        }

        // return $response;
        return response()->json($response);

    }
}

/**
 * This is the driver function which invokes the captureOrder function with
 * <b>Approved</b> Order Id to capture the order payment.
 */
if (!count(debug_backtrace()))
{
    CaptureOrder::captureOrder('0F105083N67049335', true);
}