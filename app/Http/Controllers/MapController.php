<?php

namespace App\Http\Controllers;

use App\Models\Gym;

class MapController extends Controller
{
    public function index()
    {
        $gyms = Gym::withCardData()->withCoordinates()->orderBy('name')->get();

        return view('map', [
            'markers' => $gyms->map->toMapMarker()->values(),
            'withoutCoordinates' => Gym::count() - $gyms->count(),
        ]);
    }
}