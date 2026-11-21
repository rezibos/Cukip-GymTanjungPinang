<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('membership_id')->nullable()->change();
            $table->foreignId('trainer_booking_id')
                ->nullable()
                ->after('membership_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
        });

        DB::table('trainer_bookings')
            ->join('payments', 'payments.id', '=', 'trainer_bookings.payment_id')
            ->select([
                'trainer_bookings.id',
                'trainer_bookings.user_id',
                'trainer_bookings.payment_id',
                'trainer_bookings.amount',
                'trainer_bookings.status',
                'trainer_bookings.created_at',
                'trainer_bookings.updated_at',
                'payments.checkout_batch',
                'payments.order_id',
                'payments.paid_at',
            ])
            ->orderBy('trainer_bookings.id')
            ->each(function (object $booking): void {
                DB::table('transactions')->insert([
                    'user_id' => $booking->user_id,
                    'payment_id' => $booking->payment_id,
                    'membership_id' => null,
                    'trainer_booking_id' => $booking->id,
                    'invoice_number' => $booking->order_id.'-'.$booking->id,
                    'quantity' => 1,
                    'checkout_batch' => $booking->checkout_batch,
                    'amount' => $booking->amount,
                    'status' => match ($booking->status) {
                        'confirmed' => 'paid',
                        'cancelled' => 'rejected',
                        default => 'pending',
                    },
                    'payment_proof' => null,
                    'paid_at' => $booking->status === 'confirmed' ? $booking->paid_at : null,
                    'admin_note' => null,
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at,
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('transactions')->whereNotNull('trainer_booking_id')->delete();

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('trainer_booking_id');
            $table->foreignId('membership_id')->nullable(false)->change();
        });
    }
};
