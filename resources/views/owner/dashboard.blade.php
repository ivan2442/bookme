@extends('layouts.owner')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">Prehľad prevádzky</h1>
            <p class="text-sm text-slate-500">Vitajte v dashboarde vášho profilu.</p>
        </div>
        <button onclick="openManualAppointmentModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Pridať rezerváciu</span>
        </button>
    </div>

    @if($allProfiles->first() && $allProfiles->first()->subscription_starts_at)
        @php $profile = $allProfiles->first(); @endphp
        @if($profile->trial_days_left > 0)
            <div class="card bg-slate-900 border-0 text-white p-6 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl"></div>
                <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-1">
                        <p class="text-xs uppercase font-bold tracking-widest text-emerald-400">Bezplatná verzia systému</p>
                        <h2 class="text-2xl font-display font-bold">Využívate BookMe zadarmo</h2>
                        <p class="text-sm text-slate-300">Skúšobná doba vám končí <span class="font-bold text-white">{{ $profile->trial_ends_at->format('d.m.Y') }}</span>. Potom bude systém spoplatnený sumou 20 € mesačne.</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="h-24 px-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex flex-col items-center justify-center shadow-inner">
                            <p class="text-[10px] uppercase font-bold text-emerald-400 mb-1 tracking-widest">Zostáva ešte</p>
                            <span class="text-2xl font-bold text-emerald-400 text-center leading-tight">{{ $profile->trial_time_left }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card bg-gradient-to-r from-rose-500 to-rose-600 border-0 text-white p-6 shadow-lg shadow-rose-200/50">
                <div class="flex items-center justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-xs uppercase font-bold tracking-widest text-rose-100">Predplatné skončilo</p>
                        <h2 class="text-xl font-display font-semibold">Vaša bezplatná verzia systému vypršala</h2>
                        <p class="text-sm text-rose-50/80">Pre pokračovanie v používaní systému je potrebné aktivovať platenú verziu.</p>
                    </div>
                    <button class="px-6 py-2 rounded-xl bg-white text-rose-600 font-bold hover:bg-rose-50 transition shadow-xl">Aktivovať za 20 €</button>
                </div>
            </div>
        @endif
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-stretch">
        <div class="card flex flex-col justify-between p-6 h-full">
            <div class="flex items-center justify-between mb-4">
                <div class="h-10 w-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Dnes</span>
            </div>
            <div>
                <p class="text-3xl font-bold text-slate-900">{{ $stats['appointments_today'] }}</p>
                <p class="text-sm text-slate-500 font-medium">Rezervácií na dnes</p>
            </div>
        </div>

        <div class="card p-6 h-full">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kalendár</span>
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
                    <div class="calendar-heading">po</div>
                    <div class="calendar-heading">ut</div>
                    <div class="calendar-heading">st</div>
                    <div class="calendar-heading">št</div>
                    <div class="calendar-heading">pi</div>
                    <div class="calendar-heading">so</div>
                    <div class="calendar-heading">ne</div>
                </div>
            </div>
            <input type="hidden" id="admin-selected-date" value="{{ date('Y-m-d') }}">
        </div>

        <div class="card flex flex-col justify-between p-6 h-full">
            <div class="flex items-center justify-between mb-4">
                <div class="h-10 w-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tržby v tento deň</span>
            </div>
            <div>
                <p class="text-3xl font-bold text-slate-900">€<span id="revenue-today-value">{{ number_format($stats['revenue_today'], 2, ',', ' ') }}</span></p>
                <p class="text-sm text-slate-500 font-medium">Vybavené objednávky</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 items-start">
        <div class="space-y-6">
            <div class="card overflow-hidden !p-0">
                <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                    <h2 class="font-bold text-slate-900" id="upcoming-title">Termíny na dnes</h2>
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-widest">Aktuálne</span>
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
                                        {{ $appointment->metadata['service_name_manual'] ?? ($appointment->service?->name ?? 'Manuálna služba') }}
                                    </p>
                                    <p class="text-xs text-slate-500">{{ $appointment->customer_name }} • {{ $appointment->customer_phone }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-tight
                                    {{ $appointment->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' :
                                       ($appointment->status === 'pending' ? 'bg-orange-100 text-orange-700' :
                                       ($appointment->status === 'completed' ? 'bg-slate-100 text-slate-500' : 'bg-slate-100 text-slate-500')) }}">
                                    @if($appointment->status === 'completed') Vybavené @elseif($appointment->status === 'confirmed') Potvrdené @elseif($appointment->status === 'pending') Čaká @else {{ $appointment->status }} @endif
                                </span>
                                <div class="flex items-center gap-1">
                                    @if($appointment->status === 'pending')
                                        <form method="POST" action="{{ route('owner.appointments.confirm', $appointment) }}">
                                            @csrf
                                            <button class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition" title="Potvrdiť">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                    @endif

                                    @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                        <form method="POST" action="{{ route('owner.appointments.status.update', $appointment) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button class="px-3 py-1.5 rounded-lg bg-emerald-500 text-white text-xs font-bold hover:bg-emerald-600 transition shadow-sm" title="Označiť ako vybavené">
                                                Vybavené
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
                                        "service_name" => $appointment->metadata["service_name_manual"] ?? ($appointment->service?->name ?? "Manuálna služba"),
                                        "employee_id" => $appointment->employee_id,
                                        "price" => $appointment->price
                                    ]) }})' class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition" title="Presunúť">
                                        Presunúť
                                    </button>

                                    <form method="POST" action="{{ route('owner.appointments.delete', $appointment) }}" onsubmit="return confirm('Naozaj chcete vymazať túto rezerváciu?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100 transition" title="Vymazať">
                                            Vymazať
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <p class="text-sm text-slate-500 italic">Na dnes nie sú naplánované žiadne rezervácie.</p>
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

