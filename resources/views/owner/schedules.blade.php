@extends('layouts.owner')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">{{ __('Working hours') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Definition of opening hours and availability of your team.') }}</p>
        </div>
        <button onclick="openAddScheduleModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>{{ __('Add time') }}</span>
        </button>
    </div>

    <div class="grid lg:grid-cols-[1fr,350px] gap-6 items-start">
        <div class="card space-y-3">
            <h2 class="font-semibold text-lg text-slate-900">{{ __('Current schedule') }}</h2>
            <div class="space-y-3 max-h-[75vh] overflow-y-auto pr-1">
                @php
                    $daysNames = [1=>__('Monday'),2=>__('Tuesday'),3=>__('Wednesday'),4=>__('Thursday'),5=>__('Friday'),6=>__('Saturday'),0=>__('Sunday')];
                @endphp
                @forelse($schedules as $schedule)
                    <div class="border border-slate-100 rounded-xl p-4 bg-white/80 flex items-center justify-between gap-3 shadow-sm hover:border-emerald-100 transition-colors">
                        <div>
                            <p class="font-bold text-slate-900">{{ isset($daysNames[$schedule->day_of_week]) ? $daysNames[$schedule->day_of_week] : __('Unknown day') }}</p>
                            <p class="text-sm text-slate-600">
                                {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                                <span class="text-xs text-slate-400 font-normal ml-1">({{ $schedule->profile->name }})</span>
                            </p>
                            @if($schedule->employee)
                                <p class="text-[10px] uppercase font-bold text-emerald-600 mt-1">{{ __('Employee') }}: {{ $schedule->employee->name }}</p>
                            @else
                                <p class="text-[10px] uppercase font-bold text-slate-400 mt-1">{{ __('Whole business') }}</p>
                            @endif
                            @if($schedule->breaks->isNotEmpty())
                                <p class="text-[10px] font-bold text-orange-500 mt-1">
                                    {{ __('Break') }}: {{ substr($schedule->breaks->first()->start_time, 0, 5) }} - {{ substr($schedule->breaks->first()->end_time, 0, 5) }}
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button"
                                onclick="openEditModal({{ $schedule->id }}, {{ $schedule->profile_id }}, {{ $schedule->employee_id ?? 'null' }}, {{ $schedule->day_of_week }}, '{{ substr($schedule->start_time, 0, 5) }}', '{{ substr($schedule->end_time, 0, 5) }}', {{ $schedule->breaks->isNotEmpty() ? 'true' : 'false' }}, '{{ $schedule->breaks->isNotEmpty() ? substr($schedule->breaks->first()->start_time, 0, 5) : '' }}', '{{ $schedule->breaks->isNotEmpty() ? substr($schedule->breaks->first()->end_time, 0, 5) : '' }}')"
                                class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('owner.schedules.delete', $schedule) }}" onsubmit="return confirmDelete(event, '{{ __('Are you sure you want to delete this working hour?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg bg-slate-100 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 italic p-8 text-center">{{ __('No working hours set yet.') }}</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('Information') }}
                </h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    {{ __('Set working hours for the entire business or individually for each employee. The system will automatically generate free slots based on these settings.') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Add Schedule Modal -->
<div id="addScheduleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeAddScheduleModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-display font-semibold text-slate-900">{{ __('Add working hours') }}</h3>
                <button onclick="closeAddScheduleModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('owner.schedules.store') }}" class="space-y-4">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">{{ __('Business profile') }}</label>
                        <div class="nice-select-wrapper">
                            <select name="profile_id" class="nice-select" required>
                                @foreach($profiles as $profile)
                                    <option value="{{ $profile->id }}" @selected(old('profile_id') == $profile->id)>{{ $profile->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">{{ __('Employee (optional)') }}</label>
                        <div class="nice-select-wrapper">
                            <select name="employee_id" class="nice-select">
                                <option value="">{{ __('No specific employee (general time)') }}</option>
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
                    <label class="label">{{ __('Days of week') }}</label>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <button type="button" onclick="selectAllDays()" class="px-3 py-1 text-[10px] font-bold uppercase bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition">{{ __('All') }}</button>
                        <button type="button" onclick="selectWorkDays()" class="px-3 py-1 text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition">{{ __('Mon - Fri') }}</button>
                        <button type="button" onclick="clearDays()" class="px-3 py-1 text-[10px] font-bold uppercase bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">{{ __('Cancel') }}</button>
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

                <div class="grid grid-cols-2 gap-4 border-t border-slate-50 pt-4">
                    <div>
                        <label class="label">{{ __('Opens at') }}</label>
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
                        <label class="label">{{ __('Closes at') }}</label>
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
                        <label for="has_break" class="text-xs font-bold uppercase text-slate-700 cursor-pointer">{{ __('Add break') }}</label>
                    </div>
                    <div id="break_fields_new" class="grid grid-cols-2 gap-4 hidden">
                        <div>
                            <label class="label">{{ __('Break from') }}</label>
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
                            <label class="label">{{ __('Break to') }}</label>
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
                    <button type="button" onclick="closeAddScheduleModal()" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-200/50">{{ __('Save working hours') }}</button>
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
                <h3 class="text-xl font-display font-semibold text-slate-900">{{ __('Edit working hours') }}</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="editScheduleForm" method="POST" action="" class="space-y-4">
                @csrf
                <input type="hidden" id="edit_schedule_id">

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">{{ __('Business profile') }}</label>
                        <div class="nice-select-wrapper">
                            <select name="profile_id" id="edit_profile_id" class="nice-select" required>
                                @foreach($profiles as $profile)
                                    <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="label">{{ __('Employee (optional)') }}</label>
                        <div class="nice-select-wrapper">
                            <select name="employee_id" id="edit_employee_id" class="nice-select">
                                <option value="">{{ __('No specific employee (general time)') }}</option>
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
                    <label class="label">{{ __('Day of week') }}</label>
                    <div class="nice-select-wrapper">
                        <select name="day_of_week" id="edit_day_of_week" class="nice-select" required>
                            @foreach([1=>__('Monday'),2=>__('Tuesday'),3=>__('Wednesday'),4=>__('Thursday'),5=>__('Friday'),6=>__('Saturday'),0=>__('Sunday')] as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-slate-50 pt-4">
                    <div>
                        <label class="label">{{ __('Opens at') }}</label>
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
                        <label class="label">{{ __('Closes at') }}</label>
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
                        <label for="edit_has_break" class="text-xs font-bold uppercase text-slate-700 cursor-pointer">{{ __('Break') }}</label>
                    </div>
                    <div id="break_fields_edit" class="grid grid-cols-2 gap-3 hidden">
                        <div>
                            <label class="label text-[10px]">{{ __('Break from') }}</label>
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
                            <label class="label text-[10px]">{{ __('Break to') }}</label>
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
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-200/50">
                        {{ __('Save changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        if ($.fn.niceSelect) {
            $('.nice-select').niceSelect();
        }
    });

    function openAddScheduleModal() {
        document.getElementById('addScheduleModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        if ($.fn.niceSelect) {
            $('#addScheduleModal .nice-select').niceSelect('update');
        }
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

        form.action = `/owner/schedules/${id}/update`;
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
        document.body.classList.add('overflow-hidden');
        if ($.fn.niceSelect) {
            $('#editScheduleModal .nice-select').niceSelect('update');
        }
    }

    function closeEditModal() {
        const modal = document.getElementById('editScheduleModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>
@endsection
