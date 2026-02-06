@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-3xl text-slate-900">Platby</h1>
            <p class="text-sm text-slate-500">Prehľad finančných transakcií v systéme.</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 mb-6">
        <button onclick="switchTab('local')" id="tab-local" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all bg-slate-900 text-white shadow-lg">
            Lokálne platby
        </button>
        <button onclick="switchTab('revolut')" id="tab-revolut" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all bg-white text-slate-500 hover:bg-slate-50 border border-slate-100">
            Revolut transakcie
        </button>
    </div>

    <div id="content-local" class="card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold">
                        <th class="px-6 py-4">Prevádzka</th>
                        <th class="px-6 py-4">Rezervácia</th>
                        <th class="px-6 py-4">Stav</th>
                        <th class="px-6 py-4">Suma</th>
                        <th class="px-6 py-4">Provider</th>
                        <th class="px-6 py-4">Vytvorené</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white/50">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 text-slate-700 font-medium">{{ $payment->appointment?->profile?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-500">#{{ $payment->appointment_id }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-tight
                                    {{ $payment->status === 'succeeded' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $payment->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">€{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4 text-slate-600 uppercase text-xs font-bold">{{ $payment->provider ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-500 font-medium">{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 italic">Žiadne platby.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-slate-50">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <div id="content-revolut" class="hidden space-y-4">
        @if(!$isRevolutConfigured)
            <div class="card p-12 text-center">
                <div class="h-16 w-16 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Revolut API nie je nakonfigurované</h3>
                <p class="text-slate-500 mb-6 max-w-sm mx-auto">Pre zobrazenie transakcií priamo z vášho Revolut Business účtu musíte najprv nastaviť API kľúče.</p>
                <a href="{{ route('admin.billing.settings') }}" class="inline-flex items-center px-6 py-3 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition shadow-lg">
                    Prejsť do nastavení
                </a>
            </div>
        @else
            <div class="card overflow-hidden !p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold">
                                <th class="px-6 py-4">Popis</th>
                                <th class="px-6 py-4">Typ</th>
                                <th class="px-6 py-4">Stav</th>
                                <th class="px-6 py-4">Suma</th>
                                <th class="px-6 py-4">Dátum</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white/50">
                            @forelse($revolutTransactions as $tx)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-900">{{ $tx['reference'] ?? 'Bez referencie' }}</p>
                                        <p class="text-[10px] text-slate-400 font-mono">{{ $tx['id'] }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs text-slate-600 font-medium">{{ $tx['type'] ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-tight
                                            {{ ($tx['state'] ?? '') === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $tx['state'] ?? 'unknown' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $amount = ($tx['legs'][0]['amount'] ?? 0);
                                            $currency = ($tx['legs'][0]['currency'] ?? 'EUR');
                                            $isNegative = $amount < 0;
                                        @endphp
                                        <span class="font-bold {{ $isNegative ? 'text-rose-600' : 'text-emerald-600' }}">
                                            {{ $isNegative ? '' : '+' }}{{ number_format($amount, 2) }} {{ $currency }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 font-medium">
                                        {{ \Carbon\Carbon::parse($tx['created_at'])->format('d.m.Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 italic">Žiadne Revolut transakcie neboli nájdené.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    function switchTab(tab) {
        const localTab = document.getElementById('tab-local');
        const revolutTab = document.getElementById('tab-revolut');
        const localContent = document.getElementById('content-local');
        const revolutContent = document.getElementById('content-revolut');

        if (tab === 'local') {
            localTab.classList.add('bg-slate-900', 'text-white', 'shadow-lg');
            localTab.classList.remove('bg-white', 'text-slate-500', 'hover:bg-slate-50', 'border', 'border-slate-100');

            revolutTab.classList.remove('bg-slate-900', 'text-white', 'shadow-lg');
            revolutTab.classList.add('bg-white', 'text-slate-500', 'hover:bg-slate-50', 'border', 'border-slate-100');

            localContent.classList.remove('hidden');
            revolutContent.classList.add('hidden');
        } else {
            revolutTab.classList.add('bg-slate-900', 'text-white', 'shadow-lg');
            revolutTab.classList.remove('bg-white', 'text-slate-500', 'hover:bg-slate-50', 'border', 'border-slate-100');

            localTab.classList.remove('bg-slate-900', 'text-white', 'shadow-lg');
            localTab.classList.add('bg-white', 'text-slate-500', 'hover:bg-slate-50', 'border', 'border-slate-100');

            revolutContent.classList.remove('hidden');
            localContent.classList.add('hidden');
        }
    }
</script>
@endsection
