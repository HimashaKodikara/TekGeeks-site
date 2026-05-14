<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use illuminate\Suppoert\Models\HomeProject;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('home_projects', function (Blueprint $table) {
    $table->id();

    $table->string('name');
    $table->text('description')->nullable();

    $table->string('banner_image')->nullable();
    $table->string('content_image')->nullable();

    $table->json('techstack')->nullable();

    $table->string('company_logo')->nullable();

    $table->json('awards')->nullable();

    $table->string('case_study_link')->nullable();
    $table->string('website')->nullable();

    $table->timestamps();
    $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_projects');
    }
};
