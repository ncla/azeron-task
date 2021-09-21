<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalendarTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Schema::create('calendar_years', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('year');
            $table->unsignedBigInteger('calendar_id');
            $table->timestamps();
        });

        Schema::create('calendar_months', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('month');
            $table->unsignedBigInteger('year_id');
            $table->timestamps();
        });

        Schema::create('calendar_days', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('day');
            $table->unsignedBigInteger('month_id');
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
        foreach (['calendars', 'calendar_years', 'calendar_months', 'calendar_days'] as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }
}
