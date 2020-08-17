<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Sites;
use Illuminate\Support\Facades\Storage;



class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->call(function () {
            $sites=Sites::all();
            
            foreach ($sites as $site) {
                

      $files = Storage::allFiles('public/'.$sites->siteurl);
      $directories = Storage::allDirectories('public/'.$sites->siteurl);
      $size=0;

      foreach($directories as $key=>$directory){
        array_push($files,...Storage::allFiles($directory));
      }

      foreach($files as $key=>$file){
        $size=$size+Storage::size($file);
      }
      $site->status->size=$size;
      $site->save();
            }
            
            // DB::table('recent_users')->delete();
        })->weekly();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
