<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Gym;

class HomeController extends Controller
{
    public function index()
    {
        $popularGyms = Gym::withCardData()
            ->orderByDesc('approved_reviews_avg_rating')
            ->orderByDesc('approved_reviews_count')
            ->take(3)
            ->get();

        $latestGyms = Gym::withCardData()->latest('id')->take(3)->get();

        $categories = Category::withCount('gyms')->orderBy('name')->get();

        // Untuk preview peta
        $markers = Gym::withCardData()->withCoordinates()->get()->map->toMapMarker()->values();

        return view('home', compact('popularGyms', 'latestGyms', 'categories', 'markers'));
    }
}