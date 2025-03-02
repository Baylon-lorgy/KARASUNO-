<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mongodb')->create('cache', function (Blueprint $table) {
            $table->string('key');
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::connection('mongodb')->create('cache_locks', function (Blueprint $table) {
            $table->string('key');
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('cache');
        Schema::connection('mongodb')->dropIfExists('cache_locks');
    }
};