<!-- Manual Appointment Modal -->
<div id="manualAppointmentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeManualAppointmentModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-display font-semibold text-slate-900">Pridať manuálnu rezerváciu</h3>
                <button onclick="closeManualAppointmentModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('owner.appointments.manual.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="profile_id" id="manual_profile_id" value="{{ $allProfiles->first()->id }}">

                @if($allProfiles->first()->employees->count() > 1)
                    <div>
                        <label class="label">Zamestnanec</label>
                        <div class="nice-select-wrapper">
                            <select name="employee_id" class="nice-select" id="manual_employee_select">
                                <option value="">Bez zamestnanca</option>
                                @foreach($allProfiles->first()->employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @elseif($allProfiles->first()->employees->count() === 1)
                    <input type="hidden" name="employee_id" value="{{ $allProfiles->first()->employees->first()->id }}">
                @endif

                <div class="grid sm:grid-cols-2 gap-4 border-t border-slate-50 pt-4">
                    <div>
                        <label class="label">Meno zákazníka</label>
                        <input type="text" name="customer_name" class="input-control" placeholder="Meno a priezvisko" required>
                    </div>
                    <div>
                        <label class="label">Telefón</label>
                        <input type="text" name="customer_phone" class="input-control" placeholder="+421 ...">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Názov služby</label>
                        <input type="text" name="service_name_manual" class="input-control" placeholder="Napr. Pánsky strih" required>
                    </div>
                    <div>
                        <label class="label">Dátum</label>
                        <input type="date" name="date" id="manual_date" class="input-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="grid sm:grid-cols-3 gap-4 border-t border-slate-50 pt-4">
                    <div>
                        <label class="label">Čas začiatku</label>
                        <div class="nice-select-wrapper">
                            <select name="start_time" id="manual_start_time" class="nice-select nice-select-time" required>
                                @for($h = 7; $h <= 21; $h++)
                                    @foreach(['00', '30'] as $m)
                                        @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                        <option value="{{ $time }}" {{ $time == '09:00' ? 'selected' : '' }}>{{ $time }}</option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Dĺžka (min)</label>
                        <input type="number" name="duration_minutes" class="input-control" value="30" min="1" required>
                    </div>
                    <div>
                        <label class="label">Cena (€)</label>
                        <input type="number" step="0.01" name="price" class="input-control" value="0.00" required>
                    </div>
                </div>

                <div>
                    <label class="label">Poznámka</label>
                    <textarea name="notes" rows="2" class="input-control" placeholder="Voliteľná poznámka"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeManualAppointmentModal()" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">Zrušiť</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50">Uložiť rezerváciu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Appointment Modal -->
<div id="editAppointmentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeEditAppointmentModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-display font-semibold text-slate-900">Upraviť rezerváciu</h3>
                <button onclick="closeEditAppointmentModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="editAppointmentForm" method="POST" action="" class="space-y-4">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Meno zákazníka</label>
                        <input type="text" name="customer_name" id="edit_customer_name" class="input-control" required>
                    </div>
                    <div>
                        <label class="label">Telefón</label>
                        <input type="text" name="customer_phone" id="edit_customer_phone" class="input-control">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Názov služby</label>
                        <input type="text" name="service_name_manual" id="edit_service_name" class="input-control" required>
                    </div>
                    <div>
                        <label class="label">Dátum</label>
                        <input type="date" name="date" id="edit_date" class="input-control" required>
                    </div>
                </div>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="label">Čas začiatku</label>
                        <div class="nice-select-wrapper">
                            <select name="start_time" id="edit_start_time" class="nice-select nice-select-time" required>
                                @for($h = 7; $h <= 21; $h++)
                                    @foreach(['00', '30'] as $m)
                                        @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                        <option value="{{ $time }}">{{ $time }}</option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Dĺžka (min)</label>
                        <input type="number" name="duration_minutes" id="edit_duration" class="input-control" min="1" required>
                    </div>
                    <div>
                        <label class="label">Cena (€)</label>
                        <input type="number" step="0.01" name="price" id="edit_price" class="input-control" required>
                    </div>
                </div>

                @if($allProfiles->first()->employees->count() > 1)
                <div>
                    <label class="label">Zamestnanec</label>
                    <div class="nice-select-wrapper">
                        <select name="employee_id" id="edit_employee_select" class="nice-select">
                            <option value="">Bez zamestnanca</option>
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
                    <label class="label">Poznámka</label>
                    <textarea name="notes" id="edit_notes" rows="2" class="input-control" placeholder="Voliteľná poznámka"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-50">
                    <button type="button" onclick="closeEditAppointmentModal()" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">Zrušiť</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg">Uložiť zmeny</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openManualAppointmentModal() {
        document.getElementById('manualAppointmentModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        if ($.fn.niceSelect) {
            $('#manualAppointmentModal .nice-select').niceSelect('update');
        }
        checkBusySlots();
    }

    function closeManualAppointmentModal() {
        document.getElementById('manualAppointmentModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
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
        document.getElementById('edit_notes').value = appointment.notes || '';

        const employeeSelect = document.getElementById('edit_employee_select');
        if (employeeSelect) {
            employeeSelect.value = appointment.employee_id || '';
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        if ($.fn.niceSelect) {
            $('#editAppointmentModal .nice-select').niceSelect('update');
        }
    }

    function closeEditAppointmentModal() {
        document.getElementById('editAppointmentModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function checkBusySlots() {
        const date = document.getElementById('manual_date').value;
        const employeeId = document.getElementById('manual_employee_select')?.value || '';
        const profileId = document.getElementById('manual_profile_id').value;
        const timeSelect = document.getElementById('manual_start_time');

        if (!date) return;

        axios.get('/owner/appointments/day-full', { params: { date, employee_id: employeeId, profile_id: profileId } })
            .then(response => {
                const appointments = response.data;

                Array.from(timeSelect.options).forEach(option => {
                    const time = option.value;
                    const isBusy = appointments.some(app => {
                        const appStart = new Date(app.start_at).toTimeString().slice(0, 5);
                        const appEnd = new Date(app.end_at).toTimeString().slice(0, 5);
                        return time >= appStart && time < appEnd;
                    });

                    if (isBusy) {
                        if (!option.text.includes('(obsadené)')) {
                            option.text = `${time} (obsadené)`;
                        }
                        option.classList.add('busy-option');
                    } else {
                        option.text = time;
                        option.classList.remove('busy-option');
                    }
                });
                if ($.fn.niceSelect) {
                    $(timeSelect).niceSelect('update');
                }
            })
            .catch(err => console.error('Chyba pri kontrole obsadenosti', err));
    }

    $(document).ready(function() {
        if ($.fn.niceSelect) {
            $('.nice-select').niceSelect();
        }

        $('#manual_date, #manual_employee_select').on('change', function() {
            checkBusySlots();
        });
    });
</script>
@endsection
