<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->enum('role', ['admin', 'inspector']);
            $table->char('site_code', 2)->nullable();
            $table->string('name', 100);
            $table->string('pin', 255)->nullable();
            $table->timestamps();

            $table->foreign('site_code')->references('code')->on('sites')->nullOnDelete();
            $table->index(['role', 'site_code']);
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id', 20)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
