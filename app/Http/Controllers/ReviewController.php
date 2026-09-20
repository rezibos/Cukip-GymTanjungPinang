<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Gym;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Gym $gym)
    {
        // user_id diambil dari akun yang login, BUKAN dari form.
        // Status tidak diisi di sini: review baru memakai default database ('approved'),
        // dan review yang sudah di-reject admin tidak otomatis tampil lagi saat diedit.
        Review::updateOrCreate(
            ['user_id' => $request->user()->id, 'gym_id' => $gym->id],
            $request->safe()->only(['rating', 'review'])
        );

        return redirect(route('gyms.show', $gym) . '#ulasan')
            ->with('success', 'Review Anda sudah tersimpan.');
    }
}