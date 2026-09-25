<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $heroBanner = null;
        if (Schema::hasColumn('doctors', 'hero_banner')) {
            $heroBanner = DB::table('doctors')->whereNotNull('hero_banner')->value('hero_banner');
        }

        DB::table('site_settings')->insert([
            'key' => 'hero_banner',
            'value' => $heroBanner,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
