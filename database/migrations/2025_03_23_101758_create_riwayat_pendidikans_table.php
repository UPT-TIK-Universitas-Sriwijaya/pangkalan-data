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
        Schema::create('riwayat_pendidikans', function (Blueprint $table) {
            $table->id();
            $table->string("id_registrasi_mahasiswa")->unique();
            // $table->index('id_registrasi_mahasiswa', 'idx_riwayat');
            $table->string("id_mahasiswa");
            $table->index('id_mahasiswa', 'idx_biodata');
            $table->string("nim");
            $table->string("nama_mahasiswa");
            $table->string("id_jenis_daftar");
            $table->index('id_jenis_daftar', 'idx_jenis_daftar');
            $table->string("nama_jenis_daftar");
            $table->string("id_jalur_daftar")->nullable();
            $table->string("id_periode_masuk");
            $table->index('id_periode_masuk', 'idx_periode_masuk');
            $table->string("nama_periode_masuk")->nullable();
            $table->string("id_jenis_keluar")->nullable();
            $table->string("keterangan_keluar")->nullable();
            $table->string("id_perguruan_tinggi");
            $table->string("nama_perguruan_tinggi");
            $table->string("id_prodi");
            $table->index('id_prodi', 'idx_id_prodi');
            $table->string("nama_program_studi");
            $table->string("sks_diakui")->nullable();
            $table->string("id_perguruan_tinggi_asal")->nullable();
            $table->string("nama_perguruan_tinggi_asal")->nullable();
            $table->string("id_prodi_asal")->nullable();
            $table->string("nama_program_studi_asal")->nullable();
            $table->string("jenis_kelamin");
            $table->index('jenis_kelamin', 'idx_jenis_kelamin');
            $table->string("tanggal_daftar");
            $table->string("nama_ibu_kandung");
            $table->string("id_pembiayaan")->nullable();
            $table->string("nama_pembiayaan_awal")->nullable();
            $table->string("biaya_masuk")->nullable();
            $table->string("id_bidang_minat")->nullable();
            $table->string("nm_bidang_minat")->nullable();
            $table->string("id_periode_keluar")->nullable();
            $table->string("tanggal_keluar")->nullable();
            $table->string("last_update");
            $table->string("tgl_create");
            $table->string("status_sync");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pendidikans');
    }
};
