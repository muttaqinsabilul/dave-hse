<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_matrix', function (Blueprint $table) {
            $table->unsignedTinyInteger('likelihood');
            $table->unsignedTinyInteger('severity');
            $table->unsignedSmallInteger('skor');
            $table->enum('level', ['LOW', 'MEDIUM', 'HIGH', 'EXTREME']);

            $table->primary(['likelihood', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_matrix');
    }
};
