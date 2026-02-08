@extends('layouts.owner')

@section('content')
<div class="space-y-6" x-data="bulkActions()">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">{{ __('All appointments') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Complete overview of your business appointments.') }}</p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <form action="{{ route('owner.appointments') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 group">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('Search appointments...') }}"
                           class="w-full sm:w-64 pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none group-hover:border-slate-300">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <select name="per_page" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-xl text-sm py-2 px-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all cursor-pointer">
                    <option value="30" {{ ($perPage ?? 30) == 30 ? 'selected' : '' }}>30 {{ __('per page') }}</option>
                    <option value="50" {{ ($perPage ?? 30) == 50 ? 'selected' : '' }}>50 {{ __('per page') }}</option>
                    <option value="100" {{ ($perPage ?? 30) == 100 ? 'selected' : '' }}>100 {{ __('per page') }}</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Bulk Actions Toolbar -->
    <div x-show="selected.length > 0"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-indigo-600 text-white px-6 py-3 rounded-2xl shadow-xl shadow-indigo-200 flex items-center justify-between gap-4 sticky top-4 z-20">
        <div class="flex items-center gap-3">
            <span class="text-sm font-bold uppercase tracking-wider">{{ __('Selected') }} (<span x-text="selected.length"></span>)</span>
        </div>

        <div class="flex items-center gap-2">
            <button @click="submitBulk('confirm')" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ __('Confirm') }}
            </button>
            <button @click="submitBulk('complete')" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ __('Done') }}
            </button>
            <button @click="submitBulk('cancel')" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ __('Refuse') }}
            </button>
            <button @click="submitBulk('delete')" class="px-3 py-1.5 bg-rose-500 hover:bg-rose-400 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                {{ __('Delete') }}
            </button>
        </div>
    </div>

    <form id="bulkForm" x-ref="bulkForm" action="{{ route('owner.appointments.bulk') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="action" id="bulk_action_field">
        <div x-ref="idsContainer"></div>
    </form>

    <div class="card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold border-b border-slate-100">
                        <th class="w-12 px-6 py-4">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" @click="toggleAll" :checked="allSelected" class="w-4 h-4 text-indigo-600 bg-white border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                                <span class="cursor-pointer select-none hidden sm:inline text-indigo-100 hover:text-white transition-colors" @click="toggleAll">{{ __('Select all') }}</span>
                            </div>
                        </th>
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
                        <tr class="hover:bg-slate-50/50 transition-colors" :class="selected.includes('{{ $appointment->id }}') ? 'bg-indigo-50/30' : ''">
                            <td class="px-6 py-4">
                                <input type="checkbox" value="{{ $appointment->id }}" x-model="selected" class="w-4 h-4 text-indigo-600 bg-white border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                            </td>
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
                                <form method="POST" action="{{ route('owner.appointments.delete', $appointment) }}" onsubmit="confirmDeleteAppointment(event, this)">
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

<script>
    function bulkActions() {
        return {
            selected: [],
            allIds: @json($appointments->pluck('id')).map(id => id.toString()),

            toggleAll() {
                if (this.allSelected) {
                    this.selected = [];
                } else {
                    this.selected = [...this.allIds];
                }
            },

            get allSelected() {
                return this.allIds.length > 0 && this.selected.length === this.allIds.length;
            },

            submitBulk(action) {
                let message = '{{ __("Are you sure you want to perform this action on selected appointments?") }}';

                if (action === 'delete') {
                    message = '{{ __("Are you sure you want to delete selected appointments?") }}';
                }

                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: message,
                    icon: action === 'delete' ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonColor: action === 'delete' ? '#ef4444' : '#4f46e5',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: action === 'delete' ? '{{ __("Yes, delete them!") }}' : '{{ __("Yes, do it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = this.$refs.bulkForm;
                        const actionField = document.getElementById('bulk_action_field');
                        const container = this.$refs.idsContainer;

                        actionField.value = action;
                        container.innerHTML = '';

                        this.selected.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'appointment_ids[]';
                            input.value = id;
                            container.appendChild(input);
                        });

                        form.submit();
                    }
                });
            }
        }
    }
</script>
@endsection
