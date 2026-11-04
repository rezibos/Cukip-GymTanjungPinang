<?php

namespace Database\Seeders;

use App\Models\MembershipPlan;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $plan = MembershipPlan::where('name', 'Paket Bulanan')->first();
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            // Buat membership aktif
            $membership = $user->memberships()->create([
                'membership_plan_id' => $plan->id,
                'status' => 'active',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
            ]);

            // Transaksi lunas terkait
            Transaction::create([
                'user_id' => $user->id,
                'membership_id' => $membership->id,
                'invoice_number' => Transaction::generateInvoiceNumber(),
                'amount' => $plan->price,
                'status' => 'paid',
                'paid_at' => now()->subDays(5),
            ]);

            // Review (karena sudah pernah punya membership)
            Review::create([
                'user_id' => $user->id,
                'rating' => rand(4, 5),
                'review' => 'Tempatnya bersih dan pelatihnya ramah.',
                'status' => 'approved',
            ]);
        }
    }
}