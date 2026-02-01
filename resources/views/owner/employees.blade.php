@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Môj tím</h1>
        </div>
        <span class="badge">Správa zamestnancov</span>
    </div>

    @include('owner.partials.nav')

    @if(session('status'))
        <div class="card border-emerald-200 bg-emerald-50 text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid lg:grid-cols-[1.1fr,1fr] gap-4">
        <div class="card space-y-4">
            <h2 class="font-semibold text-lg text-slate-900">Pridať člena tímu</h2>
            <form method="POST" action="{{ route('owner.employees.store') }}" class="space-y-3">
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
                    <label class="label">Meno a priezvisko</label>
                    <input type="text" name="name" class="input-control" value="{{ old('name') }}" placeholder="napr. Peter Novák" required>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="label">E-mail</label>
                        <input type="email" name="email" class="input-control" value="{{ old('email') }}" placeholder="peter@novak.sk">
                    </div>
                    <div>
                        <label class="label">Telefón</label>
                        <input type="text" name="phone" class="input-control" value="{{ old('phone') }}" placeholder="+421 9xx xxx xxx">
                    </div>
                </div>
                <button type="submit" class="w-full px-4 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition">
                    Uložiť zamestnanca
                </button>
            </form>
        </div>

        <div class="card space-y-3">
            <h2 class="font-semibold text-lg text-slate-900">Aktuálny tím</h2>
            <div class="space-y-3 max-h-[75vh] overflow-y-auto pr-1">
                @forelse($employees as $employee)
                    <div class="border border-slate-100 rounded-xl p-4 bg-white/80 space-y-3 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="font-bold text-slate-900">{{ $employee->name }}</h3>
                                <p class="text-xs text-slate-500 uppercase tracking-wider">{{ $employee->profile->name }}</p>
                                <p class="text-sm text-slate-600 mt-1">
                                    {{ $employee->email ?? 'Bez e-mailu' }}
                                    @if($employee->phone) • {{ $employee->phone }} @endif
                                </p>
                            </div>
                        </div>

                        <details class="text-sm">
                            <summary class="cursor-pointer text-slate-600 hover:text-slate-900 font-medium py-1">Upraviť údaje</summary>
                            <form method="POST" action="{{ route('owner.employees.update', $employee) }}" class="space-y-3 mt-2 pt-2 border-t border-slate-100">
                                @csrf
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-slate-500">Meno</label>
                                    <input type="text" name="name" class="input-control !py-1.5 !text-sm" value="{{ $employee->name }}" required>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-slate-500">E-mail</label>
                                        <input type="email" name="email" class="input-control !py-1.5 !text-sm" value="{{ $employee->email }}">
                                    </div>
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-slate-500">Telefón</label>
                                        <input type="text" name="phone" class="input-control !py-1.5 !text-sm" value="{{ $employee->phone }}">
                                    </div>
                                </div>
                                <button class="w-full px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold">Uložiť zmeny</button>
                            </form>
                        </details>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Zatiaľ nemáte v tíme žiadnych zamestnancov.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
