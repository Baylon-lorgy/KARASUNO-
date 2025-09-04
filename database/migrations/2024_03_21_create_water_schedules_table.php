<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('water_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('day');
            $table->time('time');
            $table->integer('duration');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('water_schedules');
    }
}; 