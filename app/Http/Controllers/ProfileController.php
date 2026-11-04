<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $activeMembership = $user->activeMembership();

        // Riwayat membership (termasuk yang sudah kedaluwarsa/dibatalkan), terbaru dulu
        $memberships = $user->memberships()->with('plan')->latest()->get();

        // Riwayat transaksi, terbaru dulu
        $transactions = $user->transactions()->with('membership.plan')->latest()->get();

        // Booking kelas yang akan datang (tanggal sesi >= hari ini, status masih booked)
        $upcomingBookings = $user->classBookings()
            ->with('session.gymClass')
            ->where('status', 'booked')
            ->whereHas('session', fn ($q) => $q->whereDate('date', '>=', now()))
            ->get()
            ->sortBy(fn ($booking) => $booking->session->date)
            ->values();

        return view('profile.show', compact(
            'user', 'activeMembership', 'memberships', 'transactions', 'upcomingBookings'
        ));
    }
}