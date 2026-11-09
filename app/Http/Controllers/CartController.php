<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use App\Support\MembershipCart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request, MembershipCart $cart): View
    {
        $items = $cart->items($request);

        return view('cart.index', [
            'items' => $items,
            'totalQuantity' => $items->sum('quantity'),
            'total' => $items->sum('subtotal'),
        ]);
    }

    public function store(Request $request, MembershipPlan $plan, MembershipCart $cart): RedirectResponse|JsonResponse
    {
        abort_unless($plan->is_active, 404);

        $cart->add($request, $plan);
        $message = $plan->name.' berhasil dimasukkan ke keranjang.';

        if ($request->expectsJson()) {
            return response()->json($this->payload($request, $cart, 'Masuk keranjang', $message));
        }

        return back()
            ->with('success', $message)
            ->with('notification_title', 'Masuk keranjang');
    }

    public function update(Request $request, MembershipPlan $plan, MembershipCart $cart): RedirectResponse|JsonResponse
    {
        abort_unless($plan->is_active, 404);

        $validated = $request->validate([
            'operation' => ['required', 'in:increase,decrease'],
        ]);

        $quantity = $cart->changeQuantity($request, $plan, $validated['operation']);
        $message = 'Jumlah '.$plan->name.' diperbarui.';

        if ($request->expectsJson()) {
            return response()->json([
                ...$this->payload($request, $cart, 'Keranjang diperbarui', $message),
                'quantity' => $quantity,
                'subtotal' => $plan->price * $quantity,
                'formatted_subtotal' => 'Rp'.number_format($plan->price * $quantity, 0, ',', '.'),
            ]);
        }

        return back()
            ->with('success', $message)
            ->with('notification_title', 'Keranjang diperbarui');
    }

    public function destroy(Request $request, MembershipPlan $plan, MembershipCart $cart): RedirectResponse|JsonResponse
    {
        $cart->remove($request, $plan);
        $message = $plan->name.' dihapus dari keranjang.';

        if ($request->expectsJson()) {
            return response()->json($this->payload($request, $cart, 'Keranjang diperbarui', $message));
        }

        return back()
            ->with('success', $message)
            ->with('notification_title', 'Keranjang diperbarui');
    }

    /**
     * @return array{title: string, message: string, cart_count: int, total: int, formatted_total: string}
     */
    private function payload(Request $request, MembershipCart $cart, string $title, string $message): array
    {
        $items = $cart->items($request);
        $total = $items->sum('subtotal');

        return [
            'title' => $title,
            'message' => $message,
            'cart_count' => $items->sum('quantity'),
            'total' => $total,
            'formatted_total' => 'Rp'.number_format($total, 0, ',', '.'),
        ];
    }
}
