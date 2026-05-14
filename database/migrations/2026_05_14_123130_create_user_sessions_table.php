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
        Schema::create('user_sessions', function (Blueprint $table) {

            $table->string('session_id')->primary();

            $table->foreignId('user_id')->nullable()->index();

            $table->text('payload')->nullable();

            $table->integer('last_activity')->index();

            $table->timestamps(); // ADD THIS
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};