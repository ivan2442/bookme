@extends('layouts.owner')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">{{ __('All appointments') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Complete overview of your business appointments.') }}</p>
        </div>
    </div>

    <div class="card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold">
                        <th class="px-6 py-4">{{ __('Client & Service') }}</th>
                        <th class="px-6 py-4">{{ __('Employee') }}</th>
                        <th class="px-6 py-4">{{ __('Time') }}</th>
                        <th class="px-6 py-4">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Price') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white/50">
                    @forelse($appointments as $appointment)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900 leading-tight">{{ $appointment->customer_name }}</p>
                                <p class="text-[11px] text-slate-500 font-medium">
                                    {{ $appointment->metadata['service_name_manual'] ?? ($appointment->service?->name ?? __('Manual service')) }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-slate-700 font-medium">{{ $appointment->employee?->name ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900 leading-none">{{ $appointment->start_at?->format('d.m.Y') }}</p>
                                <p class="text-[11px] text-slate-500 font-medium mt-1">{{ $appointment->start_at?->format('H:i') }} - {{ $appointment->end_at?->format('H:i') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-tight
                                        {{ $appointment->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' :
                                           ($appointment->status === 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-600') }}">
                                        @if($appointment->status === 'confirmed') {{ __('Confirmed') }} @elseif($appointment->status === 'pending') {{ __('Pending') }} @else {{ $appointment->status }} @endif
                                    </span>
                                    @if($appointment->status === 'pending')
                                        <form method="POST" action="{{ route('owner.appointments.confirm', $appointment) }}">
                                            @csrf
                                            <button class="text-[10px] font-bold text-emerald-600 hover:underline">{{ __('Confirm') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">
                                €{{ number_format($appointment->price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('owner.appointments.delete', $appointment) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-slate-100 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition shadow-sm" title="{{ __('Delete') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 italic">{{ __('No appointments yet.') }}</td>
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
