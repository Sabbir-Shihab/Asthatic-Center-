<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('headline')->nullable();
            $table->text('short_description')->nullable();
            $table->text('overview')->nullable();
            $table->text('who_for')->nullable();
            $table->text('what_to_expect')->nullable();
            $table->text('care_note')->nullable();
            $table->string('image')->nullable();
            $table->json('focus_areas')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};
