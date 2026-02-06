@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">Prehľad systému</h1>
            <p class="text-sm text-slate-500">Vitajte späť v administrácii BookMe.</p>
        </div>
        <span class="badge bg-emerald-500 text-white">Live</span>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card flex flex-col justify-between p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Celkom</span>
            </div>
            <div>
                <p class="text-3xl font-bold text-slate-900">{{ $stats['total_profiles'] }}</p>
                <p class="text-sm text-slate-500 font-medium">Registrovaných prevádzok</p>
            </div>
        </div>

        <div class="card flex flex-col justify-between p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="h-10 w-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Aktívne</span>
            </div>
            <div>
                <p class="text-3xl font-bold text-slate-900">{{ $stats['active_profiles'] }}</p>
                <p class="text-sm text-slate-500 font-medium">Publikovaných profilov</p>
            </div>
        </div>

        <div class="card flex flex-col justify-between p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="h-10 w-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Aktivita</span>
            </div>
            <div>
                <p class="text-3xl font-bold text-slate-900">{{ $stats['total_appointments'] }}</p>
                <p class="text-sm text-slate-500 font-medium">Všetky rezervácie</p>
            </div>
        </div>

        <div class="card flex flex-col justify-between p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="h-10 w-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Obrat</span>
            </div>
            <div>
                <p class="text-3xl font-bold text-slate-900">€{{ number_format($stats['total_revenue'], 2) }}</p>
                <p class="text-sm text-slate-500 font-medium">Potvrdené objednávky</p>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Main Section -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Latest Profiles -->
            <div class="card overflow-hidden !p-0">
                <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                    <h2 class="font-bold text-slate-900">Najnovšie prevádzky</h2>
                    <a href="{{ route('admin.profiles') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition">Zobraziť všetky</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($latest_profiles as $profile)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 font-bold uppercase">
                                    {{ substr($profile->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 leading-tight">{{ $profile->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $profile->owner?->email }} • {{ $profile->city }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-tight {{ $profile->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $profile->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Global Appointments -->
            <div class="card overflow-hidden !p-0">
                <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                    <h2 class="font-bold text-slate-900">Blížiace sa termíny</h2>
                    <a href="{{ route('admin.appointments') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition">Kalendár</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($upcoming as $appointment)
                        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:bg-slate-50/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="text-center min-w-[50px] px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700">
                                    <p class="text-[10px] font-bold uppercase leading-none">{{ $appointment->start_at?->format('M') }}</p>
                                    <p class="text-lg font-bold leading-tight">{{ $appointment->start_at?->format('d') }}</p>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 leading-tight">{{ optional($appointment->service)->name }}</p>
                                    <p class="text-xs text-slate-500">{{ optional($appointment->profile)->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="font-bold text-slate-900">{{ $appointment->start_at?->format('H:i') }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Začiatok</p>
                                </div>
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-tight {{ $appointment->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $appointment->status }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <p class="text-sm text-slate-500 italic">Zatiaľ žiadne rezervácie v systéme.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card p-6">
                <h2 class="font-bold text-slate-900 mb-4">Rýchle akcie</h2>
                <div class="grid grid-cols-1 gap-2">
                    <a href="{{ route('admin.profiles') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 transition-all group">
                        <div class="h-8 w-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-slate-400 group-hover:text-emerald-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-sm font-bold">Pridať prevádzku</span>
                    </a>
                    <a href="{{ route('admin.invoices') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 transition-all group">
                        <div class="h-8 w-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-slate-400 group-hover:text-emerald-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="text-sm font-bold">Vystaviť faktúru</span>
                    </a>
                </div>
            </div>

            <!-- Expiring Trials -->
            @if($expiring_trials->isNotEmpty())
                <div class="card p-6 border-orange-100 bg-orange-50/20">
                    <div class="flex items-center gap-3 mb-4 text-orange-700">
                        <div class="h-10 w-10 rounded-xl bg-orange-100 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="font-bold">Končiace skúšobné doby</h2>
                    </div>
                    <div class="space-y-3">
                        @foreach($expiring_trials as $profile)
                            <div class="p-4 bg-white rounded-xl border border-orange-100 shadow-sm hover:shadow-md transition-shadow">
                                <p class="font-bold text-slate-900 text-sm leading-tight mb-1">{{ $profile->name }}</p>
                                <div class="flex items-center justify-between">
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
</div>
@endsection
