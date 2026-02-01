@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Admin</p>
            <h1 class="font-display text-3xl text-slate-900">Globálny zoznam rezervácií</h1>
        </div>
        <span class="badge">Systém</span>
    </div>

    @include('admin.partials.nav')

    <div class="card">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 uppercase tracking-widest text-xs">
                    <th class="py-2">Služba</th>
                    <th class="py-2">Prevádzka</th>
                    <th class="py-2">Zamestnanec</th>
                    <th class="py-2">Čas</th>
                    <th class="py-2">Stav</th>
                    <th class="py-2 text-right">Cena</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($appointments as $appointment)
                    @php
                        $computedPrice = ($appointment->service?->base_price ?? 0) + ($appointment->serviceVariant?->price ?? 0);
                        $displayPrice = $appointment->price ?? $computedPrice;
                    @endphp
                    <tr>
                        <td class="py-3">
                            <p class="font-semibold text-slate-900">{{ $appointment->service?->name }}</p>
                            <p class="text-xs text-slate-500">{{ $appointment->customer_name }}</p>
                        </td>
                        <td class="py-3 text-slate-700">{{ $appointment->profile?->name }}</td>
                        <td class="py-3 text-slate-700">{{ $appointment->employee?->name ?? '—' }}</td>
                        <td class="py-3 text-slate-700">{{ $appointment->start_at?->format('d.m. H:i') }}</td>
                        <td class="py-3 space-y-1">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">{{ $appointment->status }}</span>
                            @if($appointment->status === 'pending')
                                <form method="POST" action="{{ route('admin.appointments.confirm', $appointment) }}" class="mt-1">
                                    @csrf
                                    <button class="text-xs text-emerald-700 hover:text-emerald-900">Potvrdiť</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.appointments.delete', $appointment) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-600 hover:text-red-800" onclick="return confirmDelete(event, 'Odstrániť rezerváciu?')">Odstrániť</button>
                            </form>
                        </td>
                        <td class="py-3 text-right font-semibold text-slate-900">€{{ number_format($displayPrice, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-slate-500">Zatiaľ žiadne rezervácie.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $appointments->links() }}
        </div>
    </div>
</section>
@endsection
