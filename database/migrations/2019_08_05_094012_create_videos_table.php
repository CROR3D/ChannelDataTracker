<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('db_id');
            $table->string('channel_db_id');
            $table->integer('user_id')->nullable();
            $table->string('id');
            $table->string('channel_id');
            $table->string('name');
            $table->integer('tracked_zero');
            $table->integer('month_zero');
            $table->integer('treshold_zero');
            $table->float('earning_factor', 8, 2);
            $table->string('factor_currency');
            $table->integer('treshold');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
