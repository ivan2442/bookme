@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Moje služby</h1>
        </div>
        <span class="badge">Správa služieb</span>
    </div>

    @include('owner.partials.nav')

    @if(session('status'))
        <div class="card border-emerald-200 bg-emerald-50 text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid lg:grid-cols-[1.3fr,1fr] gap-4">
        <div class="card space-y-4">
            <h2 class="font-semibold text-lg text-slate-900">Pridať novú službu</h2>
            <form method="POST" action="{{ route('owner.services.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="label">Prevádzka</label>
                    <select name="profile_id" class="input-control" required>
                        @foreach($profiles as $profile)
                            <option value="{{ $profile->id }}" @selected(old('profile_id') == $profile->id)>{{ $profile->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="label">Názov služby</label>
                        <input type="text" name="name" class="input-control" value="{{ old('name') }}" placeholder="napr. Pánsky strih" required>
                    </div>
                    <div>
                        <label class="label">Kategória</label>
                        <input type="text" name="category" class="input-control" value="{{ old('category') }}" placeholder="napr. Kaderníctvo">
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="label">Základná dĺžka (min)</label>
                        <input type="number" name="base_duration_minutes" class="input-control" value="{{ old('base_duration_minutes', 30) }}" min="5" required>
                    </div>
                    <div>
                        <label class="label">Základná cena (€)</label>
                        <input type="number" name="base_price" class="input-control" value="{{ old('base_price', 0) }}" step="0.01" min="0" required>
                    </div>
                </div>
                <div>
                    <label class="label">Priradiť zamestnancov</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($employees as $employee)
                            <label class="flex items-center gap-2 p-2 border border-slate-100 rounded-lg hover:bg-slate-50 cursor-pointer">
                                <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" class="h-4 w-4" @checked(is_array(old('employee_ids')) && in_array($employee->id, old('employee_ids')))>
                                <span class="text-sm">{{ $employee->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="w-full px-4 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition">
                    Vytvoriť službu
                </button>
            </form>
        </div>

        <div class="card space-y-3">
            <h2 class="font-semibold text-lg text-slate-900">Zoznam služieb</h2>
            <div class="space-y-4 max-h-[75vh] overflow-y-auto pr-1">
                @forelse($services as $service)
                    <div class="border border-slate-100 rounded-xl p-4 bg-white/80 space-y-3 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-slate-900">{{ $service->name }}</h3>
                                <p class="text-xs text-slate-500 uppercase tracking-wider">{{ $service->profile->name }} • {{ $service->category }}</p>
                            </div>
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-bold">€{{ number_format($service->base_price, 2) }}</span>
                        </div>

                        <details class="text-sm">
                            <summary class="cursor-pointer text-slate-600 hover:text-slate-900 font-medium py-1">Upraviť službu</summary>
                            <form method="POST" action="{{ route('owner.services.update', $service) }}" class="space-y-3 mt-2 pt-2 border-t border-slate-100">
                                @csrf
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="name" class="input-control !py-1.5 !text-sm" value="{{ $service->name }}" required>
                                    <input type="text" name="category" class="input-control !py-1.5 !text-sm" value="{{ $service->category }}" placeholder="Kategória">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="number" name="base_duration_minutes" class="input-control !py-1.5 !text-sm" value="{{ $service->base_duration_minutes }}" required>
                                    <input type="number" name="base_price" class="input-control !py-1.5 !text-sm" value="{{ $service->base_price }}" step="0.01" required>
                                </div>
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-slate-500">Zamestnanci</label>
                                    <div class="grid grid-cols-2 gap-1 mt-1">
                                        @foreach($employees->where('profile_id', $service->profile_id) as $employee)
                                            <label class="flex items-center gap-1 text-xs">
                                                <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" @checked($service->employees->contains($employee->id))>
                                                {{ $employee->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <button class="w-full px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold">Uložiť zmeny</button>
                            </form>
                        </details>

                        <div class="mt-4 space-y-2">
                            <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Varianty služby</p>
                            @foreach($service->variants as $variant)
                                <div class="bg-slate-50 rounded-lg p-2 text-sm flex items-center justify-between group">
                                    <span>{{ $variant->name }} ({{ $variant->duration_minutes }} min)</span>
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold">€{{ number_format($variant->price, 2) }}</span>
                                        <form action="{{ route('owner.services.variants.delete', [$service, $variant]) }}" method="POST" onsubmit="return confirmDelete(event, 'Odstrániť variant?')">
                                            @csrf @method('DELETE')
                                            <button class="text-slate-300 hover:text-red-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach

                            <details class="text-sm">
                                <summary class="cursor-pointer text-emerald-600 hover:text-emerald-700 font-medium py-1">+ Pridať variant</summary>
                                <form method="POST" action="{{ route('owner.services.variants.store', $service) }}" class="space-y-2 mt-2 pt-2 border-t border-slate-100">
                                    @csrf
                                    <input type="text" name="name" class="input-control !py-1.5 !text-sm" placeholder="Názov variantu (napr. S umytím)" required>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="number" name="duration_minutes" class="input-control !py-1.5 !text-sm" placeholder="Extra čas (min)" required>
                                        <input type="number" name="price" class="input-control !py-1.5 !text-sm" placeholder="Extra cena (€)" step="0.01" required>
                                    </div>
                                    <button class="w-full px-3 py-1.5 rounded-lg bg-emerald-500 text-white text-xs font-semibold">Pridať variant</button>
                                </form>
                            </details>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Zatiaľ nemáte žiadne služby.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
