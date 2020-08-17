<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Sites;

use Illuminate\Support\Facades\Route;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email_content;
    public $from_address;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($from_address,$email_content,$email_type)
    {
        $this->email_content = $email_content;
        $this->from_address = $from_address;
        $this->email_type = $email_type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        switch ($this->email_type) {
            case "new_order":
              $type="emails.notify.new_order_notification";
              $subject="New order received";
              break;
            case "order_state":
                $type="emails.notify.order_state_notification";
                $subject="Order is ready";
              break;
            case "new_reservation":
                $type="emails.notify.new_reservation_notification";
                $subject="New reservation";
              break;
            default:
            $type="emails.notify.new_order_notification";
          }




          
        $site=Sites::where('siteurl', Route::input("subDomain"))->first();

        $site_users=$site->user()->get();
        
        $owner_user="";
        
        foreach($site_users as $key=>$user){
            if($user->ownership->status=="owner"){$owner_user=$user;break;}
        }

        $user_plan=json_decode(json_decode($owner_user,false)->plan,false);

        // return response()->json($user_plan);

       

        


            $user_plan->email=$user_plan->email-1;
            $owner_user->plan= json_encode($user_plan);
            $owner_user->save();

            
          return $this->from($this->from_address,"Mackany Notification")
          ->subject($subject)
          ->markdown($type)
          ->with([
              'name' => $this->email_content['name'],
              'order_url' => $this->email_content['order_url'],
              'id' => $this->email_content['id'],
              'clientid' => $this->email_content['clientid'],
          ]);

           












       
    }
}
