<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;

class ServiceController extends Controller
{
    public function index(Profile $profile)
    {
        $services = $profile->services()->with('variants')->get();

        return response()->json($services);
    }
}
