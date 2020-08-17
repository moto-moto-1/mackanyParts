<?php

use App\Mail\VerificationMail as myMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

// Route::get('/{subDomain}', 'SitesController@index')->name("clonejsondata");


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::domain('{subDomain}.mackany.com')->group(function ( ) {
    
    // if(strpos($subDomain , "-") !== false){
    // $subDomain=str_replace("-",".",$subDomain);
    // request()->route()->setParameter('subDomain', $subDomain );
    // }
    


    Route::post('/register', 'Auth\RegisterController@register');

    Route::get('/jsondata', 'SitesController@index');
    
    
    // Route::get('/jsondata/{path}', 'SitesController@index')
    // ->where('path', '^(order/\d{1,99}|reservation/\d{1,99})$');//for listing specific order or reservation
   
    Route::post('/jsondata', 'SitesController@update');

    Route::post('/upload', 'FileUploader@incomming');

    Route::post('neworder', 'OrdersController@neworder');
    // Route::post('getorders', 'OrdersController@getorders');

    

    Route::post('/changeorderstatus', 'OrdersController@changeorderstatus');

    Route::post('newreservation', 'ReservationsController@newreservation');
    // Route::post('getreservations', 'ReservationsController@getreservations');

    Route::post('/changereservationstatus', 'ReservationsController@changereservationstatus');

    Route::resource('/manager', 'SiteUserController');


    Route::get('/', function ($subDomain) {
        $sitejson = DB::table('sites')->where('name', $subDomain)->first()->sitejson;
        return view('welcome', 
        ['paypalClientid' => json_decode($sitejson)->pages->cart->paypalClientid,
         'checkoutMerchantid' => json_decode($sitejson)->pages->cart->checkoutMerchantid,
         'theinitialstate' => json_encode($sitejson),
        ]);
        })->name("subed");

    Route::get('/{subPage}', function ($subDomain,$subPage) {
        $sitejson = DB::table('sites')->where('name', $subDomain)->first()->sitejson;
        return view('welcome', 
        ['paypalClientid' => json_decode($sitejson)->pages->cart->paypalClientid,
         'checkoutMerchantid' => json_decode($sitejson)->pages->cart->checkoutMerchantid,
         'theinitialstate' => json_encode($sitejson),
                 ]);
      })->where('subPage', '^(products|products/productscat\d{1,2}|welcome|services|services/servicescat\d{1,2}|profile|cart|order/\d{1,30}|reserve|reservation/\d{1,30}|contact|register|login|about|[aA]dmin[pP]anel)$');
});



    
    
    Route::get('/', function () {
    return view('landpage', 
    ['jwt_token' => request()->cookie('jwt')
    ,'username'=>request()->cookie('username')]);

}

);



// Route::resource('sites', 'SitesController', [
//     'only' => ['store']
// ])->middleware('CokkieAuthinticate','verified');

Route::post('sites', 'SitesController@store')->middleware('CokkieAuthinticate','verified');

Route::get('/terms', function () {return view('terms');});
Route::get('/privacy', function () {return view('privacy');});
Route::get('/helpEn', function () {return view('helpen');});
Route::get('/helpAr', function () {return view('helpar');});


Route::get('/services', function () {
$plan=json_decode(Storage::disk('local')->get('constants/pricing.json'), true);
$sms=0;
$email=0;
$image=0;
if(strlen(request()->cookie('jwt'))>20){
            
    auth()->setToken(request()->cookie('jwt'));}
if(auth()->check()){
    if(strlen(auth()->user()->plan)>5){
    $current_user_plan=json_decode(auth()->user()->plan);
    $sms=$current_user_plan->sms;
    $email=$current_user_plan->email;
    $image=$current_user_plan->image;
    }
}

    return view('Services', ['jwt_token' => request()->cookie('jwt')
                            ,'username'=>request()->cookie('username')
                            ,'smsplan' => $plan['sms']
                            ,'emailplan' => $plan['email']
                            ,'storageplan' => $plan['storage']
                            ,'current_sms' => $sms
                            ,'current_email' => $email
                            ,'current_image' => $image
                            ]);
})->middleware('CokkieAuthinticate','verified');

//  Route::get('/api/auth/logout','AuthController@logout');

// Auth::routes();
Auth::routes(['verify' => true]);


Route::post('/capture-paypal-transaction', 'CaptureOrder@captureOrder');

// Route::post('/register', 'Auth\RegisterController@register');
// Route::post('/upload', 'FileUploader@incomming');
// Route::get('/getyellowcat', 'HomeControllerTest@getcat');


// Route::get('/{subPage}', function ($subPage) {
//     return view('welcome');
// })->where('subPage', '^(products|services|cart|login|register|reserve|contact|about|[aA]dmin[pP]anel)$');;



// Route::get('/im', function ($subPage) {
//     return view('test');
// });
// Route::get('/sendsms', 'SmsController@sendsms');

// Route::get('/home', 'HomeController@index')->name('home');


Route::get('mailsend', function () { 
    Mail::to('eng.m.gouda@gmail.com')->send(new myMail("hi amazon ses works"));
});

// Route::get('/test/{id}', 'HomeControllerTest@news');

    


