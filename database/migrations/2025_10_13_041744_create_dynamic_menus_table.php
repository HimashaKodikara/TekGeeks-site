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
        Schema::create('dynamic_menus', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();
            $table->string('title')->nullable();
            $table->string('route_title')->nullable();
            $table->integer('page_id')->nullable();
            $table->string('url')->nullable();
            $table->integer('parent_id')->nullable();
            $table->char('is_parent',1)->nullable();
            $table->char('show_menu',1)->nullable();
            $table->tinyInteger('parent_order')->nullable();
            $table->tinyInteger('child_order')->nullable();
            $table->double('fOrder',8,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_menus');
    }
};
