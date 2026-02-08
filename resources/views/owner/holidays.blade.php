@extends('layouts.owner')

@section('content')
<div class="space-y-6" x-data="bulkActions()">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">Sviatky a uzávierky</h1>
            <p class="text-sm text-slate-500">Správa voľných dní a časových blokácií vašej prevádzky.</p>
        </div>
        <button onclick="openAddHolidayModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Pridať blokáciu</span>
        </button>
    </div>

    <!-- Bulk Actions Toolbar -->
    <div x-show="selected.length > 0"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-indigo-600 text-white px-6 py-3 rounded-2xl shadow-xl shadow-indigo-200 flex items-center justify-between gap-4 sticky top-4 z-50">
        <div class="flex items-center gap-3">
            <span class="text-sm font-bold uppercase tracking-wider">{{ __('Selected') }} (<span x-text="selected.length"></span>)</span>
        </div>

        <div class="flex items-center gap-2">
            <button @click="submitBulk('delete')" class="px-4 py-2 bg-rose-500 hover:bg-rose-400 rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-rose-900/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                {{ __('Remove blockages') }}
            </button>
        </div>
    </div>

    <form id="bulkForm" x-ref="bulkForm" action="{{ route('owner.holidays.bulkDelete') }}" method="POST" class="hidden">
        @csrf
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
                                <span class="cursor-pointer select-none hidden sm:inline text-slate-500 hover:text-indigo-600 transition-colors font-semibold" @click="toggleAll">{{ __('Select all') }}</span>
                            </div>
                        </th>
                        <th class="px-6 py-4">{{ __('Date') }}</th>
                        <th class="px-6 py-4">{{ __('Time') }}</th>
                        <th class="px-6 py-4">{{ __('Employee') }}</th>
                        <th class="px-6 py-4">{{ __('Business profile') }}</th>
                        <th class="px-6 py-4">{{ __('Reason') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white/50">
                    @forelse($holidays as $holiday)
                        <tr class="hover:bg-slate-50/50 transition-colors"
                            id="holiday-row-{{ $holiday->id }}"
                            :class="selected.includes('{{ $holiday->id }}') ? 'bg-indigo-50/30' : ''">
                            <td class="px-6 py-4">
                                <input type="checkbox" value="{{ $holiday->id }}" x-model="selected" class="w-4 h-4 text-indigo-600 bg-white border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900 leading-tight">{{ $holiday->date->format('d.m.Y') }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($holiday->start_time && $holiday->end_time)
                                    <span class="font-medium">{{ substr($holiday->start_time, 0, 5) }} - {{ substr($holiday->end_time, 0, 5) }}</span>
                                @else
                                    <span class="text-[10px] uppercase font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">{{ __('all day') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-slate-700 font-medium">{{ $holiday->employee->name ?? __('Whole business') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-slate-500 text-xs">{{ $holiday->profile->name }}</span>
                            </td>
                            <td class="px-6 py-4 italic text-slate-500">
                                {{ $holiday->reason ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button onclick="toggleEdit({{ $holiday->id }})" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition shadow-sm" title="{{ __('Edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <form action="{{ route('owner.holidays.delete', $holiday) }}" method="POST" onsubmit="confirmDelete(event, '{{ __('Delete this blockage?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-slate-100 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition shadow-sm" title="{{ __('Delete') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <tr class="hidden bg-emerald-50/20" id="holiday-edit-{{ $holiday->id }}">
                            <td colspan="7" class="px-6 py-6">
                                <form method="POST" action="{{ route('owner.holidays.update', $holiday) }}" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="profile_id" value="{{ $holiday->profile_id }}">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="text-[10px] uppercase font-bold text-slate-500 mb-1 block">{{ __('Employee') }}</label>
                                            <div class="nice-select-wrapper">
                                                <select name="employee_id" class="nice-select">
                                                    <option value="">{{ __('Whole business') }}</option>
                                                    @foreach($profiles->where('id', $holiday->profile_id) as $p)
                                                        @foreach($p->employees as $e)
                                                            <option value="{{ $e->id }}" @selected($holiday->employee_id == $e->id)>{{ $e->name }}</option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-[10px] uppercase font-bold text-slate-500 mb-1 block">{{ __('Date') }}</label>
                                            <input type="date" name="date" value="{{ $holiday->date->format('Y-m-d') }}" class="input-control !py-1.5 !text-sm" required data-allow-past>
                                        </div>
                                        <div>
                                            <label class="text-[10px] uppercase font-bold text-slate-500 mb-1 block">{{ __('From') }}</label>
                                            <input type="text" name="start_time" value="{{ $holiday->start_time ? substr($holiday->start_time, 0, 5) : '' }}" class="input-control !py-1.5 !text-sm js-flatpickr-time" placeholder="{{ __('all day') }}">
                                        </div>
                                        <div>
                                            <label class="text-[10px] uppercase font-bold text-slate-500 mb-1 block">{{ __('To') }}</label>
                                            <input type="text" name="end_time" value="{{ $holiday->end_time ? substr($holiday->end_time, 0, 5) : '' }}" class="input-control !py-1.5 !text-sm js-flatpickr-time" placeholder="{{ __('all day') }}">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-[1fr,auto] gap-4 items-end">
                                        <div>
                                            <label class="text-[10px] uppercase font-bold text-slate-500 mb-1 block">{{ __('Reason') }}</label>
                                            <input type="text" name="reason" value="{{ $holiday->reason }}" class="input-control !py-1.5 !text-sm" placeholder="{{ __('Reason') }}">
                                        </div>
                                        <div class="flex items-center gap-6 pb-1">
                                            <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                                                <input type="checkbox" name="is_closed" value="1" @checked($holiday->is_closed) class="h-4 w-4 text-emerald-600 rounded border-slate-300">
                                                {{ __('Closed') }}
                                            </label>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="toggleEdit({{ $holiday->id }})" class="px-3 py-1.5 text-xs font-bold text-slate-400 hover:text-slate-600 transition">{{ __('Cancel') }}</button>
                                                <button type="submit" class="px-4 py-1.5 rounded-lg bg-emerald-500 text-white text-xs font-bold shadow-md hover:bg-emerald-600 transition">{{ __('Save') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 italic">{{ __('No holidays or blockages set yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($holidays->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $holidays->links() }}
            </div>
        @endif
    </div>

    <div class="card p-6 bg-slate-50/50 border-none shadow-none mt-6">
        <h3 class="font-bold text-slate-900 mb-2 flex items-center gap-2 text-sm uppercase tracking-wider">
            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ __('Information') }}
        </h3>
        <p class="text-xs text-slate-500 leading-relaxed">
            {{ __('Holidays and closures are used for one-time blocking of slots (e.g., vacation, public holiday, or unexpected event). You can block the entire business or just a specific employee.') }}
        </p>
    </div>
</div>

<!-- Add Holiday Modal -->
<div id="addHolidayModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeAddHolidayModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-display font-semibold text-slate-900">{{ __('Add holiday / blockage') }}</h3>
                <button onclick="closeAddHolidayModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('owner.holidays.store') }}" class="space-y-4">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">{{ __('Business profile') }}</label>
                        <div class="nice-select-wrapper">
                            <select name="profile_id" class="nice-select" required>
                                @foreach($profiles as $profile)
                                    <option value="{{ $profile->id }}" @selected(old('profile_id') == $profile->id)>{{ $profile->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">{{ __('Employee (optional)') }}</label>
                        <div class="nice-select-wrapper">
                            <select name="employee_id" class="nice-select">
                                <option value="">{{ __('Whole business') }}</option>
                                @foreach($profiles as $profile)
                                    @foreach($profile->employees as $employee)
                                        <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->name }} — {{ $profile->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid sm:grid-cols-3 gap-4 border-t border-slate-50 pt-4">
                    <div>
                        <label class="label">{{ __('Date / Range') }}</label>
                        <input type="text" name="date" id="add_holiday_date" class="input-control" value="{{ old('date') }}" required readonly placeholder="{{ __('Select date or range') }}" data-allow-past data-mode="range">
                    </div>
                    <div>
                        <label class="label">{{ __('From') }}</label>
                        <input type="text" name="start_time" class="input-control js-flatpickr-time" value="{{ old('start_time') }}" placeholder="{{ __('all day') }}">
                    </div>
                    <div>
                        <label class="label">{{ __('To') }}</label>
                        <input type="text" name="end_time" class="input-control js-flatpickr-time" value="{{ old('end_time') }}" placeholder="{{ __('all day') }}">
                    </div>
                </div>

                <div class="grid sm:grid-cols-[1fr,200px] gap-4">
                    <div>
                        <label class="label">{{ __('Reason') }}</label>
                        <input type="text" name="reason" class="input-control" value="{{ old('reason') }}" placeholder="{{ __('Reason (holiday, vacation)') }}">
                    </div>
                    <div class="flex items-end pb-3">
                        <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                            <input type="checkbox" name="is_closed" value="1" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 rounded border-slate-300" @checked(old('is_closed', '1') == '1')>
                            {{ __('Closed whole day') }}
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-50">
                    <button type="button" onclick="closeAddHolidayModal()" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-200/50">{{ __('Add blockage') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddHolidayModal() {
        document.getElementById('addHolidayModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        if (window.reinitFlatpickr) window.reinitFlatpickr();
    }

    function closeAddHolidayModal() {
        document.getElementById('addHolidayModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function toggleEdit(id) {
        const row = document.getElementById(`holiday-row-${id}`);
        const edit = document.getElementById(`holiday-edit-${id}`);
        edit.classList.toggle('hidden');
        if (!edit.classList.contains('hidden')) {
            if (window.reinitFlatpickr) window.reinitFlatpickr();
        }
    }

    function bulkActions() {
        return {
            selected: [],
            allIds: @json($holidays->pluck('id')).map(id => id.toString()),

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
                let message = '{{ __("Are you sure you want to perform this action on selected blockages?") }}';

                if (action === 'delete') {
                    message = '{{ __("Are you sure you want to delete selected blockages?") }}';
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
                        const container = this.$refs.idsContainer;

                        container.innerHTML = '';

                        this.selected.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
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
