@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div>
        <p class="text-xs uppercase tracking-widest text-slate-500">Moja prevádzka</p>
        <h1 class="font-display text-3xl text-slate-900">Faktúry za služby</h1>
    </div>

    @include('owner.partials.nav')

    @if(session('status'))
        <div class="card border-emerald-200 bg-emerald-50 text-emerald-800 p-4 mb-4 rounded-xl">
            {{ session('status') }}
        </div>
    @endif

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-widest font-bold">
                        <th class="px-6 py-4">Číslo faktúry</th>
                        <th class="px-6 py-4">Prevádzka</th>
                        <th class="px-6 py-4">Suma</th>
                        <th class="px-6 py-4">Splatnosť</th>
                        <th class="px-6 py-4">Stav</th>
                        <th class="px-6 py-4 text-right">Akcie</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-sm text-slate-900">{{ $invoice->invoice_number }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $invoice->profile->name }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">€{{ number_format($invoice->amount, 2) }}</td>
                            <td class="px-6 py-4 text-sm {{ $invoice->due_at->isPast() && $invoice->status === 'unpaid' ? 'text-rose-600 font-bold' : 'text-slate-500' }}">
                                {{ $invoice->due_at->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase
                                    {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($invoice->status === 'unpaid' ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-600') }}">
                                    {{ $invoice->status === 'paid' ? 'Uhradená' : ($invoice->status === 'unpaid' ? 'Neuhradená' : $invoice->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('owner.invoices.preview', $invoice) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-emerald-500 hover:text-white transition flex items-center gap-2 text-xs font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Zobraziť faktúru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 italic">
                                Žiadne faktúry neboli nájdené pre Vaše prevádzky.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
