<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\GymProfile;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::orderBy('name')->get();
        $profile = GymProfile::current();

        return view('facilities.index', compact('facilities', 'profile'));
    }
}