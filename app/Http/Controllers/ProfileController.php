<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // eager loading gym agar tidak query berulang untuk setiap review
        $reviews = $user->reviews()->with('gym')->latest()->get();

        return view('profile.show', compact('user', 'reviews'));
    }
}