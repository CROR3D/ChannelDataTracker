<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelDailyTracker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_daily_tracker', function (Blueprint $table) {
            $table->increments('db_id');
            $table->string('channel_db_id');
            $table->integer('user_id')->nullable();
            $table->string('channel_id');
            $table->string('day1')->nullable();
            $table->string('day2')->nullable();
            $table->string('day3')->nullable();
            $table->string('day4')->nullable();
            $table->string('day5')->nullable();
            $table->string('day6')->nullable();
            $table->string('day7')->nullable();
            $table->string('day8')->nullable();
            $table->string('day9')->nullable();
            $table->string('day10')->nullable();
            $table->string('day11')->nullable();
            $table->string('day12')->nullable();
            $table->string('day13')->nullable();
            $table->string('day14')->nullable();
            $table->string('day15')->nullable();
            $table->string('day16')->nullable();
            $table->string('day17')->nullable();
            $table->string('day18')->nullable();
            $table->string('day19')->nullable();
            $table->string('day20')->nullable();
            $table->string('day21')->nullable();
            $table->string('day22')->nullable();
            $table->string('day23')->nullable();
            $table->string('day24')->nullable();
            $table->string('day25')->nullable();
            $table->string('day26')->nullable();
            $table->string('day27')->nullable();
            $table->string('day28')->nullable();
            $table->string('day29')->nullable();
            $table->string('day30')->nullable();
            $table->string('day31')->nullable();
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
        Schema::dropIfExists('channel_daily_tracker');
    }
}
