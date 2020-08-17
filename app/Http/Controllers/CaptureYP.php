<?php

namespace App\Http\Controllers;


getcat();



function get_cat_in_page($page){

    $categories=array();

    $page_cat_count=substr_count($page,"<a href=");

    for($i=0;$i<$page_cat_count;$i++){

    $link_position1=strpos($page,'<a href="');

    $link_position2=strpos($page,'</a>');

    $link_length=$link_position2-$link_position1;

    $tagstring=substr($page, $link_position1, $link_length);

    $link=get_string_between($tagstring,'<a href="','">');
    $cat=get_string_between($tagstring,'>','(');
    $cat_number=get_string_between($tagstring,'(',')');

    array_push($categories,array($link,$cat,$cat_number));


    $page=substr($page, $link_position2+4);
    }
    
    return $categories;
   
}

function get_companys_in_cat($cat){

    
    $client = new \GuzzleHttp\Client();

    $company_list=array();
    $last_page_number=1;

    $response = $client->request('GET', 'https://www.yellowpages.com.eg'.$cat.'/p1');

    $pagedata=$response->getBody();

    if(strpos($pagedata,'aria-label="Last"')!==false){
        $end_pos=strpos($pagedata,'aria-label="Last"');
        $last_page_tag=substr($pagedata,$end_pos-8,$end_pos);
        $last_page_number=get_string_between($last_page_tag,'/p','" aria-label');
        
        }

        for($ii=1;$ii<=$last_page_number;$ii++){
    $response = $client->request('GET', 'https://www.yellowpages.com.eg'.$cat.'/p'.$ii);

    $pagedata=$response->getBody();

    $company_count=substr_count($pagedata,"</company-result");

    $remaining_companies_tags=$pagedata;

    for($ll=1;$ll<=$company_count;$ll++){

    $company_tags=get_string_between($remaining_companies_tags,'<company-result','</company-result');

    
    $original_company_tags=$company_tags;

    $company_title=get_string_between($company_tags,'<strong>','</strong>');  //*

    $company_tags = substr($company_tags, strpos($company_tags, '</strong>')+9);

    $company_address_tag=get_string_between($company_tags,'<a','a>');

    $company_address=get_string_between($company_address_tag,">","</");  //*

    $company_about="";
    if(strpos($original_company_tags,'class="aboutUs')!==false){
    $company_tags = substr($company_tags, strpos($company_tags, 'class="aboutUs')-3);

    $company_about=get_string_between($company_tags,">","</a>");  //*
    }

    $company_id=get_string_between($company_tags,'companyIdSearch":"','","serviceCode');  //*
    
    $phones_response = $client->request('GET', 'https://www.yellowpages.com.eg/en/getPhones/'.$company_id.'/false');
    $phone_response_tags=$phones_response->getBody();
    $company_other_phones=get_string_between($phone_response_tags,"data-content='","'>+");  //*

    $other_branches="";
    // try{
    //  $branchs_response = $client->request('GET', 'https://www.yellowpages.com.eg/en/profile/'.$company_title.'/'.$company_id);
    //  $branchs_response_tags=$branchs_response->getBody();
    //  $branchs_response_tags= substr($company_tags, strpos($company_tags, 'id="branches"'));
    //  $other_branches=get_string_between($company_tags,'branches','"</div>');  //*
    // }
    // catch (RequestException $exception) {
    //     return back()->withError($exception->getMessage())->withInput();
        // return $exception->getMessage();
        // return "hi";
    // }
    // return $branchs_response_tags;

    $company_tags = substr($company_tags, strpos($company_tags, 'href="tel')-3);

    $company_tel=get_string_between($company_tags,'href="tel:','" class');  //*

    $company_tags = substr($company_tags, strpos($company_tags, 'href="https://maps.google.com/maps/?q=')-3);

    $company_map=get_string_between($company_tags,'href="','" data');  //*

    $company_web="";
    if(strpos($original_company_tags,'title="Website"')!==false){
        $company_tags = substr($original_company_tags, strpos($original_company_tags, 'title="Website">'));
        $company_web=get_string_between($company_tags,'href="','" data');  //*
        }

   
    array_push($company_list,array($company_id,$company_title,$company_address,$company_about,$company_tel,$company_other_phones,$company_web,$company_map));

    
    $remaining_companies_tags = substr($remaining_companies_tags, strpos($remaining_companies_tags, '</company-result')+16);
    $company_tags =$remaining_companies_tags;
    //  return $original_company_tags;

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

function getcat()
{
    // return get_companys_in_cat("/en/condensed-category/abrasives");

$all_companies_data=array();
    $client = new \GuzzleHttp\Client();

    $all_cat=array();
    //$cat_pages_num=35;   //original
    $cat_pages_num=1;

    for($i=1;$i<=$cat_pages_num;$i++){
    
    $response = $client->request('GET', 'https://www.yellowpages.com.eg/en/related-categories/p'.$i);

    $pagedata=$response->getBody();

    $info=get_string_between($pagedata,'container-related-categories">',"row pagination-related-categories");

    
    array_push($all_cat,get_cat_in_page($info));
    


    //$page_categories=count($all_cat[$i-1]);    //original
    $page_categories=1;
    for($perpage=0;$perpage<$page_categories;$perpage++){
         
    //     return $perpage;
    //   array_push($all_companies_data,get_companys_in_cat($all_cat[$i-1][$perpage][0]));
$companies_in_category=get_companys_in_cat($all_cat[$i-1][$perpage][0]);
return count();
foreach ($companies_in_category as $line) {
$temp_array=array_merge($all_cat[$i-1][$perpage],$line);
$file=fopen("companies.csv","w");
fputcsv($file, $temp_array);
fclose($file);
}

      
    }


    
    
    }

    // return count($all_cat[0]);

    
    
return $all_cat;

// return get_companys_in_cat($all_cat[35][0][0]);

}















?>