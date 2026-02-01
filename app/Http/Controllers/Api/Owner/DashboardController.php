<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $profileId = $request->integer('profile_id');
        $query = Appointment::query()->when($profileId, fn ($builder) => $builder->where('profile_id', $profileId));

        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $stats = [
            'appointments_today' => (clone $query)->whereDate('start_at', $today)->count(),
            'appointments_month' => (clone $query)->whereBetween('start_at', [$monthStart, Carbon::now()])->count(),
            'revenue_month' => (clone $query)->whereBetween('start_at', [$monthStart, Carbon::now()])->sum('price'),
            'pending' => (clone $query)->where('status', 'pending')->count(),
        ];

        return response()->json($stats);
    }
}
