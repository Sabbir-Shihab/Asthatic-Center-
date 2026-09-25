<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_methods')) {
            Schema::create('payment_methods', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('account_number')->nullable();
                $table->decimal('advance_amount', 10, 2)->default(0);
                $table->text('instructions')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(false);
                $table->timestamps();
            });
        }

        $now = now();
        foreach ([
            ['bKash', 'bkash', 1],
            ['Rocket', 'rocket', 2],
            ['Nagad', 'nagad', 3],
        ] as [$name, $slug, $order]) {
            $exists = DB::table('payment_methods')->where('slug', $slug)->exists();
            if ($exists) {
                continue;
            }
            DB::table('payment_methods')->insert([
                'name' => $name,
                'slug' => $slug,
                'account_number' => null,
                'advance_amount' => 0,
                'instructions' => 'Send the delivery advance, then submit the transaction ID.',
                'sort_order' => $order,
                'is_active' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
