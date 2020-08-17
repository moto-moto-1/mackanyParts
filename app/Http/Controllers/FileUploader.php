<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Sites;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;




class FileUploader extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function checkStorageAllowance($site_status){

      $plan=json_decode(Storage::disk('local')->get('constants/pricing.json'), false);

      if(($site_status['size']/1000)<($plan->freestoragelimit)){return true;}

      if($site_status['month']!=date("F")||$site_status['year']!=date("Y")){

        // deduct from balance and return true
      }else {

//dates are cuurent just update new image 

      }
 

      
    }



    public function site_size($subPage){
      $files = Storage::allFiles('public/'.$subPage);
      $directories = Storage::allDirectories('public/'.$subPage);
      $size=0;

      foreach($directories as $key=>$directory){
        array_push($files,...Storage::allFiles($directory));
      }

      foreach($files as $key=>$file){
        $size=$size+Storage::size($file);
      }
      return $size;
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function incomming(Request $request,$subPage)
    {

if(!auth()->user()->isAdmin()){return response()->json("not signed in");}


$site=Sites::select('status')->where('siteurl',Route::input("subDomain"))->first();
$status=json_decode($site->status,true);
// return response()->json(json_decode($site->siteurl,true)->siteurl);

// return response()->json($this->site_size(json_decode($site->siteurl,true)));


$size=$this->site_size($subPage);


$status['size']=$size/1000;
// $status['month']=date("F");
// $status['year']=date("Y");

//check weather month and year are today's , and if not draw from user storage credit, if balance is null reject



Sites::where('siteurl',Route::input("subDomain"))
->update(['status' => json_encode($status)]);


if(!$this->checkStorageAllowance($site)){return response()->json("can't upload anymore");}



      $file=$request->file("file");
      $action=$request->input("action");
      $type=$request->input("type");
      $subpage=$request->input("subpage");
      $index=$request->input("index");
      $mainimage=$request->input("mainimage");

      if($action=='delete'){
        $name=$request->input("name");
        $path= str_replace('/storage/', 'public/', $name);
        Storage::delete($path);
      
        return response()->json("deleted successfuly");
      
      }

      if($action=='add'){
if($type=='mainimage'){
  Storage::deleteDirectory('public/'.$subPage.'/'.$type);
  $path = $file->store('public/'.$subPage.'/'.$type);    //Working line
}
else if($mainimage=='main'){
  Storage::deleteDirectory('public/'.$subPage.'/'.$type.'/'.$subpage.'/'.$index.'/'.$mainimage);
  $path = $file->store('public/'.$subPage.'/'.$type.'/'.$subpage.'/'.$index.'/'.$mainimage);    //Working line
}
else{
  $path = $file->store('public/'.$subPage.'/'.$type.'/'.$subpage.'/'.$index.'/'.$mainimage);    //Working line
}
}

$path= str_replace('public/', '/storage/', $path);
        
         return $path;
        



        
    }

  
    
    
}
