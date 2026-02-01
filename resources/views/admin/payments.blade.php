@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Admin</p>
            <h1 class="font-display text-3xl text-slate-900">Platby</h1>
        </div>
        <span class="badge">Payments</span>
    </div>

    @include('admin.partials.nav')

    <div class="card">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 uppercase tracking-widest text-xs">
                    <th class="py-2">Prevádzka</th>
                    <th class="py-2">Rezervácia</th>
                    <th class="py-2">Stav</th>
                    <th class="py-2">Suma</th>
                    <th class="py-2">Provider</th>
                    <th class="py-2">Vytvorené</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($payments as $payment)
                    <tr>
                        <td class="py-3 text-slate-700">{{ $payment->appointment?->profile?->name ?? '—' }}</td>
                        <td class="py-3 text-slate-700">#{{ $payment->appointment_id }}</td>
                        <td class="py-3"><span class="px-2 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">{{ $payment->status }}</span></td>
                        <td class="py-3 font-semibold text-slate-900">€{{ number_format($payment->amount, 2) }}</td>
                        <td class="py-3 text-slate-700">{{ $payment->provider ?? '—' }}</td>
                        <td class="py-3 text-slate-700">{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-slate-500">Žiadne platby.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    </div>
</section>
@endsection
