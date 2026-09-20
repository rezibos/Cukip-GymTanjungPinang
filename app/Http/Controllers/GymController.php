<?php

namespace App\Http\Controllers;

use App\Enums\Region;
use App\Models\Category;
use App\Models\Gym;
use Illuminate\Http\Request;

class GymController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->toString();
        $region = Region::tryFrom($request->string('region')->toString());
        $district = $request->string('district')->toString();
        $categorySlug = $request->string('category')->toString();

        // Lokasi pengguna (untuk Gym Terdekat). Nilai tidak valid dianggap tidak ada.
        $lat = $this->coordinate($request->query('lat'), -90, 90);
        $lng = $this->coordinate($request->query('lng'), -180, 180);
        $nearby = $lat !== null && $lng !== null;

        $gyms = Gym::query()
            ->when($nearby, fn ($q) => $q->withDistance($lat, $lng)) // harus sebelum withCardData
            ->withCardData()
            ->search($search)
            ->inRegion($region?->value)
            ->inCategory($categorySlug)
            ->when($region && $district, fn ($q) => $q->where('district', $district))
            ->when($nearby, fn ($q) => $q->orderBy('distance'), fn ($q) => $q->orderBy('name'))
            ->paginate(9)
            ->withQueryString();

        return view('gyms.index', [
            'gyms' => $gyms,
            'categories' => Category::orderBy('name')->get(),
            'search' => $search,
            'region' => $region,
            'district' => $district,
            'categorySlug' => $categorySlug,
            'districts' => $region?->districts() ?? [],
            'nearby' => $nearby,
            'lat' => $lat,
            'lng' => $lng,
        ]);
    }

    public function show(Gym $gym)
    {
        $gym->load([
            'category',
            'facilities',
            'images',
            'packages' => fn ($q) => $q->orderBy('price'),
        ]);

        // rata-rata dan jumlah review (hanya yang approved)
        $gym->loadAvg('approvedReviews', 'rating');
        $gym->loadCount('approvedReviews');

        $reviews = $gym->approvedReviews()->with('user')->latest()->paginate(5);

        $myReview = auth()->check()
            ? $gym->reviews()->where('user_id', auth()->id())->first()
            : null;

        // Tamu yang klik "Login" dari halaman ini akan dikembalikan ke sini setelah login
        if (! auth()->check()) {
            session(['url.intended' => url()->current()]);
        }

        return view('gyms.show', compact('gym', 'reviews', 'myReview'));
    }

    // Mengubah input menjadi angka koordinat, atau null jika tidak valid
    private function coordinate(mixed $value, float $min, float $max): ?float
    {
        if (! is_numeric($value)) {
            return null;
        }

        $number = (float) $value;

        return ($number >= $min && $number <= $max) ? $number : null;
    }
}