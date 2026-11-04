<?php

namespace App\Http\Controllers;

use App\Models\ClassBooking;
use App\Models\ClassSession;
use Illuminate\Support\Carbon;

class ClassBookingController extends Controller
{
    public function store(ClassSession $session)
    {
        $user = auth()->user();

        if (! $user->isMember()) {
            return back()->with('error', 'Booking kelas hanya untuk member aktif. Silakan pilih paket membership dulu.');
        }

        if ($session->date->lt(\Illuminate\Support\Carbon::today())) {
            return back()->with('error', 'Sesi kelas ini sudah lewat.');
        }

        // Cegah booking dobel pada sesi yang sama
        $already = ClassBooking::where('user_id', $user->id)
            ->where('class_session_id', $session->id)
            ->where('status', 'booked')
            ->exists();

        if ($already) {
            return back()->with('error', 'Anda sudah terdaftar di kelas ini.');
        }

        if ($session->isFull()) {
            return back()->with('error', 'Kelas sudah penuh.');
        }

        ClassBooking::create([
            'user_id' => $user->id,
            'class_session_id' => $session->id,
            'status' => 'booked',
        ]);

        return back()->with('success', 'Berhasil booking kelas ' . $session->gymClass->name . '.');
    }

    public function cancel(ClassBooking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        if ($booking->status->value !== 'booked') {
            return back()->with('error', 'Booking ini sudah dibatalkan sebelumnya.');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }
}