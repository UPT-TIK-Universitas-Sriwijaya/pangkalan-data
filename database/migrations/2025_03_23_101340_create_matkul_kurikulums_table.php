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
        Schema::create('matkul_kurikulums', function (Blueprint $table) {
            $table->id();
            $table->string('id_kurikulum');
            $table->index('id_kurikulum');
            $table->string('id_matkul');
            $table->index('id_matkul');
            $table->unique(['id_kurikulum', 'id_matkul'], 'unique_id_kurikulum_id_matkul');
            $table->integer('semester')->nullable();
            $table->boolean('apakah_wajib')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matkul_kurikulums');
    }
};
