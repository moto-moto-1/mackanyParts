<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;



class CaptureypUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yp:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update existing yellowpages Database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Self::getcat();
        // 
    }


    
    function get_cat_in_page($page){

        $categories=array();

        $page_cat_count=substr_count($page,"<a href=");

        for($i=0;$i<$page_cat_count;$i++){

        $link_position1=strpos($page,'<a href="');

        $link_position2=strpos($page,'</a>');

        $link_length=$link_position2-$link_position1;

        $tagstring=substr($page, $link_position1, $link_length);

        $link=Self::get_string_between($tagstring,'<a href="','">');
        $cat=Self::get_string_between($tagstring,'>','(');
        $cat_number=Self::get_string_between($tagstring,'(',')');

        array_push($categories,array($link,$cat,$cat_number));


        $page=substr($page, $link_position2+4);
        }
        
        return $categories;
       
    }

    function get_companys_in_cat($cat){
        echo $cat."\n";
        
        $client = new \GuzzleHttp\Client();

        $company_list=array();
        $last_page_number=1;

        $response = $client->request('GET', 'https://www.yellowpages.com.eg'.$cat.'/p1');
  
        $pagedata=$response->getBody();

        if(strpos($pagedata,'aria-label="Last"')!==false){
            $end_pos=strpos($pagedata,'aria-label="Last"');
            $last_page_tag=substr($pagedata,$end_pos-8,$end_pos);
            $last_page_number=Self::get_string_between($last_page_tag,'/p','" aria-label');
                    
            }
            
            echo "last page in this category is ".$last_page_number."\n";
            
            
             $last_page_number=1544;//not original


            for($ii=1;$ii<=$last_page_number;$ii++){//start is changed
        $response = $client->request('GET', 'https://www.yellowpages.com.eg'.$cat.'/p'.$ii); //original

        $pagedata=$response->getBody();

        if (strpos($pagedata, 'no results were found') !== false){echo "End of pages\n";return $company_list;}

        $company_count=substr_count($pagedata,"</company-result");
        // echo "number of companies in page".$ii." is ".$company_count."\n";
        $remaining_companies_tags=$pagedata;

        for($ll=1;$ll<=$company_count;$ll++){

        $company_tags=Self::get_string_between($remaining_companies_tags,'<company-result','</company-result');

        
        $original_company_tags=$company_tags;

        $company_title=Self::get_string_between($company_tags,'<strong>','</strong>');  //*

       

        $company_tags = substr($company_tags, strpos($company_tags, '</strong>')+9);

        $company_address_tag=Self::get_string_between($company_tags,'<a','a>');

        $company_address=Self::get_string_between($company_address_tag,">","</");  //*

        $company_about="";
        if(strpos($original_company_tags,'class="aboutUs')!==false){
        $company_tags = substr($company_tags, strpos($company_tags, 'class="aboutUs')-3);

        $company_about=Self::get_string_between($company_tags,">","</a>");  //*
        }

        $company_id=Self::get_string_between($company_tags,'companyIdSearch":"','","serviceCode');  //*
        if($company_id==""){$company_id="111";}
        if(DB::table('yellowpages')->where('search_id', $company_id)->exists()){continue;}
        echo "\n"."new company found ".$company_title." in category ".$cat."\n";
        $text2append="\n"."new company found ".$company_title." in category ".$cat."\n";

        Storage::append('englishYP.log', $text2append);

        $phones_response = $client->request('GET', 'https://www.yellowpages.com.eg/en/getPhones/'.$company_id.'/false');
        $phone_response_tags=$phones_response->getBody();
        $company_other_phones=Self::get_string_between($phone_response_tags,"data-content='","'>+");  //*

        $other_branches="";
        // try{
        //  $branchs_response = $client->request('GET', 'https://www.yellowpages.com.eg/en/profile/'.$company_title.'/'.$company_id);
        //  $branchs_response_tags=$branchs_response->getBody();
        //  $branchs_response_tags= substr($company_tags, strpos($company_tags, 'id="branches"'));
        //  $other_branches=Self::get_string_between($company_tags,'branches','"</div>');  //*
        // }
        // catch (RequestException $exception) {
        //     return back()->withError($exception->getMessage())->withInput();
            // return $exception->getMessage();
            // return "hi";
        // }
        // return $branchs_response_tags;

        $company_tags = substr($company_tags, strpos($company_tags, 'href="tel')-3);

        $company_tel=Self::get_string_between($company_tags,'href="tel:','" class');  //*

        $company_tags = substr($company_tags, strpos($company_tags, 'href="https://maps.google.com/maps/?q=')-3);

        $company_map=Self::get_string_between($company_tags,'href="','" data');  //*

        $company_web="";
        if(strpos($original_company_tags,'title="Website"')!==false){
            $company_tags = substr($original_company_tags, strpos($original_company_tags, 'title="Website">'));
            $company_web=Self::get_string_between($company_tags,'href="','" data');  //*
            }

       
        array_push($company_list,array($company_id,$company_title,$company_address,$company_about,$company_tel,$company_other_phones,$company_web,$company_map));

        
        $remaining_companies_tags = substr($remaining_companies_tags, strpos($remaining_companies_tags, '</company-result')+16);
        $company_tags =$remaining_companies_tags;
        //  return $original_company_tags;
        echo "\n";
        echo "Company no ".$ll." done in page ".$ii." category ".$cat;
            }
        }

            return $company_list;

    }
   
    function get_string_between($string, $start, $end){
        
        
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        
        
        $stringfound=substr($string, $ini, $len);

        return $stringfound;
    }

    public function getcat()
    {

$all_companies_data=array();
        $client = new \GuzzleHttp\Client();

        $all_cat=array();
        $cat_pages_num=35;   //original
        // $cat_pages_num=1;

        for($i=1;$i<=$cat_pages_num;$i++){

            
            echo "Main categories in page ".$i."\n";
        
        $response = $client->request('GET', 'https://www.yellowpages.com.eg/en/related-categories/p'.$i);
  
        $pagedata=$response->getBody();

        $info=Self::get_string_between($pagedata,'container-related-categories">',"row pagination-related-categories");

        
        array_push($all_cat,Self::get_cat_in_page($info));
        
// print_r($all_cat[$i-2]);

        $page_categories=count($all_cat[$i-1]);    //original
        // $page_categories=2;
        echo "is ".$page_categories."\n";

        
        for($perpage=0;$perpage<$page_categories;$perpage++){

            // if($all_cat[$i-1][$perpage][2]<5000){echo "we will break";continue;}
            echo "companies in ".$all_cat[$i-1][$perpage][0]." cat is ".$all_cat[$i-1][$perpage][2]."\n";

        $companies_in_category=Self::get_companys_in_cat($all_cat[$i-1][$perpage][0]);  //original
        

foreach ($companies_in_category as $line) {
    $temp_array=array_merge($all_cat[$i-1][$perpage],$line);


    DB::table('yellowpages')->insert(
        ['url' => $temp_array[0],
        'cat' => $temp_array[1],
        'cat_num' => $temp_array[2],
        'search_id' => $temp_array[3],
        'name' => $temp_array[4],
        'address' => $temp_array[5],
        'about' => $temp_array[6],
        'tel' => $temp_array[7],
        'other_tel' => $temp_array[8],
        'web' => $temp_array[9],
        'map' => $temp_array[10]
         ]
    );

    }
          
        }   
   
        }

               
  Echo "\n";      
echo "All finished";



    }





}
