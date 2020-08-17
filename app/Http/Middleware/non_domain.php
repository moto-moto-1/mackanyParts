<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;



use Closure;

class non_domain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // return response()->json("hi");    

        if($request->getHost()!="mackany.com"){

            $request->route()->setParameter('subDomain', $request->getHost() );

            

           

            // if($request->method()=="GET"&&$request->path()=="/")
            // {
                $subDomain=Route::input("subDomain");
                $subDomain=str_replace(".mackany.com","",$subDomain);
                // return response()->json($subDomain);    
                // $sitejson = DB::table('sites')->where('name', $subDomain)->first()->sitejson;
                // return view('welcome', 
                // ['paypalClientid' => json_decode($sitejson)->pages->cart->paypalClientid,
                //  'checkoutMerchantid' => json_decode($sitejson)->pages->cart->checkoutMerchantid,
                //  'theinitialstate' => json_encode($sitejson),
                // ]);
                // return redirect()->route('subed',["subDomain"=>$subDomain]);
                // return redirect()->action('HomeController@rooted');
                if($request->method()=="GET"&&$request->path()=="/"){
                return redirect()->route('rooted');
            }

            if($request->method()=="POST"&&$request->path()=="neworder"){
                return redirect()->route('neworder',["Domain"=>$subDomain]);
            }




                if($request->method()=="GET"&&$request->path()=="jsondata"){
                    // return response()->json("his");    
                    return redirect()->route('jsondata',["subDomain"=>$subDomain]);
                }


                else if($request->method()=="GET"){
                    return redirect()->route('subp',["subPage"=>$subDomain]);
                }

            //         // return response()->json("his");    
            // if($request->method()=="GET"&&$request->path()=="jsondata"){
            // return redirect()->route('clonejsondata',["subDomain"=>$subDomain]);
            // }
            //     }
                
                // }
            
                // return redirect()->route(
                //     'clonejsondata', ['subDomain' => $subDomain]
                // );
            
            // return response()->json($request->method());     
        }

        return $next($request);
    }
}
