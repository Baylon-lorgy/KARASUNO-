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
        Schema::connection('mongodb')->create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->string('type');
            $table->text('message');
            $table->string('severity');
            $table->string('status');
            $table->timestamp('timestamp');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('alerts');
    }
};
