<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Profile;

class HomeController extends Controller
{
    public function index()
    {
        $latestArticles = Article::whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('home', compact('latestArticles'));
    }

    public function forBusinesses()
    {
        return view('for-businesses');
    }

    public function showProfile($slug)
    {
        $profile = Profile::with(['services.variants', 'employees', 'calendarSetting', 'schedules'])
            ->where('slug', $slug)
            ->whereIn('status', ['published', 'pending'])
            ->whereHas('calendarSetting', function ($q) {
                $q->where('is_public', true);
            })
            ->firstOrFail();

        // Precalculate closed days for the next 35 days
        $availability = app(\App\Services\AvailabilityService::class);
        $workingWindows = $availability->getWorkingWindows($profile, now()->startOfDay(), 35);
        $closedDays = [];
        foreach ($workingWindows as $date => $windows) {
            if (empty($windows)) {
                $closedDays[] = $date;
            }
        }

        return view('profiles.show', compact('profile', 'closedDays'));
    }
}
