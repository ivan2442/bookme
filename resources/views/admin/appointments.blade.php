@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-3xl text-slate-900">Globálny zoznam rezervácií</h1>
            <p class="text-sm text-slate-500">Prehľad všetkých termínov v systéme naprieč prevádzkami.</p>
        </div>
    </div>

    <div class="card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold">
                        <th class="px-6 py-4">Služba / Klient</th>
                        <th class="px-6 py-4">Prevádzka</th>
                        <th class="px-6 py-4">Zamestnanec</th>
                        <th class="px-6 py-4">Čas</th>
                        <th class="px-6 py-4">Stav</th>
                        <th class="px-6 py-4 text-right">Cena</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white/50">
                    @forelse($appointments as $appointment)
                        @php
                            $computedPrice = ($appointment->service?->base_price ?? 0) + ($appointment->serviceVariant?->price ?? 0);
                            $displayPrice = $appointment->price ?? $computedPrice;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900">{{ $appointment->service?->name }}</p>
                                <p class="text-xs text-slate-500">{{ $appointment->customer_name }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-700">{{ $appointment->profile?->name }}</td>
                            <td class="px-6 py-4 text-slate-700">{{ $appointment->employee?->name ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900 leading-none">{{ $appointment->start_at?->format('d.m.Y') }}</p>
                                <p class="text-[11px] text-slate-500 font-medium">{{ $appointment->start_at?->format('H:i') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-tight
                                        {{ $appointment->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' :
                                           ($appointment->status === 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-600') }}">
                                        {{ $appointment->status }}
                                    </span>
                                    @if($appointment->status === 'pending')
                                        <form method="POST" action="{{ route('admin.appointments.confirm', $appointment) }}">
                                            @csrf
                                            <button class="text-[10px] font-bold text-emerald-600 hover:underline">Potvrdiť</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.appointments.delete', $appointment) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-[10px] font-bold text-rose-600 hover:underline" onclick="return confirmDelete(event, 'Odstrániť rezerváciu?')">Odstrániť</button>
                                    </form>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">€{{ number_format($displayPrice, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 italic">Zatiaľ žiadne rezervácie.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($appointments->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
