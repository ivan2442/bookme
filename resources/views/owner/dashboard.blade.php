@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3 mb-6">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Dashboard prevádzky</h1>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button class="admin-tab">Odhlásiť</button>
            </form>
        </div>
    </div>

    @include('owner.partials.nav')

    <div class="grid md:grid-cols-3 gap-4">
        <div class="card">
            <p class="text-sm text-slate-500 mb-1">Rezervácie dnes</p>
            <p class="text-3xl font-semibold text-slate-900">{{ $stats['appointments_today'] }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-slate-500 mb-1">Rezervácie tento mesiac</p>
            <p class="text-3xl font-semibold text-slate-900">{{ $stats['appointments_month'] }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-slate-500 mb-1">Tržby tento mesiac</p>
            <p class="text-3xl font-semibold text-slate-900">€{{ number_format($stats['revenue_month'], 2) }}</p>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-lg text-slate-900">Najbližšie termíny</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($upcoming as $appointment)
                <div class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="font-semibold text-slate-900">{{ optional($appointment->service)->name }}</p>
                        <p class="text-sm text-slate-600">{{ optional($appointment->employee)->name ?? 'Bez zamestnanca' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-slate-900">{{ $appointment->start_at?->format('d.m. H:i') }}</p>
                        <p class="text-sm text-slate-600">{{ ucfirst($appointment->status) }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">Žiadne nadchádzajúce rezervácie.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
