@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3 mb-6">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Sviatky a uzávierky</h1>
        </div>
        <div class="flex gap-2">
            <button onclick="openAddHolidayModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Pridať sviatok / blokáciu</span>
            </button>
        </div>
    </div>

    @include('owner.partials.nav')

    @if(session('status'))
        <div class="card border-emerald-200 bg-emerald-50 text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="max-w-4xl mx-auto">
        <div class="card space-y-3">
            <h2 class="font-semibold text-lg text-slate-900">Zoznam blokácií</h2>
            <div class="space-y-2 max-h-[75vh] overflow-y-auto pr-1">
                @forelse($holidays as $holiday)
                    <div class="border border-slate-100 rounded-xl p-3 bg-white/80" id="holiday-view-{{ $holiday->id }}">
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <p class="font-bold text-slate-900">{{ $holiday->date->format('d.m.Y') }}</p>
                                <p class="text-sm text-slate-600">
                                    @if($holiday->start_time && $holiday->end_time)
                                        {{ substr($holiday->start_time, 0, 5) }} - {{ substr($holiday->end_time, 0, 5) }}
                                    @else
                                        celý deň
                                    @endif
                                    <span class="text-xs text-slate-400">({{ $holiday->profile->name }})</span>
                                </p>
                                <p class="text-xs text-slate-500 font-medium mt-1">
                                    {{ $holiday->employee->name ?? 'Celá prevádzka' }} — {{ $holiday->reason ?? 'blokácia' }}
                                </p>
                            </div>
                            <div class="flex gap-1">
                                <button onclick="toggleEdit({{ $holiday->id }})" class="p-1.5 rounded-lg bg-slate-50 text-slate-400 hover:bg-emerald-50 hover:text-emerald-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('owner.holidays.delete', $holiday) }}" method="POST" onsubmit="return confirmDelete(event, 'Odstrániť?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 rounded-lg bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-500 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="border border-emerald-100 rounded-xl p-3 bg-emerald-50/30 hidden" id="holiday-edit-{{ $holiday->id }}">
                        <form method="POST" action="{{ route('owner.holidays.update', $holiday) }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="profile_id" value="{{ $holiday->profile_id }}">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-slate-500">Zamestnanec</label>
                                    <div class="nice-select-wrapper">
                                        <select name="employee_id" class="nice-select">
                                            <option value="">Celá prevádzka</option>
                                            @foreach($profiles->where('id', $holiday->profile_id) as $p)
                                                @foreach($p->employees as $e)
                                                    <option value="{{ $e->id }}" @selected($holiday->employee_id == $e->id)>{{ $e->name }}</option>
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-slate-500">Dátum</label>
                                    <input type="text" name="date" value="{{ $holiday->date->format('Y-m-d') }}" class="input-control !py-1.5 !text-sm" required readonly placeholder="YYYY-MM-DD">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-slate-500">Od</label>
                                    <div class="nice-select-wrapper">
                                        <select name="start_time" class="nice-select nice-select-time">
                                            <option value="">Celý deň</option>
                                            @for($h = 0; $h <= 23; $h++)
                                                @foreach(['00', '30'] as $m)
                                                    @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                                    <option value="{{ $time }}" @selected($holiday->start_time && substr($holiday->start_time, 0, 5) == $time)>{{ $time }}</option>
                                                @endforeach
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-slate-500">Do</label>
                                    <div class="nice-select-wrapper">
                                        <select name="end_time" class="nice-select nice-select-time">
                                            <option value="">Celý deň</option>
                                            @for($h = 0; $h <= 23; $h++)
                                                @foreach(['00', '30'] as $m)
                                                    @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                                    <option value="{{ $time }}" @selected($holiday->end_time && substr($holiday->end_time, 0, 5) == $time)>{{ $time }}</option>
                                                @endforeach
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] uppercase font-bold text-slate-500">Dôvod</label>
                                <input type="text" name="reason" value="{{ $holiday->reason }}" class="input-control !py-1.5 !text-sm" placeholder="Dôvod">
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 text-xs text-slate-600">
                                    <input type="checkbox" name="is_closed" value="1" @checked($holiday->is_closed)>
                                    Zatvorené
                                </label>
                                <div class="flex gap-2">
                                    <button type="button" onclick="toggleEdit({{ $holiday->id }})" class="text-xs font-bold text-slate-400">Zrušiť</button>
                                    <button type="submit" class="px-3 py-1 rounded-lg bg-emerald-500 text-white text-xs font-bold">Uložiť</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 italic text-center py-4">Žiadne blokácie.</p>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $holidays->links() }}
            </div>
        </div>
    </div>
