<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\ServiceVariant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_profiles' => Profile::count(),
            'active_profiles' => Profile::where('status', 'published')->count(),
            'total_appointments' => Appointment::count(),
            'total_revenue' => Appointment::where('status', 'confirmed')->sum('price'),
        ];

        $latest_profiles = Profile::with('owner')->latest()->limit(5)->get();
        $upcoming = Appointment::with(['profile', 'service', 'employee'])->where('start_at', '>=', now())->orderBy('start_at')->limit(5)->get();

        // Prevádzky s končiacim predplatným (v najbližších 14 dňoch alebo po expirácii)
        $expiring_trials = Profile::whereNotNull('subscription_starts_at')
            ->where('subscription_plan', 'free')
            ->get()
            ->filter(function($p) {
                return $p->trial_days_left <= 14;
            })
            ->sortBy('trial_days_left');

        return view('admin.dashboard', compact('stats', 'latest_profiles', 'upcoming', 'expiring_trials'));
    }

    public function services(): View
    {
        $profiles = Profile::with('employees')->orderBy('name')->get();
        $services = Service::with(['variants.employees', 'profile.employees'])->orderBy('created_at', 'desc')->get();

        return view('admin.services', compact('profiles', 'services'));
    }

    public function storeService(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'variant_name' => ['nullable', 'string', 'max:255'],
            'variant_duration_minutes' => ['nullable', 'integer', 'min:5', 'max:480'],
            'variant_price' => ['nullable', 'numeric', 'min:0'],
            'employee_ids' => ['array'],
            'employee_ids.*' => ['exists:employees,id'],
            'is_pakavoz_enabled' => ['nullable', 'boolean'],
            'pakavoz_api_key' => ['nullable', 'string', 'max:255'],
        ]);

        $service = Service::create([
            'profile_id' => $data['profile_id'],
            'name' => $data['name'],
            'category' => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'base_duration_minutes' => $data['base_duration_minutes'],
            'base_price' => $data['base_price'],
            'currency' => strtoupper($data['currency']),
            'is_active' => true,
            'is_pakavoz_enabled' => $request->boolean('is_pakavoz_enabled'),
            'pakavoz_api_key' => $data['pakavoz_api_key'] ?? null,
        ]);

        if (! empty($data['variant_name'])) {
            $variant = ServiceVariant::create([
                'service_id' => $service->id,
                'name' => $data['variant_name'],
                'duration_minutes' => $data['variant_duration_minutes'] ?? $data['base_duration_minutes'],
                'price' => $data['variant_price'] ?? $data['base_price'],
                'currency' => strtoupper($data['currency']),
                'buffer_before_minutes' => 0,
                'buffer_after_minutes' => 0,
                'is_active' => true,
            ]);

            if (! empty($data['employee_ids'])) {
                $variant->employees()->sync($data['employee_ids']);
            }
        } elseif (! empty($data['employee_ids'])) {
            // If no variant was created, ensure any existing variants inherit employees
            $this->syncEmployeesToServiceVariants($service, $data['employee_ids']);
        }

        return back()->with('status', 'Služba bola vytvorená.');
    }

    public function storeVariant(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'buffer_before_minutes' => ['nullable', 'integer', 'min:0', 'max:120'],
            'buffer_after_minutes' => ['nullable', 'integer', 'min:0', 'max:120'],
        ]);

        $variant = ServiceVariant::create([
            'service_id' => $service->id,
            'name' => $data['name'],
            'duration_minutes' => $data['duration_minutes'],
            'price' => $data['price'],
            'currency' => strtoupper($data['currency']),
            'buffer_before_minutes' => $data['buffer_before_minutes'] ?? 0,
            'buffer_after_minutes' => $data['buffer_after_minutes'] ?? 0,
            'is_active' => true,
        ]);

        // Inherit employees from existing variants of the same service (service-level assignment)
        $inheritedEmployees = $service->variants()->with('employees:id')->get()
            ->flatMap(fn ($v) => $v->employees)
            ->pluck('id')
            ->unique()
            ->values()
            ->all();

        if (! empty($inheritedEmployees)) {
            $variant->employees()->sync($inheritedEmployees);
        }

        return back()->with('status', 'Variant bol pridaný.');
    }

    public function updateService(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'is_active' => ['nullable', 'boolean'],
            'employee_ids' => ['array'],
            'employee_ids.*' => ['exists:employees,id'],
            'is_pakavoz_enabled' => ['nullable', 'boolean'],
            'pakavoz_api_key' => ['nullable', 'string', 'max:255'],
        ]);

        $service->update([
            'name' => $data['name'],
            'category' => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'base_duration_minutes' => $data['base_duration_minutes'],
            'base_price' => $data['base_price'],
            'currency' => strtoupper($data['currency']),
            'is_active' => $request->boolean('is_active'),
            'is_pakavoz_enabled' => $request->boolean('is_pakavoz_enabled'),
            'pakavoz_api_key' => $data['pakavoz_api_key'] ?? null,
        ]);

        if (isset($data['employee_ids'])) {
            $this->syncEmployeesToServiceVariants($service, $data['employee_ids']);
        }

        return back()->with('status', 'Služba bola upravená.');
    }

    public function updateVariant(Request $request, Service $service, ServiceVariant $variant): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'buffer_before_minutes' => ['nullable', 'integer', 'min:0', 'max:120'],
            'buffer_after_minutes' => ['nullable', 'integer', 'min:0', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $variant->update([
            'name' => $data['name'],
            'duration_minutes' => $data['duration_minutes'],
            'price' => $data['price'],
            'currency' => strtoupper($data['currency']),
            'buffer_before_minutes' => $data['buffer_before_minutes'] ?? 0,
            'buffer_after_minutes' => $data['buffer_after_minutes'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('status', 'Variant bol upravený.');
    }

    public function deleteVariant(Service $service, ServiceVariant $variant): RedirectResponse
    {
        $variant->delete();

        return back()->with('status', 'Variant bol odstránený.');
    }

    protected function syncEmployeesToServiceVariants(Service $service, array $employeeIds): void
    {
        $service->load('variants');
        foreach ($service->variants as $variant) {
            $variant->employees()->sync($employeeIds);
        }
    }

    public function updateEmployee(Request $request, Employee $employee): RedirectResponse
    {
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:16'],
            'bio' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $employee->update([
            ...$data,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('status', 'Zamestnanec bol upravený.');
    }

    public function employees(): View
    {
        $profiles = Profile::orderBy('name')->get();
        $employees = Employee::with('profile')->orderBy('created_at', 'desc')->get();

        return view('admin.employees', compact('profiles', 'employees'));
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:16'],
            'bio' => ['nullable', 'string'],
        ]);

        Employee::create([
            ...$data,
            'is_active' => true,
        ]);

        return back()->with('status', 'Zamestnanec bol pridaný.');
    }

    public function appointments(): View
    {
        $appointments = Appointment::with(['profile', 'service', 'employee'])
            ->orderBy('start_at', 'desc')
            ->paginate(50);

        return view('admin.appointments', compact('appointments'));
    }

    public function confirmAppointment(Appointment $appointment): RedirectResponse
    {
        $appointment->update(['status' => 'confirmed']);

        return back()->with('status', 'Rezervácia potvrdená.');
    }

    public function deleteAppointment(Appointment $appointment): RedirectResponse
    {
        $appointment->delete();

        return back()->with('status', 'Rezervácia bola odstránená.');
    }

    public function profiles(): View
    {
        $profiles = Profile::with('owner')->orderBy('created_at', 'desc')->get();
        $owners = \App\Models\User::where('role', 'owner')->orderBy('name')->get();

        return view('admin.profiles', compact('profiles', 'owners'));
    }

    public function storeProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:profiles,slug'],
            'category' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:255'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'banner' => ['nullable', 'image', 'max:5120'],
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $owner = \App\Models\User::where('email', $data['email'])->first();

            if ($owner) {
                if ($owner->role !== 'owner') {
                    \Illuminate\Support\Facades\DB::rollBack();
                    return back()->withInput()->with('error', 'Tento e-mail už používa používateľ, ktorý nie je majiteľom.');
                }
            } else {
                $owner = \App\Models\User::create([
                    'name' => $data['name'] . ' Owner',
                    'email' => $data['email'],
                    'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                    'role' => 'owner',
                    'is_active' => true,
                ]);
            }

            $slug = $data['slug'] ?? Str::slug($data['name']);
            if (Profile::where('slug', $slug)->exists()) {
                \Illuminate\Support\Facades\DB::rollBack();
                return back()->withInput()->with('error', 'Slug už existuje, zadaj iný.');
            }

            $profile = Profile::create([
                'owner_id' => $owner->id,
                'name' => $data['name'],
                'slug' => $slug,
                'category' => $data['category'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'city' => $data['city'] ?? null,
                'address_line1' => $data['address_line1'] ?? null,
                'description' => $data['description'] ?? null,
                'timezone' => $data['timezone'] ?? 'Europe/Bratislava',
                'status' => 'published',
                'subscription_starts_at' => now(),
                'subscription_plan' => 'free',
                'logo_path' => $request->hasFile('logo') ? $request->file('logo')->store('profiles/logos', 'public') : null,
                'banner_path' => $request->hasFile('banner') ? $request->file('banner')->store('profiles/banners', 'public') : null,
            ]);

            // Vytvorenie základných nastavení kalendára
            \App\Models\CalendarSetting::create([
                'profile_id' => $profile->id,
                'slot_interval_minutes' => 15,
                'min_notice_minutes' => 60,
                'max_advance_days' => 90,
                'timezone' => $data['timezone'] ?? 'Europe/Bratislava',
            ]);

            // Seed základného pracovného času Po-Pia 09:00-17:00
            foreach ([1, 2, 3, 4, 5] as $dow) {
                Schedule::create([
                    'profile_id' => $profile->id,
                    'day_of_week' => $dow,
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'is_recurring' => true,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('status', 'Prevádzka bola vytvorená.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Chyba pri vytváraní prevádzky: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Vyskytla sa chyba pri vytváraní prevádzky: ' . $e->getMessage());
        }
    }

    public function updateProfile(Request $request, Profile $profile): RedirectResponse
    {
        $data = $request->validate([
            'owner_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:profiles,slug,'.$profile->id],
            'category' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:255'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'status' => ['required', 'string', 'in:draft,published,inactive,pending'],
            'subscription_starts_at' => ['nullable', 'date'],
            'subscription_plan' => ['required', 'string', 'in:free,basic,premium'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'banner' => ['nullable', 'image', 'max:5120'],
        ]);

        $updateData = [
            'owner_id' => $data['owner_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'category' => $data['category'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'address_line1' => $data['address_line1'] ?? null,
            'description' => $data['description'] ?? null,
            'timezone' => $data['timezone'] ?? 'Europe/Bratislava',
            'status' => $data['status'],
            'subscription_starts_at' => $data['subscription_starts_at'] ?? null,
            'subscription_plan' => $data['subscription_plan'] ?? 'free',
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
            \Illuminate\Support\Facades\Log::error('Chyba pri nahrávaní súborov v adminovi: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Nepodarilo sa nahrať súbory: ' . $e->getMessage()]);
        }

        $profile->update($updateData);

        return back()->with('status', 'Prevádzka bola upravená.');
    }

    public function publishProfile(Profile $profile): RedirectResponse
    {
        $profile->update(['status' => 'published']);

        return back()->with('status', 'Prevádzka bola úspešne zverejnená.');
    }

    public function schedules(): View
    {
        $profiles = Profile::with('employees')->orderBy('name')->get();
        $schedules = Schedule::with(['profile', 'employee'])->orderBy('day_of_week')->orderBy('start_time')->get();

        return view('admin.schedules', compact('profiles', 'schedules'));
    }

    public function storeSchedule(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        Schedule::create([
            'profile_id' => $data['profile_id'],
            'employee_id' => $data['employee_id'] ?? null,
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'is_recurring' => true,
        ]);

        return back()->with('status', 'Pracovný čas bol pridaný.');
    }

    public function deleteSchedule(Schedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return back()->with('status', 'Pracovný čas bol odstránený.');
    }

    public function calendarSettings(): View
    {
        $profiles = Profile::with('calendarSetting')->orderBy('name')->get();

        return view('admin.calendar_settings', compact('profiles'));
    }

    public function storeCalendarSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'slot_interval_minutes' => ['required', 'integer', 'min:5', 'max:120'],
            'buffer_before_minutes' => ['required', 'integer', 'min:0', 'max:120'],
            'buffer_after_minutes' => ['required', 'integer', 'min:0', 'max:120'],
            'max_advance_days' => ['required', 'integer', 'min:1', 'max:365'],
            'min_notice_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
            'cancellation_limit_hours' => ['required', 'integer', 'min:0', 'max:720'],
        ]);

        \App\Models\CalendarSetting::updateOrCreate(
            ['profile_id' => $data['profile_id']],
            $data
        );

        return back()->with('status', 'Kalendár bol uložený.');
    }

    public function holidays(): View
    {
        $profiles = Profile::with('employees')->orderBy('name')->get();
        $holidays = Holiday::with(['profile', 'employee'])->orderBy('date', 'desc')->paginate(30);

        return view('admin.holidays', compact('profiles', 'holidays'));
    }

    public function storeHoliday(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'is_closed' => ['nullable', 'boolean'],
            'reason' => ['nullable', 'string', 'max:255'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
        ]);

        Holiday::create([
            ...$data,
            'is_closed' => $request->boolean('is_closed', true),
        ]);

        return back()->with('status', 'Sviatok/uzávierka bola pridaná.');
    }

    public function updateHoliday(Request $request, Holiday $holiday): RedirectResponse
    {
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'is_closed' => ['nullable', 'boolean'],
            'reason' => ['nullable', 'string', 'max:255'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
        ]);

        $holiday->update([
            ...$data,
            'is_closed' => $request->boolean('is_closed', false),
        ]);

        return back()->with('status', 'Sviatok/uzávierka bola upravená.');
    }

    public function deleteHoliday(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return back()->with('status', 'Sviatok/uzávierka bola odstránená.');
    }

    public function payments(): View
    {
        $payments = \App\Models\Payment::with('appointment.profile')->orderBy('created_at', 'desc')->paginate(30);

        return view('admin.payments', compact('payments'));
    }

    public function invoices(): View
    {
        $invoices = \App\Models\Invoice::with('profile')->orderBy('created_at', 'desc')->paginate(30);
        $profiles = Profile::orderBy('name')->get();

        return view('admin.invoices', compact('invoices', 'profiles'));
    }

    public function storeInvoice(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_at' => ['required', 'date'],
            'invoice_number' => ['nullable', 'string', 'unique:invoices,invoice_number'],
        ]);

        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = 'INV-' . strtoupper(Str::random(8));
        }

        \App\Models\Invoice::create($data);

        return back()->with('status', 'Faktúra bola vytvorená.');
    }

    public function updateInvoiceStatus(Request $request, \App\Models\Invoice $invoice): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:unpaid,paid,cancelled'],
        ]);

        $updateData = ['status' => $data['status']];
        if ($data['status'] === 'paid') {
            $updateData['paid_at'] = now();
        }

        $invoice->update($updateData);

        return back()->with('status', 'Stav faktúry bol aktualizovaný.');
    }

    public function deleteInvoice(\App\Models\Invoice $invoice): RedirectResponse
    {
        $invoice->delete();
        return back()->with('status', 'Faktúra bola odstránená.');
    }

    public function previewInvoice(\App\Models\Invoice $invoice): View
    {
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

    public function sendInvoice(\App\Models\Invoice $invoice): RedirectResponse
    {
        $invoice->load('profile.owner');
        $email = $invoice->profile->email ?: $invoice->profile->owner->email;

        if (!$email) {
            return back()->with('error', 'Prevádzka nemá nastavený e-mail.');
        }

        try {
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\InvoiceMail($invoice));
            return back()->with('status', 'Faktúra bola odoslaná na ' . $email);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Chyba pri odosielaní faktúry: ' . $e->getMessage());
            return back()->with('error', 'Nepodarilo sa odoslať faktúru: ' . $e->getMessage());
        }
    }

    public function billingSettings(): View
    {
        $billingData = [
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

        return view('admin.billing_settings', compact('billingData'));
    }

    public function storeBillingSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:255'],
            'ico' => ['nullable', 'string', 'max:20'],
            'dic' => ['nullable', 'string', 'max:20'],
            'ic_dph' => ['nullable', 'string', 'max:20'],
            'iban' => ['nullable', 'string', 'max:50'],
            'swift' => ['nullable', 'string', 'max:20'],
        ]);

        foreach ($data as $key => $value) {
            \App\Models\Setting::set('billing_' . $key, $value);
        }

        return back()->with('status', 'Fakturačné údaje boli uložené.');
    }
}
