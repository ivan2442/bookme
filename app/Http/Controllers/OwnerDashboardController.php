<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Profile;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Models\CalendarSetting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OwnerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $profiles = Profile::where('owner_id', $user->id)->pluck('id');

        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();

        $appointmentsQuery = Appointment::with(['profile', 'service', 'employee'])
            ->whereIn('profile_id', $profiles);

        $stats = [
            'appointments_today' => (clone $appointmentsQuery)->whereDate('start_at', $now)->count(),
            'appointments_month' => (clone $appointmentsQuery)->whereBetween('start_at', [$monthStart, $now])->count(),
            'revenue_month' => (clone $appointmentsQuery)->whereBetween('start_at', [$monthStart, $now])->sum('price'),
            'services' => Service::whereIn('profile_id', $profiles)->count(),
        ];

        $upcoming = (clone $appointmentsQuery)->orderBy('start_at')->limit(5)->get();

        return view('owner.dashboard', compact('stats', 'upcoming'));
    }

    private function getOwnerProfileIds(Request $request)
    {
        return Profile::where('owner_id', $request->user()->id)->pluck('id')->toArray();
    }

    public function services(Request $request): View
    {
        $profileIds = $this->getOwnerProfileIds($request);
        $services = Service::with(['variants', 'profile', 'employees'])
            ->whereIn('profile_id', $profileIds)
            ->get();
        $profiles = Profile::whereIn('id', $profileIds)->get();
        $employees = Employee::whereIn('profile_id', $profileIds)->get();

        return view('owner.services', compact('services', 'profiles', 'employees'));
    }

    public function storeService(Request $request): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'base_duration_minutes' => ['required', 'integer', 'min:1'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
        ]);

        if (!in_array($data['profile_id'], $profileIds)) {
            abort(403);
        }

        $service = Service::create([
            'profile_id' => $data['profile_id'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'category' => $data['category'],
            'base_price' => $data['base_price'],
            'base_duration_minutes' => $data['base_duration_minutes'],
            'currency' => 'EUR',
            'status' => 'published',
        ]);

        if (!empty($data['employee_ids'])) {
            $service->employees()->sync($data['employee_ids']);
            // Also sync to variants if needed, or rely on service relationship
        }

        return back()->with('status', 'Služba bola vytvorená.');
    }

    public function updateService(Request $request, Service $service): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        if (!in_array($service->profile_id, $profileIds)) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'base_duration_minutes' => ['required', 'integer', 'min:1'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
        ]);

        $service->update([
            'name' => $data['name'],
            'category' => $data['category'],
            'base_price' => $data['base_price'],
            'base_duration_minutes' => $data['base_duration_minutes'],
        ]);

        if (isset($data['employee_ids'])) {
            $service->employees()->sync($data['employee_ids']);
        }

        return back()->with('status', 'Služba bola upravená.');
    }

    public function storeVariant(Request $request, Service $service): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        if (!in_array($service->profile_id, $profileIds)) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ]);

        $service->variants()->create([
            'name' => $data['name'],
            'price' => $data['price'],
            'duration_minutes' => $data['duration_minutes'],
            'currency' => 'EUR',
        ]);

        return back()->with('status', 'Variant bol pridaný.');
    }

    public function updateVariant(Request $request, Service $service, ServiceVariant $variant): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        if (!in_array($service->profile_id, $profileIds)) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ]);

        $variant->update($data);

        return back()->with('status', 'Variant bol upravený.');
    }

    public function deleteVariant(Service $service, ServiceVariant $variant): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds(request());
        if (!in_array($service->profile_id, $profileIds)) {
            abort(403);
        }

        $variant->delete();

        return back()->with('status', 'Variant bol odstránený.');
    }

    public function employees(Request $request): View
    {
        $profileIds = $this->getOwnerProfileIds($request);
        $employees = Employee::with('profile')->whereIn('profile_id', $profileIds)->get();
        $profiles = Profile::whereIn('id', $profileIds)->get();

        return view('owner.employees', compact('employees', 'profiles'));
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
        ]);

        if (!in_array($data['profile_id'], $profileIds)) {
            abort(403);
        }

        Employee::create($data);

        return back()->with('status', 'Zamestnanec bol vytvorený.');
    }

    public function updateEmployee(Request $request, Employee $employee): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        if (!in_array($employee->profile_id, $profileIds)) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
        ]);

        $employee->update($data);

        return back()->with('status', 'Zamestnanec bol upravený.');
    }

    public function appointments(Request $request): View
    {
        $profileIds = $this->getOwnerProfileIds($request);
        $appointments = Appointment::with(['service', 'profile', 'employee'])
            ->whereIn('profile_id', $profileIds)
            ->orderBy('start_at', 'desc')
            ->paginate(30);

        return view('owner.appointments', compact('appointments'));
    }

    public function confirmAppointment(Appointment $appointment): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds(request());
        if (!in_array($appointment->profile_id, $profileIds)) {
            abort(403);
        }

        $appointment->update(['status' => 'confirmed']);

        return back()->with('status', 'Rezervácia potvrdená.');
    }

    public function deleteAppointment(Appointment $appointment): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds(request());
        if (!in_array($appointment->profile_id, $profileIds)) {
            abort(403);
        }

        $appointment->delete();

        return back()->with('status', 'Rezervácia bola odstránená.');
    }

    public function schedules(Request $request): View
    {
        $profileIds = $this->getOwnerProfileIds($request);
        $profiles = Profile::with('employees')->whereIn('id', $profileIds)->get();
        $schedules = Schedule::with(['profile', 'employee'])
            ->whereIn('profile_id', $profileIds)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('owner.schedules', compact('profiles', 'schedules'));
    }

    public function storeSchedule(Request $request): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'day_of_week' => ['nullable', 'integer', 'between:0,6'],
            'days' => ['nullable', 'array'],
            'days.*' => ['integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        if (!in_array($data['profile_id'], $profileIds)) {
            abort(403);
        }

        $days = [];
        if ($request->has('days')) {
            $days = $data['days'];
        } elseif (isset($data['day_of_week'])) {
            $days = [$data['day_of_week']];
        }

        if (empty($days)) {
            return back()->withErrors(['day_of_week' => 'Vyberte aspoň jeden deň.']);
        }

        foreach ($days as $dow) {
            Schedule::updateOrCreate([
                'profile_id' => $data['profile_id'],
                'employee_id' => isset($data['employee_id']) ? $data['employee_id'] : null,
                'day_of_week' => $dow,
            ], [
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'is_recurring' => true,
            ]);
        }

        return back()->with('status', count($days) > 1 ? 'Pracovné časy boli uložené.' : 'Pracovný čas bol uložený.');
    }

    public function deleteSchedule(Schedule $schedule): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds(request());
        if (!in_array($schedule->profile_id, $profileIds)) {
            abort(403);
        }

        $schedule->delete();

        return back()->with('status', 'Pracovný čas bol odstránený.');
    }

    public function calendarSettings(Request $request): View
    {
        $profileIds = $this->getOwnerProfileIds($request);
        $profiles = Profile::with('calendarSetting')->whereIn('id', $profileIds)->get();

        return view('owner.calendar_settings', compact('profiles'));
    }

    public function storeCalendarSettings(Request $request): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'slot_interval_minutes' => ['required', 'integer', 'min:5', 'max:120'],
            'buffer_before_minutes' => ['required', 'integer', 'min:0', 'max:120'],
            'buffer_after_minutes' => ['required', 'integer', 'min:0', 'max:120'],
            'max_advance_days' => ['required', 'integer', 'min:1', 'max:365'],
            'min_notice_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
            'cancellation_limit_hours' => ['required', 'integer', 'min:0', 'max:720'],
            'requires_confirmation' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'banner' => ['nullable', 'image', 'max:5120'],
        ]);

        if (!in_array($data['profile_id'], $profileIds)) {
            abort(403);
        }

        $profile = Profile::findOrFail($data['profile_id']);

        $updateData = [
            'description' => $data['description'] ?? null,
        ];

        if ($request->hasFile('logo')) {
            $updateData['logo_path'] = $request->file('logo')->store('profiles/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            $updateData['banner_path'] = $request->file('banner')->store('profiles/banners', 'public');
        }

        $profile->update($updateData);

        CalendarSetting::updateOrCreate(
            ['profile_id' => $data['profile_id']],
            [
                'slot_interval_minutes' => $data['slot_interval_minutes'],
                'buffer_before_minutes' => $data['buffer_before_minutes'],
                'buffer_after_minutes' => $data['buffer_after_minutes'],
                'max_advance_days' => $data['max_advance_days'],
                'min_notice_minutes' => $data['min_notice_minutes'],
                'cancellation_limit_hours' => $data['cancellation_limit_hours'],
                'requires_confirmation' => $request->boolean('requires_confirmation'),
            ]
        );

        return back()->with('status', 'Kalendár bol uložený.');
    }

    public function holidays(Request $request): View
    {
        $profileIds = $this->getOwnerProfileIds($request);
        $profiles = Profile::with('employees')->whereIn('id', $profileIds)->get();
        $holidays = Holiday::with(['profile', 'employee'])
            ->whereIn('profile_id', $profileIds)
            ->orderBy('date', 'desc')
            ->paginate(30);

        return view('owner.holidays', compact('profiles', 'holidays'));
    }

    public function storeHoliday(Request $request): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'is_closed' => ['nullable', 'boolean'],
            'reason' => ['nullable', 'string', 'max:255'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
        ]);

        if (!in_array($data['profile_id'], $profileIds)) {
            abort(403);
        }

        Holiday::create([
            ...$data,
            'is_closed' => $request->boolean('is_closed', true),
        ]);

        return back()->with('status', 'Sviatok/uzávierka bola pridaná.');
    }

    public function updateHoliday(Request $request, Holiday $holiday): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        if (!in_array($holiday->profile_id, $profileIds)) {
            abort(403);
        }

        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'is_closed' => ['nullable', 'boolean'],
            'reason' => ['nullable', 'string', 'max:255'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
        ]);

        if (!in_array($data['profile_id'], $profileIds)) {
            abort(403);
        }

        $holiday->update([
            ...$data,
            'is_closed' => $request->boolean('is_closed', true),
        ]);

        return back()->with('status', 'Sviatok bol upravený.');
    }

    public function deleteHoliday(Holiday $holiday): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds(request());
        if (!in_array($holiday->profile_id, $profileIds)) {
            abort(403);
        }

        $holiday->delete();

        return back()->with('status', 'Sviatok bol odstránený.');
    }

    public function payments(Request $request): View
    {
        $profileIds = $this->getOwnerProfileIds($request);
        return view('owner.payments');
    }
}