</section>

<!-- Add Holiday Modal -->
<div id="addHolidayModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeAddHolidayModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-display font-semibold text-slate-900">Pridať sviatok / blokáciu</h3>
                <button onclick="closeAddHolidayModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('owner.holidays.store') }}" class="space-y-4">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Prevádzka</label>
                        <select name="profile_id" class="nice-select" required>
                            @foreach($profiles as $profile)
                                <option value="{{ $profile->id }}" @selected(old('profile_id') == $profile->id)>{{ $profile->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Zamestnanec (voliteľné)</label>
                        <select name="employee_id" class="nice-select">
                            <option value="">Celá prevádzka</option>
                            @foreach($profiles as $profile)
                                @foreach($profile->employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->name }} — {{ $profile->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid sm:grid-cols-3 gap-4 border-t border-slate-50 pt-4">
                    <div>
                        <label class="label">Dátum</label>
                        <input type="text" name="date" id="add_holiday_date" class="input-control" value="{{ old('date') }}" required readonly placeholder="YYYY-MM-DD">
                    </div>
                    <div>
                        <label class="label">Od</label>
                        <div class="nice-select-wrapper">
                            <select name="start_time" class="nice-select nice-select-time">
                                <option value="">Celý deň</option>
                                @for($h = 0; $h <= 23; $h++)
                                    @foreach(['00', '30'] as $m)
                                        @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                        <option value="{{ $time }}" @selected(old('start_time') == $time)>{{ $time }}</option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Do</label>
                        <div class="nice-select-wrapper">
                            <select name="end_time" class="nice-select nice-select-time">
                                <option value="">Celý deň</option>
                                @for($h = 0; $h <= 23; $h++)
                                    @foreach(['00', '30'] as $m)
                                        @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                        <option value="{{ $time }}" @selected(old('end_time') == $time)>{{ $time }}</option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid sm:grid-cols-[1fr,200px] gap-4">
                    <div>
                        <label class="label">Dôvod</label>
                        <input type="text" name="reason" class="input-control" value="{{ old('reason') }}" placeholder="Dôvod (sviatok, dovolenka)">
                    </div>
                    <div class="flex items-end pb-3">
                        <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                            <input type="checkbox" name="is_closed" value="1" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 rounded border-slate-300" @checked(old('is_closed', '1') == '1')>
                            Celý deň zatvorené
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-50">
                    <button type="button" onclick="closeAddHolidayModal()" class="px-6 py-2 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">Zrušiť</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-200/50">Pridať blokáciu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#add_holiday_date", {
            dateFormat: "Y-m-d",
            locale: "sk",
            minDate: "today"
        });
    });

    $(document).ready(function() {
        $('.nice-select').niceSelect();
    });

    function openAddHolidayModal() {
        document.getElementById('addHolidayModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        $('#addHolidayModal .nice-select').niceSelect('update');
    }

    function closeAddHolidayModal() {
        document.getElementById('addHolidayModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function toggleEdit(id) {
        const view = document.getElementById(`holiday-view-${id}`);
        const edit = document.getElementById(`holiday-edit-${id}`);
        view.classList.toggle('hidden');
        edit.classList.toggle('hidden');
        if (!edit.classList.contains('hidden')) {
            $(`#holiday-edit-${id} .nice-select`).niceSelect('update');
        }
    }
</script>
@endsection
