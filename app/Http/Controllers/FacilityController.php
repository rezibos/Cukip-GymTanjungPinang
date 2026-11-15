<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\GymProfile;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(): View
    {
        $facilities = Facility::query()->orderBy('category')->orderBy('name')->get();
        $profile = GymProfile::current();

        return view('facilities.index', compact('facilities', 'profile'));
    }
}
