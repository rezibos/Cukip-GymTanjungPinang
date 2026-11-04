<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\GymProfile;
use App\Models\MembershipPlan;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        $profile = GymProfile::current();
        $facilities = Facility::orderBy('name')->take(8)->get();
        $plans = MembershipPlan::where('is_active', true)->orderBy('price')->get();
        $reviews = Review::approved()->with('user')->latest()->take(6)->get();

        $marker = ($profile->latitude !== null && $profile->longitude !== null)
            ? [[
                'name' => $profile->name,
                'region' => $profile->address,
                'district' => null,
                'rating' => null,
                'reviews' => null,
                'lat' => $profile->latitude,
                'lng' => $profile->longitude,
                'url' => null,
            ]]
            : [];

        return view('home', compact('profile', 'facilities', 'plans', 'reviews', 'marker'));
    }
}