<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ibpr_reports', function (Blueprint $table) {
            $table->id();
            $table->char('site_code', 2);
            $table->dateTime('tanggal_realtime');
            $table->text('kegiatan');
            $table->text('bahaya');
            $table->text('risiko');
            $table->unsignedTinyInteger('likelihood');
            $table->unsignedTinyInteger('severity');
            $table->unsignedSmallInteger('skor');
            $table->enum('level', ['LOW', 'MEDIUM', 'HIGH', 'EXTREME']);
            $table->text('pengendalian')->nullable();
            $table->string('inspector_id', 20);
            $table->timestamps();

            $table->foreign('site_code')->references('code')->on('sites')->restrictOnDelete();
            $table->foreign('inspector_id')->references('id')->on('users')->restrictOnDelete();
            $table->index(['site_code', 'tanggal_realtime']);
            $table->index(['level', 'site_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ibpr_reports');
    }
};
