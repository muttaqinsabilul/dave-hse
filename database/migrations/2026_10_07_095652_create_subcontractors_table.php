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
        Schema::create('subcontractors', function (Blueprint $table) {
            $table->id();
            $table->char('site_code', 2);
            $table->string('nama', 100);
            $table->string('bidang', 100)->nullable();
            $table->string('kontak', 100)->nullable();
            $table->timestamps();

            $table->foreign('site_code')->references('code')->on('sites')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcontractors');
    }
};
