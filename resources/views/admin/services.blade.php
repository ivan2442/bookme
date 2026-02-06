@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-3xl text-slate-900">Služby a varianty</h1>
            <p class="text-sm text-slate-500">Globálna správa služieb pre všetky prevádzky.</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1.3fr,1fr] gap-6">
        <div class="card space-y-6 p-6">
            <h2 class="text-xl font-bold text-slate-900">Pridať službu</h2>
            <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-4">
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
                        <label class="label">Názov</label>
                        <input type="text" name="name" class="input-control" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label class="label">Kategória</label>
                        <input type="text" name="category" class="input-control" value="{{ old('category') }}" placeholder="napr. Kaderníctvo">
                    </div>
                </div>
                <div>
                    <label class="label">Popis</label>
                    <textarea name="description" rows="2" class="input-control" placeholder="Detail služby">{{ old('description') }}</textarea>
                </div>
                <div class="grid sm:grid-cols-3 gap-3">
                    <div>
                        <label class="label">Dĺžka (min)</label>
                        <input type="number" name="base_duration_minutes" class="input-control" value="{{ old('base_duration_minutes', 30) }}" min="5" max="480" required>
                    </div>
                    <div>
                        <label class="label">Cena</label>
                        <input type="number" name="base_price" class="input-control" value="{{ old('base_price', 0) }}" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label class="label">Mena</label>
                        <input type="text" name="currency" class="input-control" value="{{ old('currency', 'EUR') }}" maxlength="3" required>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-3 space-y-2">
                    <h3 class="font-semibold text-slate-900">Voliteľný variant</h3>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <input type="text" name="variant_name" class="input-control" value="{{ old('variant_name') }}" placeholder="Názov variantu">
                        <input type="number" name="variant_duration_minutes" class="input-control" value="{{ old('variant_duration_minutes') }}" placeholder="Dĺžka (min)">
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <input type="number" name="variant_price" class="input-control" value="{{ old('variant_price') }}" placeholder="Cena">
                        <input type="text" name="currency_variant_helper" class="input-control" value="Mena sa použije vyššie" disabled>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-3 space-y-2">
                    <h3 class="font-semibold text-slate-900">Pakavoz Integrácia</h3>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_pakavoz_enabled" value="1" id="admin_add_pakavoz_enabled" onchange="togglePakavozKeyAdmin('add')">
                        <label for="admin_add_pakavoz_enabled" class="label !mb-0">Aktivovať Pakavoz API</label>
                    </div>
                    <div id="admin_pakavoz_key_container_add" style="display: none">
                        <label class="label">Pakavoz API Kľúč</label>
                        <input type="text" name="pakavoz_api_key" class="input-control" placeholder="pakavoz_secure_token_...">
                    </div>
                </div>

                <button type="submit" class="px-4 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-semibold transition shadow-md shadow-emerald-200/70">
                    Uložiť službu
                </button>
            </form>
        </div>

        <div class="card space-y-3">
            <h2 class="font-semibold text-lg text-slate-900">Existujúce služby</h2>
            <div class="space-y-3 max-h-[75vh] overflow-y-auto pr-1">
                @forelse($services as $service)
                    <div class="border border-slate-100 rounded-xl p-3 bg-white/80 space-y-3">
                        <details class="border border-slate-200 rounded-lg p-2">
                            <summary class="cursor-pointer font-semibold text-slate-900">Upraviť službu</summary>
                            <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-2 mt-2">
                                @csrf
                                <input type="text" name="name" class="input-control" value="{{ $service->name }}" required>
                                <input type="text" name="category" class="input-control" value="{{ $service->category }}" placeholder="Kategória">
                                <textarea name="description" class="input-control" rows="2" placeholder="Popis">{{ $service->description }}</textarea>
                                <div class="grid grid-cols-3 gap-2">
                                    <input type="number" name="base_duration_minutes" class="input-control" value="{{ $service->base_duration_minutes }}" min="5" max="480" required>
                                    <input type="number" name="base_price" class="input-control" value="{{ $service->base_price }}" step="0.01" min="0" required>
                                    <input type="text" name="currency" class="input-control" value="{{ $service->currency }}" maxlength="3" required>
                                </div>
                                <label class="flex items-center gap-2 text-sm text-slate-600">
                                    <input type="checkbox" name="is_active" value="1" @checked($service->is_active) class="h-4 w-4">
                                    Aktívna
                                </label>
                                <div class="border-t border-slate-100 pt-2 mt-1 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="is_pakavoz_enabled" value="1" @checked($service->is_pakavoz_enabled) id="admin_pakavoz_enabled_{{ $service->id }}" onchange="togglePakavozKeyAdmin('{{ $service->id }}')">
                                        <label for="admin_pakavoz_enabled_{{ $service->id }}" class="text-xs font-semibold text-slate-600">Aktivovať Pakavoz API</label>
                                    </div>
                                    <div id="admin_pakavoz_key_container_{{ $service->id }}" @style(['display: none' => !$service->is_pakavoz_enabled])>
                                        <input type="text" name="pakavoz_api_key" class="input-control !py-1 !text-xs" value="{{ $service->pakavoz_api_key }}" placeholder="Pakavoz API Kľúč">
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <div>
                                        <label class="label">Zamestnanci tejto služby</label>
                                        <select name="employee_ids[]" class="input-control" multiple>
                                            @foreach($service->profile->employees as $employee)
                                                <option value="{{ $employee->id }}" @selected(isset($service->variants[0]) && $service->variants[0]->employees->contains($employee->id))>
                                                    {{ $employee->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="text-xs text-slate-500 mt-1">Priradenie sa aplikuje na všetky varianty.</p>
                                    </div>
                                <div class="flex justify-end">
                                    <button class="px-3 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800">Uložiť službu</button>
                                </div>
                                </div>
                            </form>
                        </details>

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $service->name }}</p>
                                <p class="text-sm text-slate-600">{{ $service->profile?->name }} • {{ $service->category }}</p>
                            </div>
                            <span class="text-sm font-semibold text-slate-900">€{{ number_format($service->base_price, 2) }}</span>
                        </div>
                        <p class="text-sm text-slate-600">{{ $service->description }}</p>

                        <div class="mt-3 space-y-2">
                            <p class="text-xs uppercase tracking-widest text-slate-500">Varianty</p>
                            @forelse($service->variants as $variant)
                                <details class="text-sm bg-slate-50 rounded-lg px-2 py-1 space-y-2">
                                    <summary class="cursor-pointer flex items-center justify-between">
                                        <span>{{ $variant->name }} ({{ $variant->duration_minutes }} min)</span>
                                        <span class="font-semibold">€{{ number_format($variant->price ?? 0, 2) }}</span>
                                    </summary>
                                    @if($variant->employees->count())
                                        <p class="text-xs text-slate-500">Zamestnanci: {{ $variant->employees->pluck('name')->join(', ') }}</p>
                                    @endif
                                    <form method="POST" action="{{ route('admin.services.variants.update', [$service, $variant]) }}" class="grid sm:grid-cols-2 gap-2">
                                        @csrf
                                        <input type="text" name="name" class="input-control" value="{{ $variant->name }}" required>
                                        <input type="number" name="duration_minutes" class="input-control" value="{{ $variant->duration_minutes }}" min="5" max="480" required>
                                        <input type="number" name="price" class="input-control" value="{{ $variant->price }}" step="0.01" min="0" required>
                                        <input type="text" name="currency" class="input-control" value="{{ $variant->currency }}" maxlength="3" required>
                                        <input type="number" name="buffer_before_minutes" class="input-control" value="{{ $variant->buffer_before_minutes }}" min="0" max="120" placeholder="Buffer pred">
                                        <input type="number" name="buffer_after_minutes" class="input-control" value="{{ $variant->buffer_after_minutes }}" min="0" max="120" placeholder="Buffer po">
                                        <label class="flex items-center gap-2 text-sm text-slate-600">
                                            <input type="checkbox" name="is_active" value="1" @checked($variant->is_active) class="h-4 w-4">
                                            Aktívny
                                        </label>
                                        <div class="flex gap-2 sm:col-span-2">
                                            <button type="submit" class="flex-1 px-3 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800">Uložiť variant</button>
                                            <button formaction="{{ route('admin.services.variants.delete', [$service, $variant]) }}" formmethod="POST" class="w-full px-3 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700" onclick="return confirmDelete(event, 'Odstrániť variant?')">
                                                @csrf
                                                @method('DELETE')
                                                Odstrániť
                                            </button>
                                        </div>
                                    </form>
                                </details>
                            @empty
                                <p class="text-sm text-slate-500">Žiadne varianty zatiaľ.</p>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('admin.services.variants.store', $service) }}" class="mt-3 grid sm:grid-cols-2 gap-2">
                            @csrf
                            <input type="text" name="name" class="input-control" placeholder="Názov variantu" required>
                            <input type="number" name="duration_minutes" class="input-control" placeholder="Dĺžka (min)" min="5" max="480" required>
                            <input type="number" name="price" class="input-control" placeholder="Cena" step="0.01" min="0" required>
                            <input type="text" name="currency" class="input-control" value="{{ $service->currency }}" maxlength="3" required>
                            <input type="number" name="buffer_before_minutes" class="input-control" placeholder="Buffer pred (min)" min="0" max="120">
                            <input type="number" name="buffer_after_minutes" class="input-control" placeholder="Buffer po (min)" min="0" max="120">
                            <input type="hidden" name="employee_ids[]" value="">
                            <button type="submit" class="sm:col-span-2 px-3 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800">Pridať variant</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Zatiaľ nie sú vytvorené žiadne služby.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    function togglePakavozKeyAdmin(id) {
        const checkbox = id === 'add' ? document.getElementById('admin_add_pakavoz_enabled') : document.getElementById('admin_pakavoz_enabled_' + id);
        const container = document.getElementById('admin_pakavoz_key_container_' + id);
        if (checkbox.checked) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }
</script>
@endsection
