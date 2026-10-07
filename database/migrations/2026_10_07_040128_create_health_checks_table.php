<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_checks', function (Blueprint $table) {
            $table->id();
            $table->string('worker_id', 10);
            $table->date('tanggal');
            $table->unsignedSmallInteger('sistol');
            $table->unsignedSmallInteger('diastol');
            $table->decimal('suhu', 3, 1);
            $table->string('inspector_id', 20);
            $table->enum('status', ['NORMAL', 'FLAG'])->default('NORMAL');
            $table->timestamps();

            $table->foreign('worker_id')->references('id')->on('workers')->cascadeOnDelete();
            $table->foreign('inspector_id')->references('id')->on('users')->restrictOnDelete();
            $table->unique(['worker_id', 'tanggal']);
            $table->index(['tanggal', 'worker_id']);
            $table->index(['inspector_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_checks');
    }
};
