@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6">
    <div class="flex items-center justify-between gap-3 mb-6">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Admin</p>
            <h1 class="font-display text-3xl text-slate-900">Prehľad prevádzky</h1>
        </div>
        <span class="badge">Live</span>
    </div>

    @include('admin.partials.nav')

    <div class="grid md:grid-cols-4 gap-4 mb-6">
        <div class="card">
            <p class="text-sm text-slate-500 mb-1">Počet prevádzok</p>
            <p class="text-3xl font-semibold text-slate-900">{{ $stats['total_profiles'] }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-slate-500 mb-1">Aktívne (publikované)</p>
            <p class="text-3xl font-semibold text-slate-900">{{ $stats['active_profiles'] }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-slate-500 mb-1">Rezervácie celkom</p>
            <p class="text-3xl font-semibold text-slate-900">{{ $stats['total_appointments'] }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-slate-500 mb-1">Obrat celkom</p>
            <p class="text-3xl font-semibold text-slate-900">€{{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-4">
        <div class="card lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-lg text-slate-900">Najnovšie prevádzky</h2>
                <a href="{{ route('admin.profiles') }}" class="link">Zobraziť všetky</a>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($latest_profiles as $profile)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $profile->name }}</p>
                            <p class="text-xs text-slate-500">{{ $profile->owner?->email }} • {{ $profile->city }}</p>
                        </div>
                        <span class="badge {{ $profile->status === 'published' ? 'bg-emerald-500' : 'bg-slate-400' }}">{{ $profile->status }}</span>
                    </div>
                @endforeach
            </div>

            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-semibold text-lg text-slate-900">Blížiace sa termíny (globálne)</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($upcoming as $appointment)
                        <div class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ optional($appointment->service)->name }}</p>
                                <p class="text-sm text-slate-600">
                                    {{ optional($appointment->profile)->name }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-slate-900">{{ $appointment->start_at?->format('d.m. H:i') }}</p>
                                <p class="text-sm text-slate-600">{{ ucfirst($appointment->status) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Zatiaľ žiadne rezervácie.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="space-y-4">
            <div class="card">
                <h2 class="font-semibold text-lg text-slate-900 mb-3">Rýchle akcie</h2>
                <div class="space-y-2">
                    <a class="admin-tab block text-center" href="{{ route('admin.profiles') }}">+ Pridať prevádzku</a>
                    <a class="admin-tab block text-center" href="{{ route('admin.appointments') }}">Zoznam rezervácií</a>
                    <a class="admin-tab block text-center" href="{{ route('admin.payments') }}">Finančný prehľad</a>
                    <a class="admin-tab block text-center" href="{{ route('admin.invoices') }}">Správa faktúr</a>
                </div>
            </div>

            @if($expiring_trials->isNotEmpty())
                <div class="card border-orange-100 bg-orange-50/30">
                    <h2 class="font-semibold text-lg text-slate-900 mb-3 flex items-center gap-2 text-orange-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Končiace predplatné
                    </h2>
                    <div class="space-y-3">
                        @foreach($expiring_trials as $profile)
                            <div class="p-3 bg-white rounded-xl border border-orange-100 shadow-sm">
                                <p class="font-bold text-slate-900 text-sm leading-tight">{{ $profile->name }}</p>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-[10px] uppercase font-bold text-slate-400">{{ $profile->city }}</span>
                                    @if($profile->trial_days_left > 0)
                                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">Zostáva {{ $profile->trial_time_left }}</span>
                                    @else
                                        <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md">Expirovalo</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
