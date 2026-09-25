<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('focus_areas')) {
            return;
        }

        Schema::create('focus_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('slug');
            $table->text('summary')->nullable();
            $table->text('summary_bn')->nullable();
            $table->text('details')->nullable();
            $table->text('details_bn')->nullable();
            $table->text('who_for')->nullable();
            $table->text('who_for_bn')->nullable();
            $table->text('what_to_expect')->nullable();
            $table->text('what_to_expect_bn')->nullable();
            $table->text('care_note')->nullable();
            $table->text('care_note_bn')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['service_category_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('focus_areas');
    }
};
