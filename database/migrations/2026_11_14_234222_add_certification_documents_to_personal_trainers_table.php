<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('personal_trainers', 'certification_documents')) {
            Schema::table('personal_trainers', function (Blueprint $table) {
                $table->json('certification_documents')->nullable()->after('certifications');
            });
        }

        DB::table('personal_trainers')->where('name', 'Kak Bayu')->update(['session_minutes' => 180]);
        DB::table('personal_trainers')->where('name', 'Kak Rina')->update(['session_minutes' => 120]);
        DB::table('personal_trainers')->where('name', 'Kak Doni')->update(['session_minutes' => 180]);
        DB::table('personal_trainers')->where('name', 'Kak Sinta')->update(['session_minutes' => 120]);
        DB::table('personal_trainers')->where('session_minutes', '<', 120)->update(['session_minutes' => 120]);

        DB::table('trainer_slots')->orderBy('id')->eachById(function ($slot): void {
            $sessionMinutes = (int) DB::table('personal_trainers')->where('id', $slot->personal_trainer_id)->value('session_minutes');

            DB::table('trainer_slots')->where('id', $slot->id)->update([
                'ends_at' => Carbon::parse($slot->starts_at)->addMinutes($sessionMinutes),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_trainers', function (Blueprint $table) {
            $table->dropColumn('certification_documents');
        });
    }
};
