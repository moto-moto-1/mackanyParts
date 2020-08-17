<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //

    protected $fillable = [
        'orderid','orderdescription','ordercaptureresponse','status'
    ];

    public function User()
    {
        return $this->belongsTo('App\User');
    }
}
