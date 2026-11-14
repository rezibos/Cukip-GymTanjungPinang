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
        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('creates_account')->default(false)->after('status');
            $table->text('account_password')->nullable()->after('creates_account');
            $table->timestamp('credentials_sent_at')->nullable()->after('account_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'creates_account',
                'account_password',
                'credentials_sent_at',
            ]);
        });
    }
};
