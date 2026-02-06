@extends('layouts.owner')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">Tržby a výkon</h1>
            <p class="text-sm text-slate-500">Prehľad finančnej výkonnosti vašej prevádzky.</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="hidden sm:flex px-4 py-2 rounded-xl bg-white text-slate-700 text-xs font-bold uppercase tracking-wider items-center shadow-sm border border-slate-100">
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

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card flex flex-col justify-between p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="h-10 w-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Vybavené</span>
            </div>
            <div>
                <p class="text-3xl font-bold text-slate-900">{{ number_format($stats['count']) }}</p>
                <p class="text-sm text-slate-500 font-medium">Rezervácií ({{ $monthsSlovak[$selectedMonth] }})</p>
            </div>
        </div>

        <div class="card flex flex-col justify-between p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Obrat</span>
            </div>
            <div>
                <p class="text-3xl font-bold text-slate-900">€{{ number_format($stats['revenue'], 2) }}</p>
                <p class="text-sm text-slate-500 font-medium">Celková tržba</p>
            </div>
        </div>

        <div class="card flex flex-col justify-between p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="h-10 w-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Výkon</span>
            </div>
            <div>
                <p class="text-3xl font-bold text-slate-900">{{ number_format($stats['hours'], 1) }}h</p>
                <p class="text-sm text-slate-500 font-medium">Odpracované hodiny</p>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card p-6">
        <h3 class="font-bold text-slate-900 mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            Vývoj tržieb za posledných 12 mesiacov
        </h3>
        <div class="h-64 flex items-end justify-between gap-2 px-2 pt-4">
            @php
                $maxRevenue = collect($chartData)->max('revenue') ?: 100;
            @endphp
            @foreach($chartData as $data)
                <div class="flex-1 flex flex-col items-center group relative h-full">
                    <div class="w-full bg-slate-50 rounded-t-xl transition-all duration-300 group-hover:bg-emerald-50 flex items-end justify-center h-full">
                        <div class="w-4/5 bg-emerald-400 rounded-t-xl transition-all duration-500 group-hover:bg-emerald-500 relative shadow-sm" style="height: {{ ($data['revenue'] / $maxRevenue) * 100 }}%">
                             <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] py-1 px-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10 shadow-xl">
                                {{ $data['full_label'] }}: €{{ number_format($data['revenue'], 2) }}
                             </div>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-tighter">{{ $data['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- History Table -->
    <div class="card overflow-hidden !p-0">
        <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50">
            <h3 class="font-bold text-slate-900">História vybavených rezervácií</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-widest font-bold">
                        <th class="px-6 py-4">Dátum a čas</th>
                        <th class="px-6 py-4">Zákazník</th>
                        <th class="px-6 py-4">Služba</th>
                        <th class="px-6 py-4">Zamestnanec</th>
                        <th class="px-6 py-4 text-right">Suma</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white/50">
                    @forelse($latestPayments as $appointment)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900 leading-none">{{ $appointment->start_at->format('d.m.Y') }}</p>
                                <p class="text-[11px] text-emerald-600 font-bold mt-1">{{ $appointment->start_at->format('H:i') }} - {{ $appointment->end_at->format('H:i') }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium">
                                {{ $appointment->customer_name }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-slate-900">{{ $appointment->metadata['service_name_manual'] ?? ($appointment->service?->name ?? 'Manuálna služba') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold uppercase tracking-tight">
                                    {{ optional($appointment->employee)->name ?? 'Bez zamestnanca' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">
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
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $latestPayments->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    $(document).ready(function() {
        if ($.fn.niceSelect) {
            $('.nice-select').niceSelect();
        }
    });
</script>
@endsection
