@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3 mb-6">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Dashboard prevádzky</h1>
        </div>
        <div class="flex gap-2">
            <button onclick="openManualAppointmentModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Pridať rezerváciu</span>
            </button>
        </div>
    </div>

    @include('owner.partials.nav')

    <div class="grid md:grid-cols-3 gap-4">
        <div class="card">
            <p class="text-sm text-slate-500 mb-1">Rezervácie dnes</p>
            <p class="text-3xl font-semibold text-slate-900">{{ $stats['appointments_today'] }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-slate-500 mb-1">Rezervácie tento mesiac</p>
            <p class="text-3xl font-semibold text-slate-900">{{ $stats['appointments_month'] }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr,350px] gap-6 items-start">
        <div class="space-y-4 lg:order-2">
            <div class="card p-4">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Kalendár
                </h3>
                <div class="date-calendar !shadow-none !border-slate-100" data-admin-calendar>
                    <div class="flex items-center justify-between mb-2">
                        <button type="button" class="cal-nav cal-prev" data-admin-cal-prev>‹</button>
                        <div class="text-center cal-month text-sm" data-admin-cal-month>—</div>
                        <button type="button" class="cal-nav cal-next" data-admin-cal-next>›</button>
                    </div>
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
        </div>

        <div class="card lg:order-1">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold text-lg text-slate-900" id="upcoming-title">Termíny na dnes</h2>
            </div>
            <div class="divide-y divide-slate-100" id="appointments-list">
                @forelse($upcoming->filter(fn($a) => $a->status !== 'completed' && $a->start_at->isToday())->sortBy('start_at') as $appointment)
                    <div class="py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-slate-900 leading-tight">
                                    {{ $appointment->metadata['service_name_manual'] ?? ($appointment->service?->name ?? 'Manuálna služba') }}
                                </p>
                                <span class="flex-shrink-0 text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold uppercase tracking-tight">
                                    {{ optional($appointment->employee)->name ?? 'Bez zamestnanca' }}
                                </span>
                            </div>
                            <div class="flex flex-col text-sm text-slate-600">
                                <span class="font-medium text-slate-900">{{ $appointment->customer_name }}</span>
                                @if($appointment->customer_phone)
                                    <span class="text-xs text-slate-500">{{ $appointment->customer_phone }}</span>
                                @endif
                            </div>
                        </div>
                            <div class="md:text-right space-y-2 flex-1">
                                @php
                                    $daysNames = [1=>'Pondelok',2=>'Utorok',3=>'Streda',4=>'Štvrtok',5=>'Piatok',6=>'Sobota',0=>'Nedeľa'];
                                    $dayName = $daysNames[$appointment->start_at->dayOfWeek] ?? '';
                                @endphp
                                <div class="flex flex-col md:items-end">
                                    <p class="font-bold text-slate-900 leading-none">
                                        {{ $appointment->start_at?->format('d.m.Y') }} {{ $dayName }}
                                    </p>
                                    <div class="flex items-center gap-3 mt-1 md:justify-end">
                                        <p class="text-sm font-semibold text-emerald-600">
                                            {{ $appointment->start_at?->format('H:i') }} - {{ $appointment->end_at?->format('H:i') }}
                                        </p>
                                        <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                                        <p class="text-sm font-bold text-slate-900">€{{ number_format($appointment->price, 2) }}</p>
                                    </div>
                                    <p class="text-[10px] uppercase font-bold text-slate-400 mt-1">{{ $appointment->status }}</p>
                                </div>

                                <div class="flex flex-wrap items-center gap-2 md:justify-end">
                                @if($appointment->status !== 'completed' && $appointment->status !== 'no-show')
                                    <form method="POST" action="{{ route('owner.appointments.status.update', $appointment) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="p-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all shadow-sm flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tight" title="Vybavené">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span>Vybavené</span>
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('owner.appointments.status.update', $appointment) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="no-show">
                                        <button type="submit" class="p-2 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-500 hover:text-white transition-all shadow-sm flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tight" title="Neprišiel">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <span>Neprišiel</span>
                                        </button>
                                    </form>

                                    <button type="button"
                                        onclick="openRescheduleModal({{ $appointment->id }}, '{{ $appointment->start_at->format('Y-m-d') }}', '{{ $appointment->start_at->format('H:i') }}', {{ $appointment->metadata['duration_minutes'] ?? (int) (($appointment->end_at->timestamp - $appointment->start_at->timestamp) / 60) }}, {{ $appointment->employee_id ?? 'null' }})"
                                        class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white transition-all shadow-sm flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tight" title="Presunúť">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        <span>Presunúť</span>
                                    </button>

                                    <button type="button"
                                        onclick="openEditAppointmentModal({{ json_encode([
                                            'id' => $appointment->id,
                                            'customer_name' => $appointment->customer_name,
                                            'customer_phone' => $appointment->customer_phone,
                                            'service_name' => $appointment->metadata['service_name_manual'] ?? ($appointment->service?->name ?? 'Manuálna služba'),
                                            'date' => $appointment->start_at->format('Y-m-d'),
                                            'start_time' => $appointment->start_at->format('H:i'),
                                            'duration_minutes' => $appointment->metadata['duration_minutes'] ?? (int) (($appointment->end_at->timestamp - $appointment->start_at->timestamp) / 60),
                                            'price' => $appointment->price,
                                            'employee_id' => $appointment->employee_id,
                                            'notes' => $appointment->metadata['notes'] ?? ''
                                        ]) }})"
                                        class="p-2 rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-500 hover:text-white transition-all shadow-sm flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tight" title="Upraviť">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Upraviť</span>
                                    </button>
                                @endif

                                <form method="POST" action="{{ route('owner.appointments.delete', $appointment) }}" onsubmit="return confirmDelete(event, 'Naozaj vymazať túto rezerváciu?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-all shadow-sm flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tight" title="Vymazať">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Vymazať</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center" id="no-appointments">
                        <p class="text-sm text-slate-500 italic">Žiadne nadchádzajúce rezervácie na tento deň.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</section>

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
                {{-- Skrytý výber prevádzky - defaultne prvá, ak ich má majiteľ viac, v dashboarde pracujeme s konkrétnou (predpokladáme kontext jednej prevádzky pre zjednodušenie podľa zadania) --}}
                <input type="hidden" name="profile_id" value="{{ $allProfiles->first()->id }}">

                @if($allProfiles->first()->employees->count() > 1)
                    <div>
                        <label class="label">Zamestnanec</label>
                        <select name="employee_id" class="input-control nice-select" id="manual_employee_select">
                            <option value="">Bez zamestnanca</option>
                            @foreach($allProfiles->first()->employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
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
                        <input type="text" name="service_name" class="input-control" placeholder="Napr. Pánsky strih" required>
                    </div>
                    <div>
                        <label class="label">Dátum</label>
                        <input type="date" name="date" class="input-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="grid sm:grid-cols-3 gap-4 border-t border-slate-50 pt-4">
                    <div>
                        <label class="label">Čas začiatku</label>
                        <select name="start_time" class="input-control nice-select nice-select-time" required>
                            @for($h = 7; $h <= 21; $h++)
                                @foreach(['00', '30'] as $m)
                                    @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                    <option value="{{ $time }}" {{ $time == '09:00' ? 'selected' : '' }}>{{ $time }}</option>
                                @endforeach
                            @endfor
                        </select>
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
                    <button type="button" onclick="closeManualAppointmentModal()" class="px-6 py-2 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">Zrušiť</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50">Uložiť rezerváciu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openManualAppointmentModal() {
        document.getElementById('manualAppointmentModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        $('#manualAppointmentModal .nice-select').niceSelect('update');
    }

    function closeManualAppointmentModal() {
        document.getElementById('manualAppointmentModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openRescheduleModal(id, date, startTime, duration, employeeId) {
        const modal = document.getElementById('rescheduleModal');
        const form = document.getElementById('rescheduleForm');

        // Update form action with the ID
        form.action = `/owner/appointments/${id}/reschedule`;

        // Set values
        document.getElementById('reschedule_date').value = date;
        document.getElementById('reschedule_start_time').value = startTime;
        document.getElementById('reschedule_duration').value = duration;

        const employeeSelect = document.getElementById('reschedule_employee_select');
        if (employeeSelect) {
            employeeSelect.value = employeeId || '';
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        $('#rescheduleModal .nice-select').niceSelect('update');
    }

    function closeRescheduleModal() {
        document.getElementById('rescheduleModal').classList.add('hidden');
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
        $('#editAppointmentModal .nice-select').niceSelect('update');
    }

    function closeEditAppointmentModal() {
        document.getElementById('editAppointmentModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    $(document).ready(function() {
        $('.nice-select').niceSelect();
    });
</script>

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
                        <input type="text" name="service_name" id="edit_service_name" class="input-control" required>
                    </div>
                    <div>
                        <label class="label">Dátum</label>
                        <input type="date" name="date" id="edit_date" class="input-control" required>
                    </div>
                </div>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="label">Čas začiatku</label>
                        <select name="start_time" id="edit_start_time" class="input-control nice-select nice-select-time" required>
                            @for($h = 7; $h <= 21; $h++)
                                @foreach(['00', '30'] as $m)
                                    @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                    <option value="{{ $time }}">{{ $time }}</option>
                                @endforeach
                            @endfor
                        </select>
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
                    <select name="employee_id" id="edit_employee_select" class="input-control nice-select">
                        <option value="">Bez zamestnanca</option>
                        @foreach($allProfiles->first()->employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                @elseif($allProfiles->first()->employees->count() === 1)
                    <input type="hidden" name="employee_id" id="edit_employee_select" value="{{ $allProfiles->first()->employees->first()->id }}">
                @endif

                <div>
                    <label class="label">Poznámka</label>
                    <textarea name="notes" id="edit_notes" rows="2" class="input-control" placeholder="Voliteľná poznámka"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-50">
                    <button type="button" onclick="closeEditAppointmentModal()" class="px-6 py-2 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">Zrušiť</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50">Uložiť zmeny</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reschedule Appointment Modal -->
<div id="rescheduleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeRescheduleModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-display font-semibold text-slate-900">Presunúť rezerváciu</h3>
                <button onclick="closeRescheduleModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="rescheduleForm" method="POST" action="" class="space-y-4">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Nový dátum</label>
                        <input type="date" name="date" id="reschedule_date" class="input-control" required>
                    </div>
                    <div>
                        <label class="label">Nový čas začiatku</label>
                        <select name="start_time" id="reschedule_start_time" class="input-control nice-select nice-select-time" required>
                            @for($h = 7; $h <= 21; $h++)
                                @foreach(['00', '30'] as $m)
                                    @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                    <option value="{{ $time }}">{{ $time }}</option>
                                @endforeach
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Dĺžka (min)</label>
                        <input type="number" name="duration_minutes" id="reschedule_duration" class="input-control" min="1" required>
                    </div>
                    @if($allProfiles->first()->employees->count() > 1)
                    <div>
                        <label class="label">Zamestnanec</label>
                        <select name="employee_id" id="reschedule_employee_select" class="input-control nice-select">
                            <option value="">Bez zamestnanca</option>
                            @foreach($allProfiles->first()->employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-50">
                    <button type="button" onclick="closeRescheduleModal()" class="px-6 py-2 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">Zrušiť</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-blue-500 text-white font-semibold hover:bg-blue-600 transition shadow-md shadow-blue-200/50">Uložiť zmeny</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
