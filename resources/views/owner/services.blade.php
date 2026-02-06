@extends('layouts.owner')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">Moje služby</h1>
            <p class="text-sm text-slate-500">Správa ponuky služieb pre vašu prevádzku.</p>
        </div>
        <button onclick="openAddServiceModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Pridať službu</span>
        </button>
    </div>

    <div class="grid lg:grid-cols-[1fr,350px] gap-6 items-start">
        <div class="card space-y-3">
            <h2 class="font-semibold text-lg text-slate-900">Zoznam služieb</h2>
            <div class="space-y-4 max-h-[75vh] overflow-y-auto pr-1">
                @forelse($services as $service)
                    <div class="border border-slate-100 rounded-xl p-4 bg-white/80 space-y-3 shadow-sm hover:border-emerald-100 transition-colors">
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
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-slate-500">Názov služby</label>
                                        <input type="text" name="name" class="input-control !py-1.5 !text-sm" value="{{ $service->name }}" required>
                                    </div>
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-slate-500">Kategória</label>
                                        <input type="text" name="category" class="input-control !py-1.5 !text-sm" value="{{ $service->category }}" placeholder="Kategória">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-slate-500">Dĺžka (min)</label>
                                        <input type="number" name="base_duration_minutes" class="input-control !py-1.5 !text-sm" value="{{ $service->base_duration_minutes }}" required>
                                    </div>
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-slate-500">Cena (€)</label>
                                        <input type="number" name="base_price" class="input-control !py-1.5 !text-sm" value="{{ $service->base_price }}" step="0.01" required>
                                    </div>
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
                                <div class="grid grid-cols-2 gap-2 border-t border-slate-50 pt-2 mt-2">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="is_pakavoz_enabled" value="1" @checked($service->is_pakavoz_enabled) id="pakavoz_enabled_{{ $service->id }}" onchange="togglePakavozKey('{{ $service->id }}')">
                                        <label for="pakavoz_enabled_{{ $service->id }}" class="text-[10px] uppercase font-bold text-slate-500">Pakavoz API</label>
                                    </div>
                                    <div id="pakavoz_key_container_{{ $service->id }}" @style(['display: none' => !$service->is_pakavoz_enabled])>
                                        <label class="text-[10px] uppercase font-bold text-slate-500">Pakavoz API Kľúč</label>
                                        <input type="text" name="pakavoz_api_key" class="input-control !py-1.5 !text-sm" value="{{ $service->pakavoz_api_key }}" placeholder="pakavoz_secure_token_...">
                                    </div>
                                </div>
                                <button class="w-full px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold">Uložiť zmeny</button>
                            </form>
                        </details>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 italic p-8 text-center">Zatiaľ nemáte pridané žiadne služby.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Informácia
                </h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Služby definujú, čo ponúkate svojim klientom. Môžete nastaviť dĺžku trvania, cenu a priradiť konkrétnych zamestnancov, ktorí danú službu vykonávajú.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Add Service Modal -->
<div id="addServiceModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeAddServiceModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-display font-semibold text-slate-900">Pridať novú službu</h3>
                <button onclick="closeAddServiceModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('owner.services.store') }}" class="space-y-4">
                @csrf
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

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Názov služby</label>
                        <input type="text" name="name" class="input-control" placeholder="napr. Pánsky strih" required>
                    </div>
                    <div>
                        <label class="label">Kategória</label>
                        <input type="text" name="category" class="input-control" placeholder="napr. Kaderníctvo">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Základná dĺžka (min)</label>
                        <input type="number" name="base_duration_minutes" class="input-control" value="30" min="5" required>
                    </div>
                    <div>
                        <label class="label">Základná cena (€)</label>
                        <input type="number" name="base_price" class="input-control" value="0" step="0.01" min="0" required>
                    </div>
                </div>

                <div>
                    <label class="label">Priradiť zamestnancov</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($employees as $employee)
                            <label class="flex items-center gap-2 p-2 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                                <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 rounded border-slate-300">
                                <span class="text-sm font-medium text-slate-700">{{ $employee->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4 border-t border-slate-50 pt-4">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_pakavoz_enabled" value="1" id="add_pakavoz_enabled" onchange="togglePakavozKey('add')">
                        <label for="add_pakavoz_enabled" class="label !mb-0">Aktivovať Pakavoz API</label>
                    </div>
                    <div id="pakavoz_key_container_add" style="display: none">
                        <label class="label">Pakavoz API Kľúč</label>
                        <input type="text" name="pakavoz_api_key" class="input-control" placeholder="pakavoz_secure_token_...">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-50">
                    <button type="button" onclick="closeAddServiceModal()" class="px-6 py-2 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">Zrušiť</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-lg shadow-emerald-200/50">Vytvoriť službu</button>
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

    function openAddServiceModal() {
        document.getElementById('addServiceModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        if ($.fn.niceSelect) {
            $('#addServiceModal .nice-select').niceSelect('update');
        }
    }

    function closeAddServiceModal() {
        document.getElementById('addServiceModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function togglePakavozKey(id) {
        const checkbox = id === 'add' ? document.getElementById('add_pakavoz_enabled') : document.getElementById('pakavoz_enabled_' + id);
        const container = document.getElementById('pakavoz_key_container_' + id);
        if (checkbox.checked) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }
</script>
@endsection
