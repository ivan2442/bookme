@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3 mb-6">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Môj tím</h1>
        </div>
        <div class="flex gap-2">
            <button onclick="openAddEmployeeModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Pridať člena tímu</span>
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

<!-- Add Employee Modal -->
<div id="addEmployeeModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeAddEmployeeModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-display font-semibold text-slate-900">Pridať člena tímu</h3>
                <button onclick="closeAddEmployeeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('owner.employees.store') }}" class="space-y-4">
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
                <div>
                    <label class="label">Meno a priezvisko</label>
                    <input type="text" name="name" class="input-control" placeholder="napr. Peter Novák" required>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">E-mail</label>
                        <input type="email" name="email" class="input-control" placeholder="peter@novak.sk">
                    </div>
                    <div>
                        <label class="label">Telefón</label>
                        <input type="text" name="phone" class="input-control" placeholder="+421 9xx xxx xxx">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-50">
                    <button type="button" onclick="closeAddEmployeeModal()" class="px-6 py-2 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">Zrušiť</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-200/50">Uložiť zamestnanca</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.nice-select').niceSelect();
    });

    function openAddEmployeeModal() {
        document.getElementById('addEmployeeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        $('#addEmployeeModal .nice-select').niceSelect('update');
    }

    function closeAddEmployeeModal() {
        document.getElementById('addEmployeeModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>
@endsection
