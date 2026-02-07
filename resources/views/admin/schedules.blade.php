@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-3xl text-slate-900">Pracovné dni a časy</h1>
            <p class="text-sm text-slate-500">Definícia otváracích hodín a dostupnosti zamestnancov.</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1.1fr,1fr] gap-6">
        <div class="card space-y-6 p-6">
            <h2 class="text-xl font-bold text-slate-900">Pridať pracovný čas</h2>
            <form method="POST" action="{{ route('admin.schedules.store') }}" class="space-y-4">
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
                        <option value="">Bez väzby na zamestnanca</option>
                        @foreach($profiles as $profile)
                            @foreach($profile->employees as $employee)
                                <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->name }} — {{ $profile->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="grid sm:grid-cols-3 gap-3">
                    <div>
                        <label class="label">Deň</label>
                        <select name="day_of_week" class="input-control" required>
                            <option value="">Vyber deň</option>
                            @foreach([0=>'Nedeľa',1=>'Pondelok',2=>'Utorok',3=>'Streda',4=>'Štvrtok',5=>'Piatok',6=>'Sobota'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('day_of_week') == (string)$val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Od</label>
                        <input type="text" name="start_time" class="input-control js-flatpickr-time" value="{{ old('start_time', '09:00') }}" required>
                    </div>
                    <div>
                        <label class="label">Do</label>
                        <input type="text" name="end_time" class="input-control js-flatpickr-time" value="{{ old('end_time', '17:00') }}" required>
                    </div>
                </div>
                <button type="submit" class="px-4 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-semibold transition shadow-md shadow-emerald-200/70">
                    Uložiť čas
                </button>
            </form>
        </div>

        <div class="card space-y-3">
            <h2 class="font-semibold text-lg text-slate-900">Aktívne časy</h2>
            <div class="space-y-2 max-h-[75vh] overflow-y-auto pr-1">
                @forelse($schedules as $schedule)
                    <div class="border border-slate-100 rounded-xl p-3 bg-white/80 flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $schedule->profile?->name }}</p>
                            <p class="text-sm text-slate-600">
                                {{ ['Nedeľa','Pondelok','Utorok','Streda','Štvrtok','Piatok','Sobota'][$schedule->day_of_week] ?? $schedule->day_of_week }}
                                • {{ $schedule->start_time }} - {{ $schedule->end_time }}
                            </p>
                            @if($schedule->employee)
                                <p class="text-xs text-slate-500">Zamestnanec: {{ $schedule->employee->name }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('admin.schedules.delete', $schedule) }}">
                            @csrf
                            @method('DELETE')
                            <button class="link subtle" onclick="return confirmDelete(event, 'Odstrániť tento čas?')">Odstrániť</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Žiadne zadané pracovné časy.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
