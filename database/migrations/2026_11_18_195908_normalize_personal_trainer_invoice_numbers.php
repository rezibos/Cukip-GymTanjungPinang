<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('transactions')
            ->whereNotNull('trainer_booking_id')
            ->select(['id', 'created_at'])
            ->orderBy('id')
            ->each(function (object $transaction): void {
                $date = date('Ymd', strtotime($transaction->created_at));

                DB::table('transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'invoice_number' => 'INV-TR-'.$date.'-'.str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT),
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('transactions')
            ->join('trainer_bookings', 'trainer_bookings.id', '=', 'transactions.trainer_booking_id')
            ->join('payments', 'payments.id', '=', 'trainer_bookings.payment_id')
            ->select([
                'transactions.id',
                'trainer_bookings.id as trainer_booking_id',
                'payments.order_id',
            ])
            ->orderBy('transactions.id')
            ->each(function (object $transaction): void {
                DB::table('transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'invoice_number' => $transaction->order_id.'-'.$transaction->trainer_booking_id,
                    ]);
            });
    }
};
