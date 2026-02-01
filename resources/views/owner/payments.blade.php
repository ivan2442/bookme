@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Platby</h1>
        </div>
        <span class="badge">Financie</span>
    </div>

    @include('owner.partials.nav')

    <div class="card p-12 text-center space-y-4">
        <div class="h-16 w-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3 1.343 3-3-1.343-3-3-3zM12 8V7m0 1v1m0 5v1m0-1c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM12 5a7 7 0 100 14 7 7 0 000-14z"/></svg>
        </div>
        <h2 class="text-xl font-bold text-slate-900">Prehľad platieb pripravujeme</h2>
        <p class="text-slate-500 max-w-sm mx-auto">Pracujeme na integrácii platobnej brány a podrobných finančných reportoch pre vaše prevádzky.</p>
        <div class="pt-4">
            <a href="{{ route('owner.dashboard') }}" class="admin-tab">Späť na dashboard</a>
        </div>
    </div>
</section>
@endsection
