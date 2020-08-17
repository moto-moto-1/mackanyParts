<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
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

    // public function rooted()
    // {        
    //     $method=request()->method();
    //     $path=request()->path();
    //     request()->route()->setParameter('subDomain', request()->getHost() );
    //     $subDomain=Route::input("subDomain");
    //     $subDomain=str_replace(".mackany.com","",$subDomain);

    //     // return response()->json($path);

    //    if($method=="GET"&&$path=="/"){
    // // return response()->json($path);

    //     $sitejson = DB::table('sites')->where('name', $subDomain)->first()->sitejson;
    //     return view('welcome', 
    //     ['paypalClientid' => json_decode($sitejson)->pages->cart->paypalClientid,
    //         'checkoutMerchantid' => json_decode($sitejson)->pages->cart->checkoutMerchantid,
    //         'theinitialstate' => json_encode($sitejson),
    //     ]);
    // }
    // //     return response()->json($path);

    // //     if($method=="GET"&&$path=="jsondata"){

    // //         return response()->json($path);
    // //     // return redirect()->action(
    // //     //     'SitesController@index', ['subDomain' => $subDomain]
    // //     // );
    // // }


    // }
    
}
