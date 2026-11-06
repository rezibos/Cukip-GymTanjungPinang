<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request): RedirectResponse
    {
        $request->user()->reviews()->updateOrCreate(
            [],
            $request->safe()->only(['name', 'rating', 'review']),
        );

        return redirect()->to(route('home').'#write-review')
            ->with('review_success', 'Terima kasih! Review kamu berhasil disimpan.');
    }
}
