@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Zoznam rezervácií</h1>
        </div>
        <span class="badge">Správa termínov</span>
    </div>

    @include('owner.partials.nav')

    @if(session('status'))
        <div class="card border-emerald-200 bg-emerald-50 text-emerald-900 mb-4">
            {{ session('status') }}
        </div>
    @endif

    <div class="card overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 uppercase tracking-widest text-xs">
                    <th class="py-2 px-4">Klient & Služba</th>
                    <th class="py-2 px-4">Prevádzka</th>
                    <th class="py-2 px-4">Zamestnanec</th>
                    <th class="py-2 px-4">Čas</th>
                    <th class="py-2 px-4">Stav</th>
                    <th class="py-2 px-4 text-right">Cena</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($appointments as $appointment)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3 px-4">
                            <p class="font-bold text-slate-900">{{ $appointment->customer_name }}</p>
                            <p class="text-xs text-slate-500">{{ $appointment->service?->name }}</p>
                        </td>
                        <td class="py-3 px-4 text-slate-700">{{ $appointment->profile?->name }}</td>
                        <td class="py-3 px-4 text-slate-700">{{ $appointment->employee?->name ?? '—' }}</td>
                        <td class="py-3 px-4">
                            <p class="font-semibold text-slate-900">{{ $appointment->start_at?->format('d.m.Y') }}</p>
                            <p class="text-xs text-slate-500">{{ $appointment->start_at?->format('H:i') }}</p>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex flex-col gap-1">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $appointment->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $appointment->status }}
                                </span>
                                @if($appointment->status === 'pending')
                                    <form method="POST" action="{{ route('owner.appointments.confirm', $appointment) }}">
                                        @csrf
                                        <button class="text-[10px] font-bold text-emerald-600 hover:underline">Potvrdiť</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('owner.appointments.delete', $appointment) }}" onsubmit="return confirmDelete(event, 'Naozaj odstrániť?')">
                                    @csrf @method('DELETE')
                                    <button class="text-[10px] font-bold text-red-500 hover:underline">Odstrániť</button>
                                </form>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-slate-900">
                            €{{ number_format($appointment->price, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500 italic">Zatiaľ žiadne rezervácie.</td>
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
