@extends('layouts.app')

@section('content')
<div class="overflow-x-hidden">
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3 mb-6">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Pracovná doba</h1>
        </div>
        <div class="flex gap-2">
            <button onclick="openAddScheduleModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Pridať pracovný čas</span>
            </button>
        </div>
    </div>

    @include('owner.partials.nav')

    @if(session('status'))
        <div class="card border-emerald-200 bg-emerald-50 text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    <div class="max-w-4xl mx-auto">
        <div class="card space-y-3">
            <h2 class="font-semibold text-lg text-slate-900">Aktuálny rozvrh</h2>
            <div class="space-y-3 max-h-[75vh] overflow-y-auto pr-1">
                @php
                    $daysNames = [1=>'Pondelok',2=>'Utorok',3=>'Streda',4=>'Štvrtok',5=>'Piatok',6=>'Sobota',0=>'Nedeľa'];
                @endphp
                @forelse($schedules as $schedule)
                    <div class="border border-slate-100 rounded-xl p-4 bg-white/80 flex items-center justify-between gap-3 shadow-sm">
                        <div>
                            <p class="font-bold text-slate-900">{{ isset($daysNames[$schedule->day_of_week]) ? $daysNames[$schedule->day_of_week] : 'Neznámy deň' }}</p>
                            <p class="text-sm text-slate-600">
                                {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                                <span class="text-xs text-slate-400 font-normal ml-1">({{ $schedule->profile->name }})</span>
                            </p>
                            @if($schedule->employee)
                                <p class="text-[10px] uppercase font-bold text-emerald-600 mt-1">Zamestnanec: {{ $schedule->employee->name }}</p>
                            @else
                                <p class="text-[10px] uppercase font-bold text-slate-400 mt-1">Celá prevádzka</p>
                            @endif
                            @if($schedule->breaks->isNotEmpty())
                                <p class="text-[10px] font-bold text-orange-500 mt-1">
                                    Prestávka: {{ substr($schedule->breaks->first()->start_time, 0, 5) }} - {{ substr($schedule->breaks->first()->end_time, 0, 5) }}
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button"
                                onclick="openEditModal({{ $schedule->id }}, {{ $schedule->profile_id }}, {{ $schedule->employee_id ?? 'null' }}, {{ $schedule->day_of_week }}, '{{ substr($schedule->start_time, 0, 5) }}', '{{ substr($schedule->end_time, 0, 5) }}', {{ $schedule->breaks->isNotEmpty() ? 'true' : 'false' }}, '{{ $schedule->breaks->isNotEmpty() ? substr($schedule->breaks->first()->start_time, 0, 5) : '' }}', '{{ $schedule->breaks->isNotEmpty() ? substr($schedule->breaks->first()->end_time, 0, 5) : '' }}')"
                                class="p-2 text-slate-300 hover:text-blue-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('owner.schedules.delete', $schedule) }}" onsubmit="return confirmDelete(event, 'Naozaj odstrániť tento čas?')">
                                @csrf @method('DELETE')
                                <button class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 italic text-center py-4">Zatiaľ nie sú nastavené žiadne pracovné časy.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

<!-- Add Schedule Modal -->
<div id="addScheduleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeAddScheduleModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-display font-semibold text-slate-900">Pridať pracovný čas</h3>
                <button onclick="closeAddScheduleModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('owner.schedules.store') }}" class="space-y-4">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Prevádzka</label>
                        <div class="nice-select-wrapper">
                            <select name="profile_id" class="nice-select" required>
                                @foreach($profiles as $profile)
                                    <option value="{{ $profile->id }}" @selected(old('profile_id') == $profile->id)>{{ $profile->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Zamestnanec (voliteľné)</label>
                        <div class="nice-select-wrapper">
                            <select name="employee_id" class="nice-select">
                                <option value="">Bez väzby na zamestnanca (všeobecný čas)</option>
                                @foreach($profiles as $profile)
                                    @foreach($profile->employees as $employee)
                                        <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->name }} — {{ $profile->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="label">Dni v týždni</label>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <button type="button" onclick="selectAllDays()" class="px-3 py-1 text-[10px] font-bold uppercase bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition">Všetky</button>
                        <button type="button" onclick="selectWorkDays()" class="px-3 py-1 text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition">Po - Pia</button>
                        <button type="button" onclick="clearDays()" class="px-3 py-1 text-[10px] font-bold uppercase bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">Zrušiť</button>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach([1=>'Pondelok',2=>'Utorok',3=>'Streda',4=>'Štvrtok',5=>'Piatok',6=>'Sobota',0=>'Nedeľa'] as $val => $label)
                            <label class="flex items-center gap-2 p-2 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                                <input type="checkbox" name="days[]" value="{{ $val }}" class="h-4 w-4 day-checkbox text-emerald-600 focus:ring-emerald-500 rounded border-slate-300">
                                <span class="text-xs font-medium text-slate-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Otvárame o</label>
                        <div class="nice-select-wrapper">
                            <select name="start_time" class="nice-select nice-select-time" required>
                                @for($h = 0; $h <= 23; $h++)
                                    @foreach(['00', '30'] as $m)
                                        @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                        <option value="{{ $time }}" {{ $time == '09:00' ? 'selected' : '' }}>{{ $time }}</option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Zatvárame o</label>
                        <div class="nice-select-wrapper">
                            <select name="end_time" class="nice-select nice-select-time" required>
                                @for($h = 0; $h <= 23; $h++)
                                    @foreach(['00', '30'] as $m)
                                        @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                        <option value="{{ $time }}" {{ $time == '17:00' ? 'selected' : '' }}>{{ $time }}</option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-50 pt-4">
                    <div class="flex items-center gap-2 mb-3">
                        <input type="checkbox" id="has_break" name="has_break" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" onchange="toggleBreakFields(this, 'break_fields_new')">
                        <label for="has_break" class="text-xs font-bold uppercase text-slate-700 cursor-pointer">Pridať prestávku</label>
                    </div>
                    <div id="break_fields_new" class="grid grid-cols-2 gap-4 hidden">
                        <div>
                            <label class="label">Prestávka od</label>
                            <div class="nice-select-wrapper">
                                <select name="break_start_time" class="nice-select nice-select-time">
                                    @for($h = 0; $h <= 23; $h++)
                                        @foreach(['00', '30'] as $m)
                                            @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                            <option value="{{ $time }}" {{ $time == '12:00' ? 'selected' : '' }}>{{ $time }}</option>
                                        @endforeach
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="label">Prestávka do</label>
                            <div class="nice-select-wrapper">
                                <select name="break_end_time" class="nice-select nice-select-time">
                                    @for($h = 0; $h <= 23; $h++)
                                        @foreach(['00', '30'] as $m)
                                            @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                            <option value="{{ $time }}" {{ $time == '13:00' ? 'selected' : '' }}>{{ $time }}</option>
                                        @endforeach
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-50">
                    <button type="button" onclick="closeAddScheduleModal()" class="px-6 py-2 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">Zrušiť</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-200/50">Uložiť rozvrh</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div id="editScheduleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-display font-semibold text-slate-900">Upraviť pracovný čas</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="editScheduleForm" method="POST" action="" class="space-y-4">
                @csrf
                <input type="hidden" id="edit_schedule_id">

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Prevádzka</label>
                        <div class="nice-select-wrapper">
                            <select name="profile_id" id="edit_profile_id" class="nice-select" required>
                                @foreach($profiles as $profile)
                                    <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="label">Zamestnanec (voliteľné)</label>
                        <div class="nice-select-wrapper">
                            <select name="employee_id" id="edit_employee_id" class="nice-select">
                                <option value="">Bez väzby na zamestnanca (všeobecný čas)</option>
                                @foreach($profiles as $profile)
                                    @foreach($profile->employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }} — {{ $profile->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="label">Deň v týždni</label>
                    <div class="nice-select-wrapper">
                        <select name="day_of_week" id="edit_day_of_week" class="nice-select" required>
                            @foreach([1=>'Pondelok',2=>'Utorok',3=>'Streda',4=>'Štvrtok',5=>'Piatok',6=>'Sobota',0=>'Nedeľa'] as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Otvárame o</label>
                        <div class="nice-select-wrapper">
                            <select name="start_time" id="edit_start_time" class="nice-select nice-select-time" required>
                                @for($h = 0; $h <= 23; $h++)
                                    @foreach(['00', '30'] as $m)
                                        @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                        <option value="{{ $time }}">{{ $time }}</option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Zatvárame o</label>
                        <div class="nice-select-wrapper">
                            <select name="end_time" id="edit_end_time" class="nice-select nice-select-time" required>
                                @for($h = 0; $h <= 23; $h++)
                                    @foreach(['00', '30'] as $m)
                                        @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                        <option value="{{ $time }}">{{ $time }}</option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-50 pt-4">
                    <div class="flex items-center gap-2 mb-2">
                        <input type="checkbox" id="edit_has_break" name="has_break" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" onchange="toggleBreakFields(this, 'break_fields_edit')">
                        <label for="edit_has_break" class="text-xs font-bold uppercase text-slate-700 cursor-pointer">Prestávka</label>
                    </div>
                    <div id="break_fields_edit" class="grid grid-cols-2 gap-3 hidden">
                        <div>
                            <label class="label text-[10px]">Prestávka od</label>
                            <div class="nice-select-wrapper">
                                <select name="break_start_time" id="edit_break_start_time" class="nice-select nice-select-time">
                                    @for($h = 0; $h <= 23; $h++)
                                        @foreach(['00', '30'] as $m)
                                            @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="label text-[10px]">Prestávka do</label>
                            <div class="nice-select-wrapper">
                                <select name="break_end_time" id="edit_break_end_time" class="nice-select nice-select-time">
                                    @for($h = 0; $h <= 23; $h++)
                                        @foreach(['00', '30'] as $m)
                                            @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-50">
                    <button type="button" onclick="closeEditModal()" class="px-6 py-2 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">
                        Zrušiť
                    </button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-200/50">
                        Uložiť zmeny
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.nice-select').niceSelect();
    });

    function openAddScheduleModal() {
        document.getElementById('addScheduleModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        $('#addScheduleModal .nice-select').niceSelect('update');
    }

    function closeAddScheduleModal() {
        document.getElementById('addScheduleModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function selectAllDays() {
        document.querySelectorAll('.day-checkbox').forEach(cb => cb.checked = true);
    }

    function selectWorkDays() {
        clearDays();
        // 1-5 are Monday-Friday
        document.querySelectorAll('.day-checkbox').forEach(cb => {
            if (['1', '2', '3', '4', '5'].includes(cb.value)) {
                cb.checked = true;
            }
        });
    }

    function clearDays() {
        document.querySelectorAll('.day-checkbox').forEach(cb => cb.checked = false);
    }

    function toggleBreakFields(checkbox, targetId) {
        const target = document.getElementById(targetId);
        if (checkbox.checked) {
            target.classList.remove('hidden');
        } else {
            target.classList.add('hidden');
        }
    }

    function openEditModal(id, profileId, employeeId, dayOfWeek, startTime, endTime, hasBreak, breakStart, breakEnd) {
        const modal = document.getElementById('editScheduleModal');
        const form = document.getElementById('editScheduleForm');

        form.action = `/owner/schedules/${id}`;
        document.getElementById('edit_schedule_id').value = id;
        document.getElementById('edit_profile_id').value = profileId;
        document.getElementById('edit_employee_id').value = employeeId || '';
        document.getElementById('edit_day_of_week').value = dayOfWeek;
        document.getElementById('edit_start_time').value = startTime;
        document.getElementById('edit_end_time').value = endTime;

        const breakCheckbox = document.getElementById('edit_has_break');
        const breakFields = document.getElementById('break_fields_edit');
        breakCheckbox.checked = hasBreak;

        if (hasBreak) {
            breakFields.classList.remove('hidden');
            document.getElementById('edit_break_start_time').value = breakStart;
            document.getElementById('edit_break_end_time').value = breakEnd;
        } else {
            breakFields.classList.add('hidden');
            document.getElementById('edit_break_start_time').value = '12:00';
            document.getElementById('edit_break_end_time').value = '13:00';
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        $('#editScheduleModal .nice-select').niceSelect('update');
    }

    function closeEditModal() {
        const modal = document.getElementById('editScheduleModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    </script>
</div>
@endsection
