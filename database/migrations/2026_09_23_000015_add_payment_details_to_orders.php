<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->after('payment_method')->constrained()->nullOnDelete();
            $table->string('payment_method_name')->nullable()->after('payment_method_id');
            $table->string('payment_account')->nullable()->after('payment_method_name');
            $table->decimal('advance_amount', 10, 2)->default(0)->after('payment_account');
            $table->string('transaction_id')->nullable()->after('advance_amount');
            $table->string('payment_status')->default('pending')->after('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_method_id');
            $table->dropColumn(['payment_method_name', 'payment_account', 'advance_amount', 'transaction_id', 'payment_status']);
        });
    }
};
