<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;


class remove_duplicate_ypar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yp:rm_duplicate_ar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes any duplicate objects inside arabic yp database';

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
        $all_duplicate=DB::table('yellowpagesarabic')->select("name",'search_id',DB::raw('count(*) as total_occ'))
        ->groupBy('name','cat','search_id')
        ->havingRaw('count(*) > 1')->orderBy('total_occ','desc')
        ->get();

$iso=0;
        foreach($all_duplicate as $duplicate){
    $not_delete=DB::table('yellowpagesarabic')->where('search_id',$duplicate->search_id)->first();

    DB::table('yellowpagesarabic')->where('search_id',$duplicate->search_id)->delete();

    DB::table('yellowpagesarabic')->insert((array)$not_delete);

    echo $not_delete->name." unified with search id= ".$not_delete->search_id." , number ".$iso++."\n";

        }

     


    //  print_r((array)$all_duplicate[0]->name);
        

    }
}
