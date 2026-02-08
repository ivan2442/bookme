@extends('layouts.owner')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">{{ __('Profile Overview') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Welcome to your dashboard') }}</p>
        </div>
        <div class="flex items-center gap-2">
            @if($allProfiles->count() > 0)
                <button onclick="copyBusinessLink('{{ route('profiles.show', $allProfiles->first()->slug) }}')" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition shadow-sm flex items-center gap-2" title="{{ __('Copy link') }}">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('Copy link') }}</span>
                </button>
            @endif
            <button onclick="openManualAppointmentModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>{{ __('Add Appointment') }}</span>
            </button>
        </div>
    </div>

    @if($allProfiles->first() && $allProfiles->first()->subscription_starts_at)
        @php $profile = $allProfiles->first(); @endphp
        @if($profile->trial_days_left > 0)
            <div class="card bg-slate-900 border-0 text-white p-6 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl"></div>
                <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-1">
                        <p class="text-xs uppercase font-bold tracking-widest text-emerald-400">{{ __('Free system version') }}</p>
                        <h2 class="text-2xl font-display font-bold">{{ __('Using BookMe for free') }}</h2>
                        <p class="text-sm text-slate-300">{{ __('Trial ends on') }} <span class="font-bold text-white">{{ $profile->trial_ends_at->format('d.m.Y') }}</span>. {{ __('After that, the system will be charged 20 € per month.') }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="h-24 px-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex flex-col items-center justify-center shadow-inner">
                            <p class="text-[10px] uppercase font-bold text-emerald-400 mb-1 tracking-widest">{{ __('Remaining') }}</p>
                            <span class="text-2xl font-bold text-emerald-400 text-center leading-tight">{{ $profile->trial_time_left }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card bg-gradient-to-r from-rose-500 to-rose-600 border-0 text-white p-6 shadow-lg shadow-rose-200/50">
                <div class="flex items-center justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-xs uppercase font-bold tracking-widest text-rose-100">{{ __('Subscription ended') }}</p>
                        <h2 class="text-xl font-display font-semibold">{{ __('Your free version of the system has expired') }}</h2>
                        <p class="text-sm text-rose-50/80">{{ __('To continue using the system, you need to activate the paid version.') }}</p>
                    </div>
                    <button class="px-6 py-2 rounded-xl bg-white text-rose-600 font-bold hover:bg-rose-50 transition shadow-xl">{{ __('Activate for 20 €') }}</button>
                </div>
            </div>
        @endif
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 items-stretch mb-8">
        <div class="card flex flex-col justify-between p-6 h-full">
            <div class="flex items-center justify-between mb-4">
                <div class="h-10 w-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Today') }}</span>
            </div>
            <div>
                <p class="text-3xl font-bold text-slate-900" id="appointments-stat-count">{{ $stats['appointments_today'] }}</p>
                <p class="text-sm text-slate-500 font-medium" id="appointments-stat-label">{{ __('Appointments today') }}</p>
            </div>
        </div>

        <div class="card flex flex-col justify-between p-6 h-full">
            <div class="flex items-center justify-between mb-4">
                <div class="h-10 w-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Today') }}</span>
            </div>
            <div>
                <div class="flex items-center justify-between gap-2">
                    <p class="text-3xl font-bold text-slate-900" id="free-slots-stat-count">{{ $stats['free_slots_today'] }}</p>
                    <button onclick="openFreeSlotsModal()" class="px-2 py-1 rounded-lg bg-sky-100 text-sky-700 text-[10px] font-bold uppercase hover:bg-sky-200 transition">
                        {{ __('Show') }}
                    </button>
                </div>
                <p class="text-sm text-slate-500 font-medium" id="free-slots-stat-label">{{ __('Free slots') }}</p>
            </div>
        </div>

        <div class="card flex flex-col justify-between p-6 h-full">
            <div class="flex items-center justify-between mb-4">
                <div class="h-10 w-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Revenue on this day') }}</span>
            </div>
            <div>
                <p class="text-3xl font-bold text-slate-900">€<span id="revenue-today-value">{{ number_format($stats['revenue_today'], 2, ',', ' ') }}</span></p>
                <p class="text-sm text-slate-500 font-medium">{{ __('Completed orders') }}</p>
            </div>
        </div>

        <div class="card p-6 h-full xl:col-span-2 lg:col-span-2 md:col-span-2">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Calendar') }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" class="p-1 rounded-md hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-600" data-admin-cal-prev>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="text-[11px] font-bold text-slate-600 min-w-[80px] text-center" data-admin-cal-month>—</div>
                    <button type="button" class="p-1 rounded-md hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-600" data-admin-cal-next>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <div class="date-calendar !shadow-none !border-0 !p-0">
                <div class="calendar-grid !gap-1" data-admin-cal-grid>
                    <div class="calendar-heading">{{ __('mon') }}</div>
                    <div class="calendar-heading">{{ __('tue') }}</div>
                    <div class="calendar-heading">{{ __('wed') }}</div>
                    <div class="calendar-heading">{{ __('thu') }}</div>
                    <div class="calendar-heading">{{ __('fri') }}</div>
                    <div class="calendar-heading">{{ __('sat') }}</div>
                    <div class="calendar-heading">{{ __('sun') }}</div>
                </div>
            </div>
            <input type="hidden" id="admin-selected-date" value="{{ date('Y-m-d') }}">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 items-start">
        <div class="space-y-6">
            <div class="card overflow-hidden !p-0">
                <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                    <h2 class="font-bold text-slate-900" id="upcoming-title">{{ __('Appointments for today') }}</h2>
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-widest">{{ __('Current') }}</span>
                    </div>
                </div>
                <div class="divide-y divide-slate-100" id="appointments-list">
                    @forelse($upcoming as $appointment)
                        <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:bg-slate-50/50 transition-colors {{ $appointment->status === 'completed' ? 'opacity-50' : '' }}">
                            <div class="flex items-center gap-4">
                                <div class="text-center min-w-[50px] px-2 py-1 rounded-lg {{ $appointment->status === 'completed' ? 'bg-slate-100 text-slate-500' : 'bg-emerald-50 text-emerald-700' }}">
                                    <p class="text-lg font-bold leading-tight">{{ $appointment->start_at->format('H:i') }}</p>
                                </div>
                                <div>
                                    <p class="font-bold {{ $appointment->status === 'completed' ? 'text-slate-500' : 'text-slate-900' }} leading-tight">
                                        {{ $appointment->metadata['service_name_manual'] ?? ($appointment->service?->name ?? __('Manual service')) }}
                                    </p>
                                    <p class="text-xs text-slate-500">{{ $appointment->customer_name }} • {{ $appointment->customer_phone }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($appointment->status !== 'confirmed' || ($appointment->profile->calendarSetting->requires_confirmation ?? true))
                                    <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-tight
                                        {{ $appointment->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' :
                                           ($appointment->status === 'pending' ? 'bg-orange-100 text-orange-700' :
                                           ($appointment->status === 'completed' ? 'bg-slate-100 text-slate-500' : 'bg-slate-100 text-slate-500')) }}">
                                        @if($appointment->status === 'completed') {{ __('Completed') }} @elseif($appointment->status === 'confirmed') {{ __('Confirmed') }} @elseif($appointment->status === 'pending') {{ __('Pending') }} @else {{ $appointment->status }} @endif
                                    </span>
                                @endif
                                <div class="flex items-center gap-1">
                                    @if($appointment->status === 'pending')
                                        <form method="POST" action="{{ route('owner.appointments.confirm', $appointment) }}">
                                            @csrf
                                            <button class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition" title="{{ __('Confirm') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                    @endif

                                    @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                        <form method="POST" action="{{ route('owner.appointments.status.update', $appointment) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button class="px-3 py-1.5 rounded-lg bg-emerald-500 text-white text-xs font-bold hover:bg-emerald-600 transition shadow-sm" title="{{ __('Mark as completed') }}">
                                                {{ __('Completed') }}
                                            </button>
                                        </form>
                                    @endif

                                    <button onclick='openEditAppointmentModal({{ json_encode([
                                        "id" => $appointment->id,
                                        "customer_name" => $appointment->customer_name,
                                        "customer_email" => $appointment->customer_email,
                                        "customer_phone" => $appointment->customer_phone,
                                        "status" => $appointment->status,
                                        "notes" => $appointment->metadata["notes"] ?? "",
                                        "date" => $appointment->start_at->format("Y-m-d"),
                                        "start_time" => $appointment->start_at->format("H:i"),
                                        "duration_minutes" => $appointment->metadata["duration_minutes"] ?? (int) (($appointment->end_at->timestamp - $appointment->start_at->timestamp) / 60),
                                        "service_name" => $appointment->metadata["service_name_manual"] ?? ($appointment->service?->name ?? __('Manual service')),
                                        "employee_id" => $appointment->employee_id,
                                        "price" => $appointment->price
                                    ]) }})' class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-bold hover:bg-emerald-100 transition" title="{{ __('Edit') }}">
                                        {{ __('Edit') }}
                                    </button>

                                    <button onclick='openEditAppointmentModal({{ json_encode([
                                        "id" => $appointment->id,
                                        "customer_name" => $appointment->customer_name,
                                        "customer_email" => $appointment->customer_email,
                                        "customer_phone" => $appointment->customer_phone,
                                        "status" => $appointment->status,
                                        "notes" => $appointment->metadata["notes"] ?? "",
                                        "date" => $appointment->start_at->format("Y-m-d"),
                                        "start_time" => $appointment->start_at->format("H:i"),
                                        "duration_minutes" => $appointment->metadata["duration_minutes"] ?? (int) (($appointment->end_at->timestamp - $appointment->start_at->timestamp) / 60),
                                        "service_name" => $appointment->metadata["service_name_manual"] ?? ($appointment->service?->name ?? __('Manual service')),
                                        "employee_id" => $appointment->employee_id,
                                        "price" => $appointment->price
                                    ]) }})' class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition" title="{{ __('Reschedule') }}">
                                        {{ __('Reschedule') }}
                                    </button>

                                    <form method="POST" action="{{ route('owner.appointments.delete', $appointment) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100 transition" title="{{ __('Delete') }}">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <p class="text-sm text-slate-500 italic">{{ __('No appointments scheduled for today.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{--
        <div class="space-y-6">
            <div class="card p-6">
                <h2 class="font-bold text-slate-900 mb-4">Rýchle akcie</h2>
                <div class="grid grid-cols-1 gap-2">
                    <a href="{{ route('owner.services') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 transition-all group">
                        <div class="h-8 w-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-slate-400 group-hover:text-emerald-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-sm font-bold">Moje služby</span>
                    </a>
                    <a href="{{ route('owner.employees') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 transition-all group">
                        <div class="h-8 w-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-slate-400 group-hover:text-emerald-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <span class="text-sm font-bold">Zamestnanci</span>
                    </a>
                </div>
            </div>
        </div>
        --}}
    </div>
</div>


<!-- Edit Appointment Modal -->
<div id="editAppointmentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeEditAppointmentModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-display font-semibold text-slate-900">{{ __('Edit appointment') }}</h3>
                <button onclick="closeEditAppointmentModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="editAppointmentForm" method="POST" action="" class="space-y-4">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">{{ __('Customer name') }}</label>
                        <input type="text" name="customer_name" id="edit_customer_name" class="input-control" required>
                    </div>
                    <div>
                        <label class="label">{{ __('Phone') }}</label>
                        <input type="text" name="customer_phone" id="edit_customer_phone" class="input-control">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">{{ __('Service name') }}</label>
                        <input type="text" name="service_name_manual" id="edit_service_name" class="input-control" required>
                    </div>
                    <div>
                        <label class="label">{{ __('Date') }}</label>
                        <input type="date" name="date" id="edit_date" class="input-control" required>
                    </div>
                </div>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="label">{{ __('Start time') }}</label>
                        <input type="text" name="start_time" id="edit_start_time" class="input-control js-flatpickr-time" required>
                    </div>
                    <div>
                        <label class="label">{{ __('Duration (min)') }}</label>
                        <input type="number" name="duration_minutes" id="edit_duration" class="input-control" min="1" required>
                    </div>
                    <div>
                        <label class="label">{{ __('Price (€)') }}</label>
                        <input type="number" step="0.01" name="price" id="edit_price" class="input-control" required>
                    </div>
                </div>

                @if($allProfiles->first()->employees->count() > 1)
                <div>
                    <label class="label">{{ __('Employee') }}</label>
                    <div class="nice-select-wrapper">
                        <select name="employee_id" id="edit_employee_select" class="nice-select">
                            <option value="">{{ __('No employee') }}</option>
                            @foreach($allProfiles->first()->employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @elseif($allProfiles->first()->employees->count() === 1)
                    <input type="hidden" name="employee_id" id="edit_employee_select" value="{{ $allProfiles->first()->employees->first()->id }}">
                @endif

                <div>
                    <label class="label">{{ __('Note') }}</label>
                    <textarea name="notes" id="edit_notes" rows="2" class="input-control" placeholder="{{ __('Optional note') }}"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-50">
                    <button type="button" onclick="closeEditAppointmentModal()" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg">{{ __('Save changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Free Slots Modal -->
<div id="freeSlotsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeFreeSlotsModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-4xl p-0 overflow-hidden text-left transition-all sm:my-8">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-900">{{ __('Free slots') }}</h3>
                <button onclick="closeFreeSlotsModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6">
                <div id="free-slots-container" class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                    <!-- Slots will be loaded here via JS -->
                    <div class="flex items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sky-500"></div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button onclick="closeFreeSlotsModal()" class="px-4 py-2 text-sm font-bold text-slate-600 hover:text-slate-800 transition">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Booking Modal -->
<div id="quickBookingModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeQuickBookingModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">{{ __('Quick booking') }}</h3>
                    <p class="text-sm text-slate-500" id="quick-booking-time-display"></p>
                </div>
                <button onclick="closeQuickBookingModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('owner.appointments.manual.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="profile_id" id="quick_profile_id">
                <input type="hidden" name="date" id="quick_date">
                <input type="hidden" name="duration_minutes" id="quick_duration" value="30">
                <input type="hidden" name="price" id="quick_price" value="0">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">{{ __('Date') }}</label>
                        <input type="date" name="date_display" id="quick_date_display" class="input-control" onchange="document.getElementById('quick_date').value = this.value">
                    </div>
                    <div>
                        <label class="label">{{ __('Start time') }}</label>
                        <input type="text" name="start_time" id="quick_start_time" class="input-control js-flatpickr-time" style="padding: 15px !important;   height: auto !important;   max-height: 52px !important;   text-align: center;  " required>
                    </div>
                </div>

                <div id="quick-service-selection" class="space-y-3 mb-6">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Choose a service') }}</label>
                    <div class="grid grid-cols-1 gap-2" id="quick-services-list">
                        <!-- Services will be loaded here -->
                    </div>
                    <button type="button" onclick="selectManualService()" id="btn-manual-service" class="w-full text-left px-4 py-3 rounded-xl border-2 border-slate-100 hover:border-emerald-500 hover:bg-emerald-50 transition-all group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-700 group-hover:text-emerald-700">{{ __('Manual service') }}</p>
                                <p class="text-xs text-slate-500">{{ __('Enter name and price manually') }}</p>
                            </div>
                            <div class="h-5 w-5 rounded-full border-2 border-slate-200 group-hover:border-emerald-500 flex items-center justify-center">
                                <div class="h-2-5 w-2-5 rounded-full bg-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                        </div>
                    </button>
                </div>

                <div id="manual-service-fields" class="hidden space-y-4 border-t border-slate-100 pt-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">{{ __('Service name') }}</label>
                            <input type="text" name="service_name_manual" id="quick_service_name" class="input-control" placeholder="{{ __('e.g. Cutting') }}">
                        </div>
                        <div>
                            <label class="label">{{ __('Price (€)') }}</label>
                            <input type="number" step="0.01" name="price_manual" id="quick_price_manual" class="input-control" value="0.00">
                        </div>
                    </div>
                </div>

                <div class="space-y-4 border-t border-slate-100 pt-4">
                    <div>
                        <label class="label">{{ __('Customer name') }}</label>
                        <input type="text" name="customer_name" class="input-control" placeholder="{{ __('Name and surname') }}" required>
                    </div>
                    <div>
                        <label class="label">{{ __('Phone') }}</label>
                        <input type="text" name="customer_phone" class="input-control" placeholder="+421 ...">
                    </div>
                    <div>
                        <label class="label">{{ __('Note') }}</label>
                        <textarea name="notes" rows="2" class="input-control" placeholder="{{ __('Optional note') }}"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeQuickBookingModal()" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50">{{ __('Create booking') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.adminInitialClosedDays = @json($closedDays ?? []);

    function copyBusinessLink(url) {
        navigator.clipboard.writeText(url).then(() => {
            Swal.fire({
                title: window.translations['Success'] || 'Úspech',
                text: window.translations['Link copied to clipboard'] || 'Odkaz bol skopírovaný do schránky',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        });
    }

    function openManualAppointmentModal() {
        const profile = @json($allProfiles->first());
        const services = @json($allProfiles->first()->services);
        const selectedDate = document.getElementById('admin-selected-date')?.value || new Date().toISOString().split('T')[0];
        const now = new Date();
        const hour = now.getHours();
        const minute = now.getMinutes() < 30 ? '30' : '00';
        const defaultTime = `${String(now.getMinutes() < 30 ? hour : hour + 1).padStart(2, '0')}:${minute}`;

        const slot = {
            profile_id: profile.id,
            profile_name: profile.name,
            date: selectedDate,
            time: defaultTime,
            services: services.map(s => ({
                id: s.id,
                name: s.name,
                price: s.base_price
            }))
        };

        openQuickBookingModal(slot);
    }

    function closeManualAppointmentModal() {
        // Starý modal už nepoužívame
    }

    function openEditAppointmentModal(appointment) {
        const modal = document.getElementById('editAppointmentModal');
        const form = document.getElementById('editAppointmentForm');

        form.action = `/owner/appointments/${appointment.id}/update`;

        document.getElementById('edit_customer_name').value = appointment.customer_name || '';
        document.getElementById('edit_customer_phone').value = appointment.customer_phone || '';
        document.getElementById('edit_service_name').value = appointment.service_name || '';
        document.getElementById('edit_date').value = appointment.date || '';
        document.getElementById('edit_start_time').value = appointment.start_time || '';
        document.getElementById('edit_duration').value = appointment.duration_minutes || '';
        document.getElementById('edit_price').value = appointment.price || '';

        const employeeSelect = document.getElementById('edit_employee_select');
        if (employeeSelect) {
            employeeSelect.value = appointment.employee_id || '';
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        if (window.reinitFlatpickr) window.reinitFlatpickr();
    }

    function closeEditAppointmentModal() {
        document.getElementById('editAppointmentModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openFreeSlotsModal() {
        const date = document.getElementById('admin-selected-date').value;
        const container = document.getElementById('free-slots-container');
        document.getElementById('freeSlotsModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        // Reset container to loading state
        container.innerHTML = `
            <div class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sky-500"></div>
            </div>
        `;

        axios.get(`/owner/appointments/free-slots?date=${date}&days=7`)
            .then(response => {
                const slots = response.data.slots;
                const count = response.data.count; // Počet pre vybraný deň

                // Update count in dashboard too
                const dashboardCount = document.getElementById('free-slots-today-count');
                const today = new Date().toISOString().split('T')[0];
                if (date === today && dashboardCount) {
                    dashboardCount.innerText = count;
                }

                if (slots.length === 0) {
                    container.innerHTML = `
                        <div class="py-12 text-center">
                            <p class="text-slate-500 italic">{{ __('No free slots available for this day.') }}</p>
                        </div>
                    `;
                    return;
                }

                // Rozdelíme sloty na dnes (vybraný deň) a budúce
                const slotsToday = slots.filter(s => s.date === date);
                const slotsUpcoming = slots.filter(s => s.date !== date);

                let html = '';

                if (slotsToday.length > 0) {
                    html += `<div class="grid grid-cols-1 gap-3">`;
                    html += slotsToday.map(slot => renderSlotItem(slot)).join('');
                    html += `</div>`;
                } else {
                    html += `
                        <div class="py-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 mb-6">
                            <p class="text-slate-500 italic text-sm">{{ __('No free slots available for this day.') }}</p>
                        </div>
                    `;
                }

                if (slotsUpcoming.length > 0) {
                    html += `<div class="mt-8 space-y-6">`;
                    html += `<h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 pb-2">${window.translations['Upcoming available times'] || 'Najbližšie voľné termíny'}</h4>`;

                    // Zoskupíme podľa dátumu
                    const grouped = {};
                    slotsUpcoming.forEach(s => {
                        if (!grouped[s.date]) grouped[s.date] = [];
                        grouped[s.date].push(s);
                    });

                    Object.keys(grouped).forEach(d => {
                        const dateObj = new Date(d);
                        const dayNames = [
                            window.translations['Sunday'] || 'Nedeľa',
                            window.translations['Monday'] || 'Pondelok',
                            window.translations['Tuesday'] || 'Utorok',
                            window.translations['Wednesday'] || 'Streda',
                            window.translations['Thursday'] || 'Štvrtok',
                            window.translations['Friday'] || 'Piatok',
                            window.translations['Saturday'] || 'Sobota'
                        ];
                        const dayName = dayNames[dateObj.getDay()];
                        const dateFormatted = dateObj.toLocaleDateString('sk-SK');

                        html += `
                            <div class="space-y-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-slate-700">${dayName}</span>
                                    <span class="text-xs text-slate-400">${dateFormatted}</span>
                                </div>
                                <div class="grid grid-cols-1 gap-2">
                                    ${grouped[d].map(slot => renderSlotItem(slot)).join('')}
                                </div>
                            </div>
                        `;
                    });
                    html += `</div>`;
                }

                container.innerHTML = html;
            })
            .catch(error => {
                console.error(error);
                container.innerHTML = `
                    <div class="py-12 text-center text-rose-600">
                        {{ __('Failed to load free slots.') }}
                    </div>
                `;
            });
    }

    function renderSlotItem(slot) {
        return `
            <div class="flex items-center justify-between p-4 rounded-xl bg-white border border-slate-100 hover:border-sky-200 transition-all group shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-lg bg-sky-50 text-sky-700 flex flex-col items-center justify-center border border-sky-100">
                        <span class="text-sm font-bold leading-none">${slot.time}</span>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900 leading-tight">${slot.profile_name}</p>
                        <p class="text-[10px] uppercase font-bold text-slate-400 tracking-tight">${new Date(slot.date).toLocaleDateString('sk-SK')}</p>
                    </div>
                </div>
                <button onclick='openQuickBookingModal(${JSON.stringify(slot)})' class="px-4 py-2 rounded-lg bg-slate-900 text-xs font-bold text-white hover:bg-emerald-600 transition-all shadow-md">
                    {{ __('Quick booking') }}
                </button>
            </div>
        `;
    }

    function closeFreeSlotsModal() {
        document.getElementById('freeSlotsModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    let currentSlot = null;

    function openQuickBookingModal(slot) {
        currentSlot = slot;
        const modal = document.getElementById('quickBookingModal');
        const timeDisplay = document.getElementById('quick-booking-time-display');
        const servicesList = document.getElementById('quick-services-list');

        document.getElementById('quick_profile_id').value = slot.profile_id;
        document.getElementById('quick_date').value = slot.date;
        document.getElementById('quick_date_display').value = slot.date;
        document.getElementById('quick_start_time').value = slot.time;

        const dateFormatted = new Date(slot.date).toLocaleDateString('sk-SK');
        timeDisplay.innerText = `${dateFormatted} o ${slot.time} - ${slot.profile_name}`;

        // Load services
        if (slot.services && slot.services.length > 0) {
            servicesList.innerHTML = slot.services.map(service => {
                const serviceJson = JSON.stringify(service).replace(/'/g, "&apos;");
                return `
                    <button type="button" onclick='selectQuickService(${serviceJson}, this)' class="quick-service-btn w-full text-left px-4 py-3 rounded-xl border-2 border-slate-100 hover:border-emerald-500 hover:bg-emerald-50 transition-all group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-700 group-hover:text-emerald-700">${service.name}</p>
                                <p class="text-xs text-slate-500">${service.price} €</p>
                            </div>
                            <div class="h-5 w-5 rounded-full border-2 border-slate-200 group-hover:border-emerald-500 flex items-center justify-center">
                                <div class="h-2-5 w-2-5 rounded-full bg-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                        </div>
                    </button>
                `;
            }).join('');
        } else {
            servicesList.innerHTML = `<p class="text-sm text-slate-500 italic py-2">{{ __('No services found for this business.') }}</p>`;
        }

        // Reset manual fields
        document.getElementById('manual-service-fields').classList.add('hidden');
        document.getElementById('quick_service_name').required = false;
        document.getElementById('btn-manual-service').classList.remove('border-emerald-500', 'bg-emerald-50');

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        if (window.reinitFlatpickr) window.reinitFlatpickr();
    }

    function selectQuickService(service, element) {
        document.getElementById('quick_service_name').value = service.name;
        document.getElementById('quick_price').value = service.price;
        document.getElementById('quick_price_manual').value = service.price;

        // Visual selection
        document.querySelectorAll('.quick-service-btn').forEach(btn => {
            btn.classList.remove('border-emerald-500', 'bg-emerald-50');
            const indicator = btn.querySelector('.h-2-5');
            if (indicator) indicator.classList.add('opacity-0');
        });
        element.classList.add('border-emerald-500', 'bg-emerald-50');
        const activeIndicator = element.querySelector('.h-2-5');
        if (activeIndicator) activeIndicator.classList.remove('opacity-0');

        document.getElementById('manual-service-fields').classList.add('hidden');
        document.getElementById('quick_service_name').required = false;
        document.getElementById('btn-manual-service').classList.remove('border-emerald-500', 'bg-emerald-50');
    }

    function selectManualService() {
        document.getElementById('quick_service_name').value = '';
        document.getElementById('quick_price').value = 0;
        document.getElementById('quick_price_manual').value = '0.00';

        // Visual selection
        document.querySelectorAll('.quick-service-btn').forEach(btn => {
            btn.classList.remove('border-emerald-500', 'bg-emerald-50');
            const indicator = btn.querySelector('.h-2-5');
            if (indicator) indicator.classList.add('opacity-0');
        });

        document.getElementById('btn-manual-service').classList.add('border-emerald-500', 'bg-emerald-50');
        document.getElementById('manual-service-fields').classList.remove('hidden');
        document.getElementById('quick_service_name').required = true;
        document.getElementById('quick_service_name').focus();
    }

    function closeQuickBookingModal() {
        document.getElementById('quickBookingModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    $(document).ready(function() {
        if ($.fn.niceSelect) {
            $('.nice-select').niceSelect();
        }
    });
</script>
@endsection
