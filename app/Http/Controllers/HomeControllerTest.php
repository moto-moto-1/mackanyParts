<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;





class HomeControllerTest extends Controller
{

   

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function news($value)
    {
//         $json = Storage::disk('local')->get('constants/defaultjson.json');
//         $json = json_decode($json, true);
// return $json;
        return $value;
    }

    public function returnJSON($value){

        $JSONData=DB::table('sites')->select('sitejson')->where('name', $value)->first();

        $jsonPHPObject=$JSONData->sitejson;
        
        $jsonPHPObject=json_decode($jsonPHPObject);

        $jsonPHPObject->UserData->csrfToken=csrf_token();

         return json_encode($jsonPHPObject);
        
         
    }

    
    public function updateJSON(Request $request,$subDomain)
    {

        $affected = DB::table('sites')
              ->where('name', $subDomain)
              ->update(['sitejson' => $request->data]);

        return $request->data;
    }
    
}
