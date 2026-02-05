@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3 mb-6">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Moje služby</h1>
        </div>
        <div class="flex gap-2">
            <button onclick="openAddServiceModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Pridať novú službu</span>
            </button>
        </div>
    </div>

    @include('owner.partials.nav')

    @if(session('status'))
        <div class="card border-emerald-200 bg-emerald-50 text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    <div class="max-w-4xl mx-auto">
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

{{--
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
--}}
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Zatiaľ nemáte žiadne služby.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

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
        $('.nice-select').niceSelect();
    });

    function openAddServiceModal() {
        document.getElementById('addServiceModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        $('#addServiceModal .nice-select').niceSelect('update');
    }

    function closeAddServiceModal() {
        document.getElementById('addServiceModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
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
