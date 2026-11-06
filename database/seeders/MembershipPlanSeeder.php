<?php

namespace Database\Seeders;

use App\Models\MembershipPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipPlanSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            foreach (config('membership.catalog') as $name => $plan) {
                MembershipPlan::updateOrCreate(['name' => $name, 'duration_days' => $plan['duration_days']], [
                    'price' => $plan['price'],
                    'description' => $plan['description'],
                    'is_active' => true,
                ]);

                if (isset($plan['yearly_price'])) {
                    MembershipPlan::updateOrCreate(['name' => $name, 'duration_days' => 365], [
                        'price' => $plan['yearly_price'],
                        'description' => $plan['description'],
                        'is_active' => true,
                    ]);
                }
            }

            MembershipPlan::whereIn('name', ['Paket Harian', 'Paket Mingguan', 'Paket Bulanan', 'Paket Tahunan'])
                ->update(['is_active' => false]);
        });
    }
}
