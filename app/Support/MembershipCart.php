<?php

namespace App\Support;

use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MembershipCart
{
    /**
     * @return Collection<int, int>
     */
    public function quantities(Request $request): Collection
    {
        $storedCart = $request->session()->get('membership_cart', []);

        if (! is_array($storedCart)) {
            return collect();
        }

        $quantities = [];
        $usesLegacyList = array_is_list($storedCart);
        $usesCurrentFormat = $request->session()->get('membership_cart_version') === 2;

        foreach ($storedCart as $key => $value) {
            $planId = $usesLegacyList ? (int) $value : (int) $key;
            $quantity = $usesCurrentFormat && ! $usesLegacyList ? (int) $value : 1;

            if ($planId > 0 && $quantity > 0) {
                $quantities[$planId] = $quantity;
            }
        }

        return collect($quantities);
    }

    /**
     * @return Collection<int, array{plan: MembershipPlan, quantity: int, subtotal: int}>
     */
    public function items(Request $request): Collection
    {
        $quantities = $this->quantities($request);
        $plans = MembershipPlan::query()
            ->where('is_active', true)
            ->whereKey($quantities->keys())
            ->get()
            ->keyBy('id');

        $items = $quantities->map(function (int $quantity, int $planId) use ($plans): ?array {
            $plan = $plans->get($planId);

            if (! $plan) {
                return null;
            }

            return [
                'plan' => $plan,
                'quantity' => $quantity,
                'subtotal' => $plan->price * $quantity,
            ];
        })->filter()->values();

        $this->put($request, $items->mapWithKeys(
            fn (array $item): array => [$item['plan']->id => $item['quantity']],
        ));

        return $items;
    }

    public function add(Request $request, MembershipPlan $plan): int
    {
        $quantities = $this->quantities($request);
        $quantity = ((int) $quantities->get($plan->id, 0)) + 1;
        $quantities->put($plan->id, $quantity);
        $this->put($request, $quantities);

        return $quantity;
    }

    public function changeQuantity(Request $request, MembershipPlan $plan, string $action): int
    {
        $quantities = $this->quantities($request);
        $currentQuantity = max(1, (int) $quantities->get($plan->id, 1));
        $quantity = $action === 'increase' ? $currentQuantity + 1 : max(1, $currentQuantity - 1);
        $quantities->put($plan->id, $quantity);
        $this->put($request, $quantities);

        return $quantity;
    }

    public function remove(Request $request, MembershipPlan $plan): void
    {
        $quantities = $this->quantities($request);
        $quantities->forget($plan->id);
        $this->put($request, $quantities);
    }

    public function clear(Request $request): void
    {
        $request->session()->forget(['membership_cart', 'membership_cart_version']);
    }

    /**
     * @param  Collection<int, int>  $quantities
     */
    private function put(Request $request, Collection $quantities): void
    {
        $request->session()->put('membership_cart', $quantities->all());
        $request->session()->put('membership_cart_version', 2);
    }
}
