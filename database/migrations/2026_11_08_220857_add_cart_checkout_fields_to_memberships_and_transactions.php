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
        Schema::table('memberships', function (Blueprint $table) {
            $table->unsignedSmallInteger('quantity')->default(1)->after('membership_plan_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedSmallInteger('quantity')->default(1)->after('invoice_number');
            $table->uuid('checkout_batch')->nullable()->after('quantity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['checkout_batch']);
            $table->dropColumn(['quantity', 'checkout_batch']);
        });

        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
