<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CreateSitesTable extends Migration
{


    public function setdefaultjson(){
        return json_decode(Storage::disk('local')->get('constants/defaultjson.json'), true);

    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        

        Schema::create('sites', function (Blueprint $table) {
            $table->bigIncrements('id')->unique();
            $table->string('siteurl')->unique();
            $table->integer('enid')->nullable();
            $table->integer('arid')->nullable();
            $table->text('name')->nullable();
            $table->string('telephone')->unique()->nullable();
            $table->string('telephone1')->unique()->nullable();
            $table->text('about')->nullable();
            $table->string('web')->unique()->nullable();
            $table->text('address')->nullable();
            $table->string('email')->unique()->nullable();
            
            $table->json('sitejson');
            $table->json('orders')->nullable();
            $table->json('reservations')->nullable();
            $table->json('status')->nullable();


            $table->timestamps();
        });

        DB::update("ALTER TABLE sites AUTO_INCREMENT = 100000;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sites');
    }
}
