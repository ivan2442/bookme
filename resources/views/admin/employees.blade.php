@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-3xl text-slate-900">Zamestnanci</h1>
            <p class="text-sm text-slate-500">Správa tímu a zamestnancov pre jednotlivé prevádzky.</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1.1fr,1fr] gap-6">
        <div class="card space-y-6 p-6">
            <h2 class="text-xl font-bold text-slate-900">Pridať zamestnanca</h2>
            <form method="POST" action="{{ route('admin.employees.store') }}" class="space-y-4">
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
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="label">Meno</label>
                        <input type="text" name="name" class="input-control" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label class="label">E-mail</label>
                        <input type="email" name="email" class="input-control" value="{{ old('email') }}" placeholder="na@priklad.sk">
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="label">Telefón</label>
                        <input type="text" name="phone" class="input-control" value="{{ old('phone') }}" placeholder="+421...">
                    </div>
                    <div>
                        <label class="label">Farba kalendára</label>
                        <input type="text" name="color" class="input-control" value="{{ old('color') }}" placeholder="#10b981">
                    </div>
                </div>
                <div>
                    <label class="label">Bio</label>
                    <textarea name="bio" rows="2" class="input-control" placeholder="Skúsenosti, špecializácia">{{ old('bio') }}</textarea>
                </div>
                <button type="submit" class="px-4 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-semibold transition shadow-md shadow-emerald-200/70">
                    Uložiť zamestnanca
                </button>
            </form>
        </div>

        <div class="card space-y-3">
            <h2 class="font-semibold text-lg text-slate-900">Zoznam tímu</h2>
            <div class="space-y-2 max-h-[75vh] overflow-y-auto pr-1">
                @forelse($employees as $employee)
                    <div class="border border-slate-100 rounded-xl p-3 bg-white/80 space-y-2">
                        <details class="border border-slate-200 rounded-lg p-2">
                            <summary class="cursor-pointer font-semibold text-slate-900">Upraviť</summary>
                            <form method="POST" action="{{ route('admin.employees.update', $employee) }}" class="space-y-2 mt-2">
                                @csrf
                                <div>
                                    <label class="label">Prevádzka</label>
                                    <select name="profile_id" class="input-control" required>
                                        @foreach($profiles as $profile)
                                            <option value="{{ $profile->id }}" @selected($profile->id === $employee->profile_id)>{{ $profile->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="text" name="name" class="input-control" value="{{ $employee->name }}" required>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="email" name="email" class="input-control" value="{{ $employee->email }}" placeholder="Email">
                                    <input type="text" name="phone" class="input-control" value="{{ $employee->phone }}" placeholder="Telefón">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="color" class="input-control" value="{{ $employee->color }}" placeholder="#10b981">
                                    <label class="flex items-center gap-2 text-sm text-slate-600">
                                        <input type="checkbox" name="is_active" value="1" @checked($employee->is_active) class="h-4 w-4">
                                        Aktívny
                                    </label>
                                </div>
                                <textarea name="bio" rows="2" class="input-control" placeholder="Bio">{{ $employee->bio }}</textarea>
                                <div class="flex justify-end">
                                    <button class="px-3 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800">Uložiť</button>
                                </div>
                            </form>
                        </details>
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $employee->name }}</p>
                                <p class="text-sm text-slate-600">{{ $employee->profile?->name }}</p>
                                <p class="text-xs text-slate-500">{{ $employee->email }} • {{ $employee->phone }}</p>
                            </div>
                            @if($employee->color)
                                <span class="h-8 w-8 rounded-full border border-slate-200" style="background: {{ $employee->color }}"></span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Zatiaľ žiadni zamestnanci.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
