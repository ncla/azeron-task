<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalendarTables extends Migration
{
    /**
     * Run the migrations.
     * TODO: Will need indexes once the tables get bigger
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

            $table->foreign('calendar_id')
                ->references('id')
                ->on('calendars')
                ->onDelete('cascade');
        });

        Schema::create('calendar_months', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('month');
            $table->unsignedBigInteger('year_id');
            $table->timestamps();

            $table->foreign('year_id')
                ->references('id')
                ->on('calendar_years')
                ->onDelete('cascade');
        });

        Schema::create('calendar_days', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('day');
            $table->unsignedBigInteger('month_id');
            $table->timestamps();

            $table->foreign('month_id')
                ->references('id')
                ->on('calendar_months')
                ->onDelete('cascade');
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
