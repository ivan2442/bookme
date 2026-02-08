<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\CalendarSetting;
use App\Mail\BusinessRegistrationConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            return back()->withInput()->with('error', __('Invalid login credentials.'));
        }

        $request->session()->regenerate();

        $redirect = $request->user()->role === 'admin'
            ? route('admin.dashboard')
            : route('owner.dashboard');

        return redirect()->intended($redirect);
    }

    public function registerBusiness(Request $request)
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'role' => 'owner',
                'password' => Hash::make($data['password']),
            ]);

            $slug = Str::slug($data['business_name']);
            $originalSlug = $slug;
            $count = 1;
            while (Profile::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            $profile = Profile::create([
                'owner_id' => $user->id,
                'name' => $data['business_name'],
                'slug' => $slug,
                'category' => $data['category'],
                'city' => $data['city'],
                'status' => 'pending',
                'subscription_starts_at' => now(),
                'subscription_plan' => 'free',
                'timezone' => 'Europe/Bratislava',
            ]);

            CalendarSetting::create([
                'profile_id' => $profile->id,
                'slot_interval_minutes' => 30,
                'max_advance_days' => 30,
                'is_public' => true,
            ]);

            DB::commit();

            try {
                Mail::to($user->email)->send(new BusinessRegistrationConfirmation($profile));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('E-mail send failed: ' . $e->getMessage());
            }

            Auth::login($user);

            return redirect()->route('owner.dashboard')->with('status', __('Registration successful. Your business will be public after admin approval. Currently, it is fully functional via your unique link.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['registration_error' => $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('status', __('Logout successful.'));
    }
}
