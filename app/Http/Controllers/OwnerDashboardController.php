<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Invoice;
use App\Models\Profile;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Models\CalendarSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OwnerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $profileIds = Profile::where('owner_id', $user->id)->pluck('id');

        $now = Carbon::now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();
        $monthStart = $now->copy()->startOfMonth();

        // Base query for stats - NO eager loading here for better performance
        $baseQuery = Appointment::whereIn('profile_id', $profileIds);

        $stats = [
            'appointments_today' => (clone $baseQuery)->whereBetween('start_at', [$todayStart, $todayEnd])->count(),
            'appointments_month' => (clone $baseQuery)->whereBetween('start_at', [$monthStart, $now])->count(),
            'services' => Service::whereIn('profile_id', $profileIds)->count(),
            'revenue_today' => (clone $baseQuery)
                ->whereBetween('start_at', [$todayStart, $todayEnd])
                ->where('status', 'completed')
                ->sum('price'),
        ];

        // Fetch today's appointments with eager loading (including completed)
        $upcoming = (clone $baseQuery)
            ->with(['profile', 'service', 'employee'])
            ->whereBetween('start_at', [$todayStart, $todayEnd])
            ->orderBy('start_at')
            ->get();

        $allProfiles = Profile::with('employees')->whereIn('id', $profileIds)->get();

        return view('owner.dashboard', compact('stats', 'upcoming', 'allProfiles'));
    }

    public function getAppointmentsForDay(Request $request): JsonResponse
    {
        $user = $request->user();
        $profileIds = Profile::where('owner_id', $user->id)->pluck('id');
        $date = $request->date;
        $dayStart = Carbon::parse($date)->startOfDay();
        $dayEnd = Carbon::parse($date)->endOfDay();

        $query = Appointment::whereIn('profile_id', $profileIds)
            ->whereBetween('start_at', [$dayStart, $dayEnd]);

        $revenue = (clone $query)->where('status', 'completed')->sum('price');

        $appointments = (clone $query)
            ->with(['profile', 'service', 'employee'])
            ->orderBy('start_at')
            ->get()
            ->map(function($a) {
                $daysNames = [1=>'Pondelok',2=>'Utorok',3=>'Streda',4=>'Štvrtok',5=>'Piatok',6=>'Sobota',0=>'Nedeľa'];
                return [
                    'id' => $a->id,
                    'customer_name' => $a->customer_name,
                    'customer_phone' => $a->customer_phone,
                    'service_name' => $a->metadata['service_name_manual'] ?? ($a->service?->name ?? 'Manuálna služba'),
                    'employee_name' => optional($a->employee)->name ?? 'Bez zamestnanca',
                    'start_time' => $a->start_at->format('H:i'),
                    'end_time' => $a->end_at->format('H:i'),
                    'date_formatted' => $a->start_at->format('d.m.Y'),
                    'day_name' => $daysNames[$a->start_at->dayOfWeek] ?? '',
                    'price' => number_format($a->price, 2),
                    'status' => $a->status,
                    'duration_minutes' => $a->metadata['duration_minutes'] ?? (int) (($a->end_at->timestamp - $a->start_at->timestamp) / 60),
                    'employee_id' => $a->employee_id,
                    'date_raw' => $a->start_at->format('Y-m-d'),
                    'reschedule_url' => route('owner.appointments.reschedule', $a),
                    'confirm_url' => route('owner.appointments.confirm', $a),
                    'status_update_url' => route('owner.appointments.status.update', $a),
                    'delete_url' => route('owner.appointments.delete', $a),
                ];
            });

        return response()->json([
            'appointments' => $appointments,
            'revenue' => (float) $revenue,
            'revenue_formatted' => number_format($revenue, 2, ',', ' ')
        ]);
    }

    public function getAppointmentsForDayFull(Request $request): JsonResponse
    {
        $user = $request->user();
        $profileIds = Profile::where('owner_id', $user->id)->pluck('id');
        $date = $request->date;
        $employeeId = $request->employee_id;
        $dayStart = Carbon::parse($date)->startOfDay();
        $dayEnd = Carbon::parse($date)->endOfDay();

        $query = Appointment::whereIn('profile_id', $profileIds)
            ->whereBetween('start_at', [$dayStart, $dayEnd])
            ->where('status', '!=', 'cancelled');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $timezone = Profile::whereIn('id', $profileIds)->first()->timezone ?? config('app.timezone');

        $appointments = $query->get(['start_at', 'end_at'])->map(function($a) use ($timezone) {
            return [
                'start' => $a->start_at->timezone($timezone)->format('H:i'),
                'end' => $a->end_at->timezone($timezone)->format('H:i'),
            ];
        });

        return response()->json($appointments);
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
            'is_pakavoz_enabled' => ['nullable', 'boolean'],
            'pakavoz_api_key' => ['nullable', 'string', 'max:255'],
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
            'is_pakavoz_enabled' => $request->boolean('is_pakavoz_enabled'),
            'pakavoz_api_key' => $data['pakavoz_api_key'] ?? null,
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
            'is_pakavoz_enabled' => ['nullable', 'boolean'],
            'pakavoz_api_key' => ['nullable', 'string', 'max:255'],
        ]);

        $service->update([
            'name' => $data['name'],
            'category' => $data['category'],
            'base_price' => $data['base_price'],
            'base_duration_minutes' => $data['base_duration_minutes'],
            'is_pakavoz_enabled' => $request->boolean('is_pakavoz_enabled'),
            'pakavoz_api_key' => $data['pakavoz_api_key'] ?? null,
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

    public function updateAppointmentStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        if (!in_array($appointment->profile_id, $profileIds)) {
            abort(403);
        }

        $data = $request->validate([
            'status' => ['required', 'string', 'in:confirmed,completed,no-show,cancelled,pending'],
        ]);

        $appointment->update(['status' => $data['status']]);

        $message = match($data['status']) {
            'completed' => 'Rezervácia bola označená ako vybavená.',
            'no-show' => 'Zákazník bol označený ako neprišiel.',
            'cancelled' => 'Rezervácia bola zrušená.',
            default => 'Stav rezervácie bol aktualizovaný.',
        };

        return back()->with('status', $message);
    }

    public function storeManualAppointment(Request $request): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'service_name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        if (!in_array($data['profile_id'], $profileIds)) {
            abort(403);
        }

        $startAt = Carbon::createFromFormat('Y-m-d H:i', $data['date'] . ' ' . $data['start_time']);
        $endAt = $startAt->copy()->addMinutes((int) $data['duration_minutes']);

        // Pre manuálne rezervácie použijeme prvú nájdenú službu ako placeholder,
        // alebo ju v budúcne môžeme úplne oddeliť v databáze.
        $service = Service::where('profile_id', $data['profile_id'])->first();

        Appointment::create([
            'profile_id' => $data['profile_id'],
            'service_id' => $service?->id,
            'employee_id' => $data['employee_id'] ?? null,
            'customer_name' => $data['customer_name'],
            'customer_email' => null, // Email nie je vo formulári
            'customer_phone' => $data['customer_phone'] ?? null,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => 'confirmed',
            'price' => $data['price'],
            'currency' => 'EUR', // Predvolená mena
            'confirmation_code' => (string) Str::uuid(),
            'metadata' => [
                'notes' => $data['notes'] ?? null,
                'manual' => true,
                'service_name_manual' => $data['service_name'],
                'duration_minutes' => (int) $data['duration_minutes'],
            ],
        ]);

        return back()->with('status', 'Rezervácia bola úspešne pridaná.');
    }

    public function rescheduleAppointment(Request $request, Appointment $appointment): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        if (!in_array($appointment->profile_id, $profileIds)) {
            abort(403);
        }

        $data = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'employee_id' => ['nullable', 'exists:employees,id'],
        ]);

        $startAt = Carbon::createFromFormat('Y-m-d H:i', $data['date'] . ' ' . $data['start_time']);
        $endAt = $startAt->copy()->addMinutes((int) $data['duration_minutes']);

        $metadata = $appointment->metadata ?? [];
        $metadata['duration_minutes'] = (int) $data['duration_minutes'];

        $appointment->update([
            'start_at' => $startAt,
            'end_at' => $endAt,
            'employee_id' => $data['employee_id'] ?? $appointment->employee_id,
            'metadata' => $metadata,
        ]);

        return back()->with('status', 'Rezervácia bola úspešne presunutá.');
    }

    public function updateAppointment(Request $request, Appointment $appointment): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        if (!in_array($appointment->profile_id, $profileIds)) {
            abort(403);
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'service_name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $startAt = Carbon::createFromFormat('Y-m-d H:i', $data['date'] . ' ' . $data['start_time']);
        $endAt = $startAt->copy()->addMinutes((int) $data['duration_minutes']);

        $metadata = $appointment->metadata ?? [];
        $metadata['service_name_manual'] = $data['service_name'];
        $metadata['duration_minutes'] = (int) $data['duration_minutes'];
        $metadata['notes'] = $data['notes'] ?? null;

        $appointment->update([
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'price' => $data['price'],
            'employee_id' => $data['employee_id'] ?? null,
            'metadata' => $metadata,
        ]);

        return back()->with('status', 'Rezervácia bola úspešne upravená.');
    }

    public function schedules(Request $request): View
    {
        $profileIds = $this->getOwnerProfileIds($request);
        $profiles = Profile::with('employees')->whereIn('id', $profileIds)->get();
        $schedules = Schedule::with(['profile', 'employee', 'breaks'])
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
            'has_break' => ['nullable', 'string'],
            'break_start_time' => ['nullable', 'required_if:has_break,on', 'date_format:H:i'],
            'break_end_time' => ['nullable', 'required_if:has_break,on', 'date_format:H:i', 'after:break_start_time'],
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
            $schedule = Schedule::updateOrCreate([
                'profile_id' => $data['profile_id'],
                'employee_id' => isset($data['employee_id']) ? $data['employee_id'] : null,
                'day_of_week' => $dow,
            ], [
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'is_recurring' => true,
            ]);

            // Handle breaks
            $schedule->breaks()->delete();
            if ($request->boolean('has_break')) {
                $schedule->breaks()->create([
                    'start_time' => $data['break_start_time'],
                    'end_time' => $data['break_end_time'],
                    'label' => 'Prestávka',
                ]);
            }
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

    public function updateSchedule(Request $request, Schedule $schedule): RedirectResponse
    {
        $profileIds = $this->getOwnerProfileIds($request);
        if (!in_array($schedule->profile_id, $profileIds)) {
            abort(403);
        }

        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'has_break' => ['nullable', 'string'],
            'break_start_time' => ['nullable', 'required_if:has_break,on', 'date_format:H:i'],
            'break_end_time' => ['nullable', 'required_if:has_break,on', 'date_format:H:i', 'after:break_start_time'],
        ]);

        if (!in_array($data['profile_id'], $profileIds)) {
            abort(403);
        }

        $schedule->update([
            'profile_id' => $data['profile_id'],
            'employee_id' => $data['employee_id'] ?? null,
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
        ]);

        // Handle breaks
        $schedule->breaks()->delete();
        if ($request->boolean('has_break')) {
            $schedule->breaks()->create([
                'start_time' => $data['break_start_time'],
                'end_time' => $data['break_end_time'],
                'label' => 'Prestávka',
            ]);
        }

        return back()->with('status', 'Pracovný čas bol úspešne upravený.');
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
            'is_public' => ['nullable', 'boolean'],
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

        try {
            if ($request->hasFile('logo')) {
                if ($profile->logo_path) {
                    Storage::disk('public')->delete($profile->logo_path);
                }
                $updateData['logo_path'] = $request->file('logo')->store('profiles/logos', 'public');
            }

            if ($request->hasFile('banner')) {
                if ($profile->banner_path) {
                    Storage::disk('public')->delete($profile->banner_path);
                }
                $updateData['banner_path'] = $request->file('banner')->store('profiles/banners', 'public');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Chyba pri nahrávaní súborov: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Nepodarilo sa nahrať súbory: ' . $e->getMessage()]);
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
                'is_public' => $request->boolean('is_public'),
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

        // Získanie vybraného mesiaca a roka, inak aktuálny
        $selectedMonth = $request->integer('month', Carbon::now()->month);
        $selectedYear = $request->integer('year', Carbon::now()->year);
        $selectedDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);

        $startOfMonth = $selectedDate->copy()->startOfMonth();
        $endOfMonth = $selectedDate->copy()->endOfMonth();

        $monthsSlovak = [
            1 => 'Január', 2 => 'Február', 3 => 'Marec', 4 => 'Apríl',
            5 => 'Máj', 6 => 'Jún', 7 => 'Júl', 8 => 'August',
            9 => 'September', 10 => 'Október', 11 => 'November', 12 => 'December'
        ];

        // Vybraný mesiac - štatistiky (optimalizované query)
        $statsBaseQuery = Appointment::whereIn('profile_id', $profileIds)
            ->where('status', 'completed')
            ->whereBetween('start_at', [$startOfMonth, $endOfMonth]);

        $stats = [
            'count' => (clone $statsBaseQuery)->count(),
            'revenue' => (clone $statsBaseQuery)->sum('price'),
            'hours' => (clone $statsBaseQuery)->get(['start_at', 'end_at'])->reduce(function ($carry, $appointment) {
                if ($appointment->start_at && $appointment->end_at) {
                    return $carry + $appointment->start_at->diffInMinutes($appointment->end_at);
                }
                return $carry;
            }, 0) / 60,
        ];

        $now = Carbon::now();
        // Ročný graf - posledných 12 mesiacov
        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $revenue = Appointment::whereIn('profile_id', $profileIds)
                ->where('status', 'completed')
                ->whereBetween('start_at', [$start, $end])
                ->sum('price');

            $chartData[] = [
                'label' => Str::substr($monthsSlovak[$month->month], 0, 3),
                'full_label' => $monthsSlovak[$month->month] . ' ' . $month->year,
                'revenue' => (float)$revenue,
            ];
        }

        // Zoznam vybavených rezervácií pre vybraný mesiac
        $latestPayments = Appointment::with(['service', 'employee'])
            ->whereIn('profile_id', $profileIds)
            ->where('status', 'completed')
            ->whereBetween('start_at', [$startOfMonth, $endOfMonth])
            ->orderBy('start_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('owner.payments', compact('stats', 'chartData', 'latestPayments', 'selectedMonth', 'selectedYear', 'selectedDate', 'monthsSlovak'));
    }

    public function billingSettings(Request $request): View
    {
        $profileIds = Profile::where('owner_id', $request->user()->id)->pluck('id');
        $profiles = Profile::whereIn('id', $profileIds)->get();

        return view('owner.billing_settings', compact('profiles'));
    }

    public function storeBillingSettings(Request $request): RedirectResponse
    {
        $profileIds = Profile::where('owner_id', $request->user()->id)->pluck('id')->toArray();
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'billing_name' => ['required', 'string', 'max:255'],
            'billing_address' => ['required', 'string', 'max:255'],
            'billing_city' => ['required', 'string', 'max:255'],
            'billing_postal_code' => ['required', 'string', 'max:20'],
            'billing_country' => ['required', 'string', 'max:255'],
            'billing_ico' => ['nullable', 'string', 'max:20'],
            'billing_dic' => ['nullable', 'string', 'max:20'],
            'billing_ic_dph' => ['nullable', 'string', 'max:20'],
            'billing_iban' => ['nullable', 'string', 'max:50'],
            'billing_swift' => ['nullable', 'string', 'max:20'],
        ]);

        if (!in_array($data['profile_id'], $profileIds)) {
            abort(403);
        }

        $profile = Profile::findOrFail($data['profile_id']);
        $profile->update($data);

        return back()->with('status', 'Fakturačné údaje boli uložené.');
    }

    public function invoices(Request $request): View
    {
        $profileIds = $this->getOwnerProfileIds($request);

        $invoices = Invoice::with('profile')
            ->whereIn('profile_id', $profileIds)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('owner.invoices', compact('invoices'));
    }

    public function previewInvoice(Invoice $invoice): View
    {
        $profileIds = $this->getOwnerProfileIds(request());
        if (!in_array($invoice->profile_id, $profileIds)) {
            abort(403);
        }

        $invoice->load('profile');

        $ourBilling = [
            'name' => \App\Models\Setting::get('billing_name'),
            'address' => \App\Models\Setting::get('billing_address'),
            'city' => \App\Models\Setting::get('billing_city'),
            'postal_code' => \App\Models\Setting::get('billing_postal_code'),
            'country' => \App\Models\Setting::get('billing_country'),
            'ico' => \App\Models\Setting::get('billing_ico'),
            'dic' => \App\Models\Setting::get('billing_dic'),
            'ic_dph' => \App\Models\Setting::get('billing_ic_dph'),
            'iban' => \App\Models\Setting::get('billing_iban'),
            'swift' => \App\Models\Setting::get('billing_swift'),
        ];

        return view('admin.invoices.preview', compact('invoice', 'ourBilling'));
    }
}
