@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3 mb-6">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Prehľad platieb a výkonu</h1>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden sm:flex px-4 py-2 rounded-xl bg-emerald-100 text-emerald-700 text-sm font-bold uppercase tracking-wider items-center shadow-sm whitespace-nowrap">
                {{ $monthsSlovak[$selectedMonth] }} {{ $selectedYear }}
            </span>
            <form action="{{ route('owner.payments') }}" method="GET" class="flex gap-2 items-center" id="filter-form">
                <div class="nice-select-wrapper">
                    <select name="month" class="nice-select" onchange="this.form.submit()">
                        @foreach($monthsSlovak as $num => $name)
                            <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="nice-select-wrapper">
                    <select name="year" class="nice-select" onchange="this.form.submit()">
                        @php
                            $startYear = now()->year - 2;
                            $endYear = now()->year;
                        @endphp
                        @foreach(range($endYear, $startYear) as $y)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @include('owner.partials.nav')

    <!-- Štatistiky aktuálneho mesiaca -->
    <div class="grid md:grid-cols-3 gap-6">
        <div class="card p-6 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-slate-500">Vybavené rezervácie</p>
                <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900">{{ number_format($stats['count']) }}</p>
            <p class="text-xs text-slate-400 mt-1">Počet ukončených termínov ({{ $selectedDate->translatedFormat('F') }})</p>
        </div>

        <div class="card p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-slate-500">Suma zarobených peňazí</p>
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3 1.343 3-3-1.343-3-3-3zM12 8V7m0 1v1m0 5v1m0-1c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM12 5a7 7 0 100 14 7 7 0 000-14z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900">€{{ number_format($stats['revenue'], 2) }}</p>
            <p class="text-xs text-slate-400 mt-1">Celkový obrat za {{ $selectedDate->translatedFormat('F') }}</p>
        </div>

        <div class="card p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-slate-500">Odpracované hodiny</p>
                <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900">{{ number_format($stats['hours'], 1) }}h</p>
            <p class="text-xs text-slate-400 mt-1">Služby v {{ $selectedDate->translatedFormat('F') }}</p>
        </div>
    </div>

    <!-- Ročný graf -->
    <div class="card p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            Vývoj tržieb za posledných 12 mesiacov
        </h3>
    <div class="h-64 flex items-end justify-between gap-2 px-2 pt-4">
        @php
            $maxRevenue = collect($chartData)->max('revenue') ?: 100;
        @endphp
        @foreach($chartData as $data)
            <div class="flex-1 flex flex-col items-center group relative h-full">
                <div class="w-full bg-slate-50 rounded-t-lg transition-all duration-300 group-hover:bg-emerald-100 flex items-end justify-center h-full">
                    <div class="w-4/5 bg-emerald-400 rounded-t-lg transition-all duration-500 group-hover:bg-emerald-500 relative" style="height: {{ ($data['revenue'] / $maxRevenue) * 100 }}%">
                         <!-- Tooltip -->
                         <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                            {{ $data['full_label'] }}: €{{ number_format($data['revenue'], 2) }}
                         </div>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-tighter">{{ $data['label'] }}</span>
            </div>
        @endforeach
    </div>
    </div>

    <!-- Zoznam vybavených rezervácií -->
    <div class="card overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">História vybavených rezervácií</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-widest font-bold">
                        <th class="px-6 py-4">Dátum a čas</th>
                        <th class="px-6 py-4">Trvanie</th>
                        <th class="px-6 py-4">Zákazník</th>
                        <th class="px-6 py-4">Služba</th>
                        <th class="px-6 py-4">Zamestnanec</th>
                        <th class="px-6 py-4 text-right">Suma</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($latestPayments as $appointment)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-900">{{ $appointment->start_at->format('d.m.Y') }}</div>
                                <div class="text-xs text-emerald-600 font-semibold">{{ $appointment->start_at->format('H:i') }} - {{ $appointment->end_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                @php
                                    $diff = $appointment->start_at->diffInMinutes($appointment->end_at);
                                @endphp
                                {{ $diff }} min
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $appointment->customer_name }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-slate-900">{{ $appointment->metadata['service_name_manual'] ?? ($appointment->service?->name ?? 'Manuálna služba') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold uppercase tracking-tight">
                                    {{ optional($appointment->employee)->name ?? 'Bez zamestnanca' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-slate-900">
                                €{{ number_format($appointment->price, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 italic">
                                Žiadne vybavené rezervácie neboli nájdené.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($latestPayments->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $latestPayments->links() }}
            </div>
        @endif
    </div>
    <div class="card p-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
            <div>
                <h3 class="text-xl font-display font-semibold mb-1 text-slate-900">Vyhodnotenie za {{ $monthsSlovak[$selectedMonth] }} {{ $selectedYear }}</h3>
                <p class="text-slate-500 text-sm">Celkový prehľad výkonu vo vybranom období</p>
            </div>
            <div class="flex flex-wrap justify-center gap-8 md:gap-12">
                <div>
                    <p class="text-xs uppercase tracking-widest text-black mb-1">Rezervácií</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['count']) }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-widest text-black mb-1">Odpracovaných hodín</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['hours'], 1) }}h</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-widest text-emerald-600 mb-1">Celkom tržba</p>
                    <p class="text-2xl font-bold text-emerald-500">€{{ number_format($stats['revenue'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .nice-select {
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        height: 42px;
        line-height: 40px;
        padding-left: 1rem;
        padding-right: 2.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #0f172a;
        background-color: #ffffff;
    }
    .nice-select:after {
        border-bottom: 2px solid #64748b;
        border-right: 2px solid #64748b;
        height: 6px;
        width: 6px;
        right: 15px;
    }
    .nice-select .list {
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        border: 1px solid #f1f5f9;
        margin-top: 4px;
    }
    .nice-select .option {
        line-height: 2.5rem;
        min-height: 2.5rem;
        font-size: 0.875rem;
    }
    .nice-select .option.selected {
        font-weight: 700;
        color: #10b981;
    }
    .nice-select .option:hover, .nice-select .option.focus, .nice-select .option.selected.focus {
        background-color: #f0fdf4;
    }
</style>

<script>
    $(document).ready(function() {
        $('.nice-select').niceSelect();
    });
</script>
@endsection
