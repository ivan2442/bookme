@extends('layouts.owner')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">{{ __('System invoices') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Overview of invoices for using the BookMe reservation system.') }}</p>
        </div>
    </div>

    <div class="card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-widest font-bold">
                        <th class="px-6 py-4">{{ __('Invoice number') }}</th>
                        <th class="px-6 py-4">{{ __('Business') }}</th>
                        <th class="px-6 py-4">{{ __('Amount') }}</th>
                        <th class="px-6 py-4">{{ __('Due date') }}</th>
                        <th class="px-6 py-4">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white/50">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-slate-900 font-bold">{{ $invoice->invoice_number }}</td>
                            <td class="px-6 py-4 text-slate-600 font-medium">{{ $invoice->profile->name }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">€{{ number_format($invoice->amount, 2) }}</td>
                            <td class="px-6 py-4 {{ $invoice->due_at->isPast() && $invoice->status === 'unpaid' ? 'text-rose-600 font-bold' : 'text-slate-500' }}">
                                {{ $invoice->due_at->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-tight
                                    {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($invoice->status === 'unpaid' ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-600') }}">
                                    {{ $invoice->status === 'paid' ? __('Paid') : ($invoice->status === 'unpaid' ? __('Unpaid') : $invoice->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('owner.invoices.preview', $invoice) }}" target="_blank" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition shadow-sm" title="{{ __('View invoice') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 italic">
                                {{ __('No invoices found for your businesses.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
