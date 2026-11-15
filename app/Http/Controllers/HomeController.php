<?php

namespace App\Http\Controllers;

use App\Enums\MembershipStatus;
use App\Models\Facility;
use App\Models\GymClass;
use App\Models\GymLocation;
use App\Models\GymProfile;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\PersonalTrainer;
use App\Models\Review;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $profile = GymProfile::current();
        $facilities = Facility::orderBy('name')->take(8)->get();
        $plans = MembershipPlan::where('is_active', true)->orderBy('duration_days')->get();
        $reviews = Review::approved()->with('user')->latest()->take(6)->get();
        $user = auth()->user();
        $canReview = $user?->everHadMembership() ?? false;
        $ownReview = $user?->reviews()->first();
        $activeMembershipCount = Membership::query()
            ->where('status', MembershipStatus::Active)
            ->whereDate('end_date', '>=', today())
            ->count();
        $activeClassCount = GymClass::active()->count();
        $facilityCount = Facility::count();
        $instructors = PersonalTrainer::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $marker = GymLocation::query()
            ->active()
            ->ordered()
            ->get()
            ->map(fn (GymLocation $location): array => [
                'name' => $location->name,
                'region' => $location->address,
                'district' => null,
                'rating' => null,
                'reviews' => null,
                'lat' => $location->latitude,
                'lng' => $location->longitude,
                'image' => $location->image_url,
                'image_position' => $location->image_position,
                'hours' => $location->opening_hours,
                'maps_url' => $location->maps_url,
                'whatsapp_url' => $location->whatsapp_url,
                'url' => null,
            ])->all();

        return view('home', compact(
            'profile',
            'facilities',
            'plans',
            'reviews',
            'canReview',
            'ownReview',
            'instructors',
            'activeMembershipCount',
            'activeClassCount',
            'facilityCount',
            'marker',
        ));
    }
}
