<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use App\Models\PersonalTrainer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(): View
    {
        $start = Carbon::today();
        $end = Carbon::today()->addDays(6);

        $sessions = ClassSession::with('gymClass')
            ->whereDate('date', '>=', $start->toDateString())
            ->whereDate('date', '<=', $end->toDateString())
            ->withCount(['activeBookings'])
            ->orderBy('date')
            ->get()
            ->groupBy(fn (ClassSession $s) => $s->date->toDateString());

        $myBookings = auth()->check()
            ? auth()->user()->classBookings()->where('status', 'booked')->get()->keyBy('class_session_id')
            : collect();
        $currentUserId = auth()->id();
        $canBook = auth()->user()?->isMember() ?? false;
        $trainers = PersonalTrainer::query()
            ->where('is_active', true)
            ->with(['slots' => function (HasMany $query) use ($currentUserId): void {
                $query->where('starts_at', '>', now())
                    ->where('starts_at', '<=', now()->addDays(60))
                    ->where(function (Builder $availability): void {
                        $availability->where('status', 'available')
                            ->orWhere('status', 'booked')
                            ->orWhere(fn (Builder $held): Builder => $held->where('status', 'held')->where('held_until', '>=', now()))
                            ->orWhere(fn (Builder $held): Builder => $held->where('status', 'held')->where('held_until', '<', now()));
                    })
                    ->orderBy('starts_at');

                if ($currentUserId) {
                    $query->with(['bookings' => function (HasMany $bookings) use ($currentUserId): void {
                        $bookings->where('user_id', $currentUserId)
                            ->whereIn('status', ['pending_payment', 'confirmed']);
                    }]);
                }
            }])
            ->orderBy('name')
            ->get();

        return view('classes.index', compact('sessions', 'start', 'end', 'myBookings', 'canBook', 'trainers'));
    }
}
