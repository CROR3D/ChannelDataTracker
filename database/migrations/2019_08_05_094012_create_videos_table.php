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
            $table->string('id')->primary();
            $table->string('channel_id');
            $table->string('name');
            $table->integer('views');
            $table->integer('tracked_zero');
            $table->float('earning_factor', 8, 2);
            $table->string('factor_currency');
            $table->integer('monthly_views')->nullable();
            $table->integer('treshold_views');
            $table->integer('treshold');
            $table->integer('likes');
            $table->integer('dislikes');
            $table->integer('comments');
            $table->string('note')->nullable();
            $table->string('privacy');
            $table->timestamps();
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
