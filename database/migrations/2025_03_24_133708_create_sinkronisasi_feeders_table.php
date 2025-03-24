<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sinkronisasi_feeders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('batch_name');
            $table->string('function_name');
            $table->string('batch_id')->nullable();
            $table->dateTime('terakhir_sinkronisasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinkronisasi_feeders');
    }
};
