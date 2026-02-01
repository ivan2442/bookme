@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Pracovná doba</h1>
        </div>
        <span class="badge">Nastavenie časov</span>
    </div>

    @include('owner.partials.nav')

    @if(session('status'))
        <div class="card border-emerald-200 bg-emerald-50 text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid lg:grid-cols-[1.1fr,1fr] gap-4">
        <div class="card space-y-4">
            <h2 class="font-semibold text-lg text-slate-900">Pridať pracovný čas</h2>
            <form method="POST" action="{{ route('owner.schedules.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="label">Prevádzka</label>
                    <select name="profile_id" class="input-control" required>
                        @foreach($profiles as $profile)
                            <option value="{{ $profile->id }}" @selected(old('profile_id') == $profile->id)>{{ $profile->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Zamestnanec (voliteľné)</label>
                    <select name="employee_id" class="input-control">
                        <option value="">Bez väzby na zamestnanca (všeobecný čas)</option>
                        @foreach($profiles as $profile)
                            @foreach($profile->employees as $employee)
                                <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->name }} — {{ $profile->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-500 mt-1 italic">Ak nevyberiete zamestnanca, čas sa aplikuje na celú prevádzku.</p>
                </div>
                <div class="grid sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-3">
                        <label class="label">Dni v týždni</label>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <button type="button" onclick="selectAllDays()" class="px-2 py-1 text-[10px] font-bold uppercase bg-slate-100 text-slate-600 rounded hover:bg-slate-200 transition">Všetky</button>
                            <button type="button" onclick="selectWorkDays()" class="px-2 py-1 text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700 rounded hover:bg-emerald-200 transition">Po - Pia</button>
                            <button type="button" onclick="clearDays()" class="px-2 py-1 text-[10px] font-bold uppercase bg-red-100 text-red-700 rounded hover:bg-red-200 transition">Zrušiť výber</button>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach([1=>'Pondelok',2=>'Utorok',3=>'Streda',4=>'Štvrtok',5=>'Piatok',6=>'Sobota',0=>'Nedeľa'] as $val => $label)
                                <label class="flex items-center gap-2 p-2 border border-slate-100 rounded-lg hover:bg-slate-50 cursor-pointer">
                                    <input type="checkbox" name="days[]" value="{{ $val }}" class="h-4 w-4 day-checkbox" @checked(old('day_of_week') == (string)$val || (is_array(old('days')) && in_array($val, old('days'))))>
                                    <span class="text-xs">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('day_of_week')
                            <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-1">
                        <label class="label">Otvárame o</label>
                        <input type="time" name="start_time" class="input-control" required value="{{ old('start_time', '09:00') }}">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="label">Zatvárame o</label>
                        <input type="time" name="end_time" class="input-control" required value="{{ old('end_time', '17:00') }}">
                    </div>
                </div>
                <button type="submit" class="w-full px-4 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition">
                    Uložiť pracovný čas
                </button>
            </form>
        </div>

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
                        </div>
                        <form method="POST" action="{{ route('owner.schedules.delete', $schedule) }}" onsubmit="return confirmDelete(event, 'Naozaj odstrániť tento čas?')">
                            @csrf @method('DELETE')
                            <button class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 italic text-center py-4">Zatiaľ nie sú nastavené žiadne pracovné časy.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

<script>
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
</script>
@endsection
