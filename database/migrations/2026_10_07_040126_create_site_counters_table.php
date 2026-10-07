<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_counters', function (Blueprint $table) {
            $table->char('site_code', 2)->primary();
            $table->unsignedInteger('last_no')->default(0);

            $table->foreign('site_code')->references('code')->on('sites')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_counters');
    }
};
