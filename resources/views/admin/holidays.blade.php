@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-3xl text-slate-900">Sviatky a uzávierky</h1>
            <p class="text-sm text-slate-500">Správa voľných dní a časových blokácií pre prevádzky.</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1.1fr,1fr] gap-6">
        <div class="card space-y-6 p-6">
            <h2 class="text-xl font-bold text-slate-900">Pridať sviatok / blokáciu</h2>
            <form method="POST" action="{{ route('admin.holidays.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="label">Prevádzka</label>
                    <select name="profile_id" class="input-control" required>
                        <option value="">Vyber prevádzku</option>
                        @foreach($profiles as $profile)
                            <option value="{{ $profile->id }}" @selected(old('profile_id') == $profile->id)>{{ $profile->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Zamestnanec (voliteľné)</label>
                    <select name="employee_id" class="input-control">
                        <option value="">Celá prevádzka</option>
                        @foreach($profiles as $profile)
                            @foreach($profile->employees as $employee)
                                <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->name }} — {{ $profile->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="grid sm:grid-cols-3 gap-3">
                    <div>
                        <label class="label">Dátum</label>
                        <input type="text" name="date" class="input-control" value="{{ old('date') }}" required readonly placeholder="YYYY-MM-DD">
                    </div>
                    <div>
                        <label class="label">Od</label>
                        <input type="time" name="start_time" class="input-control" value="{{ old('start_time') }}">
                    </div>
                    <div>
                        <label class="label">Do</label>
                        <input type="time" name="end_time" class="input-control" value="{{ old('end_time') }}">
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <input type="text" name="reason" class="input-control" value="{{ old('reason') }}" placeholder="Dôvod (sviatok, dovolenka)">
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="is_closed" value="1" class="h-4 w-4" @checked(old('is_closed', '1') == '1')>
                        Celý deň zatvorené
                    </label>
                </div>
                <div class="flex justify-end">
                    <button class="px-3 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800">Pridať</button>
                </div>
            </form>
        </div>

        <div class="card space-y-3">
            <h2 class="font-semibold text-lg text-slate-900">Zoznam</h2>
            <div class="space-y-2 max-h-[75vh] overflow-y-auto pr-1">
                @forelse($holidays as $holiday)
                    <div class="border border-slate-100 rounded-xl p-3 bg-white/80" id="holiday-view-{{ $holiday->id }}">
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $holiday->profile?->name }}</p>
                                <p class="text-sm text-slate-600">
                                    {{ $holiday->date->format('d.m.Y') }}
                                    @if($holiday->start_time && $holiday->end_time)
                                        • {{ $holiday->start_time }} - {{ $holiday->end_time }}
                                    @else
                                        • celý deň
                                    @endif
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $holiday->employee?->name ?? 'Prevádzka' }} — {{ $holiday->reason ?? 'blokácia' }}
                                </p>
                            </div>
                            <div class="flex gap-1">
                                <button onclick="toggleEdit({{ $holiday->id }})" class="p-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-emerald-100 hover:text-emerald-700 transition" title="Upraviť">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('admin.holidays.delete', $holiday) }}" method="POST" onsubmit="return confirmDelete(event, 'Naozaj chcete odstrániť tento sviatok?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-red-100 hover:text-red-700 transition" title="Odstrániť">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="border border-emerald-100 rounded-xl p-3 bg-emerald-50/30 hidden" id="holiday-edit-{{ $holiday->id }}">
                        <form method="POST" action="{{ route('admin.holidays.update', $holiday) }}" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-slate-500">Prevádzka</label>
                                    <select name="profile_id" class="input-control !py-1.5 !text-sm" required>
                                        @foreach($profiles as $p)
                                            <option value="{{ $p->id }}" {{ $holiday->profile_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-slate-500">Zamestnanec</label>
                                    <select name="employee_id" class="input-control !py-1.5 !text-sm">
                                        <option value="">Celá prevádzka</option>
                                        @foreach($profiles as $p)
                                            @foreach($p->employees as $e)
                                                <option value="{{ $e->id }}" {{ $holiday->employee_id == $e->id ? 'selected' : '' }}>{{ $e->name }} ({{ $p->name }})</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-slate-500">Dátum</label>
                                    <input type="text" name="date" value="{{ $holiday->date->format('Y-m-d') }}" class="input-control !py-1.5 !text-sm" required readonly placeholder="YYYY-MM-DD">
                                </div>
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-slate-500">Od</label>
                                    <input type="time" name="start_time" value="{{ $holiday->start_time ? substr($holiday->start_time, 0, 5) : '' }}" class="input-control !py-1.5 !text-sm">
                                </div>
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-slate-500">Do</label>
                                    <input type="time" name="end_time" value="{{ $holiday->end_time ? substr($holiday->end_time, 0, 5) : '' }}" class="input-control !py-1.5 !text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] uppercase font-bold text-slate-500">Dôvod</label>
                                <input type="text" name="reason" value="{{ $holiday->reason }}" class="input-control !py-1.5 !text-sm" placeholder="Dôvod">
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <label class="flex items-center gap-2 text-xs text-slate-600">
                                    <input type="checkbox" name="is_closed" value="1" class="h-3 w-3" {{ $holiday->is_closed ? 'checked' : '' }}>
                                    Zatvorené
                                </label>
                                <div class="flex gap-2">
                                    <button type="button" onclick="toggleEdit({{ $holiday->id }})" class="px-2 py-1 text-xs font-semibold text-slate-500 hover:text-slate-700">Zrušiť</button>
                                    <button type="submit" class="px-3 py-1 rounded-lg bg-emerald-500 text-white text-xs font-semibold hover:bg-emerald-600">Uložiť</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Žiadne blokácie.</p>
                @endforelse
            </div>
            <div>
                {{ $holidays->links() }}
            </div>
        </div>
    </div>
</div>
<script>
    function toggleEdit(id) {
        const view = document.getElementById(`holiday-view-${id}`);
        const edit = document.getElementById(`holiday-edit-${id}`);
        if (view.classList.contains('hidden')) {
            view.classList.remove('hidden');
            edit.classList.add('hidden');
        } else {
            view.classList.add('hidden');
            edit.classList.remove('hidden');
        }
    }
</script>
@endsection
