<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Profile::query()->where('status', 'published');

        if ($request->filled('q')) {
            $search = $request->string('q')->toString();

            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('category', 'like', '%'.$search.'%')
                    ->orWhere('city', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->string('city')->toString());
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category')->toString());
        }

        $profiles = $query
            ->with([
                'services' => function ($q) {
                    $q->select('id', 'profile_id', 'name', 'category', 'base_price', 'base_duration_minutes', 'is_active');
                },
                'services.employees:id,name',
                'services.variants' => function ($q) {
                    $q->select('id', 'service_id', 'name', 'duration_minutes', 'price', 'currency', 'buffer_before_minutes', 'buffer_after_minutes');
                },
                'services.variants.employees:id,name',
                'employees:id,profile_id,name',
            ])
            ->paginate($request->integer('per_page', 15));

        return response()->json($profiles);
    }

    public function show(Profile $profile)
    {
        $profile->load(['services.variants', 'employees', 'calendarSetting']);

        return response()->json($profile);
    }
}
