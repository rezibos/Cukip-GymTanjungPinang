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
            $activePlanIds = [];

            foreach (config('membership.catalog') as $name => $plan) {
                $membershipPlan = MembershipPlan::updateOrCreate(['name' => $name, 'duration_days' => $plan['duration_days']], [
                    'price' => $plan['price'],
                    'original_price' => $plan['original_price'],
                    'discount_label' => $plan['discount_label'],
                    'badge' => $plan['badge'],
                    'description' => $plan['description'],
                    'benefits' => $plan['benefits'],
                    'is_active' => true,
                ]);

                $activePlanIds[] = $membershipPlan->id;
            }

            MembershipPlan::query()
                ->whereNotIn('id', $activePlanIds)
                ->update(['is_active' => false]);
        });
    }
}
