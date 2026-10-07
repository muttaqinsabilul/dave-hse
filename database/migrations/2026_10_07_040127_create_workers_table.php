<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->char('site_code', 2);
            $table->string('nama', 100);
            $table->string('jenis_pekerjaan', 100);
            $table->string('mandor_subkon', 100);
            $table->string('foto_path', 255);
            $table->unsignedTinyInteger('usia');
            $table->string('asal', 100);
            $table->text('riwayat_penyakit')->nullable();
            $table->date('tanggal_regis');
            $table->timestamps();

            $table->foreign('site_code')->references('code')->on('sites')->restrictOnDelete();
            $table->index(['site_code', 'nama']);
            $table->index('tanggal_regis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
