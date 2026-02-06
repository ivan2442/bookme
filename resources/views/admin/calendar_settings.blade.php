@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-display text-3xl text-slate-900">Nastavenia kalendárov</h1>
        <p class="text-sm text-slate-500">Konfigurácia pravidiel rezervácií a slotov pre jednotlivé prevádzky.</p>
    </div>

    <div class="grid md:grid-cols-2 2xl:grid-cols-3 gap-6">
        @foreach($profiles as $profile)
            <div class="card p-6 flex flex-col justify-between">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-1">{{ $profile->name }}</h2>
                    <p class="text-xs text-slate-400 uppercase font-bold tracking-widest">{{ $profile->city }}</p>
                </div>

                <form method="POST" action="{{ route('admin.calendar.settings.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="profile_id" value="{{ $profile->id }}">
                    @php $settings = $profile->calendarSetting; @endphp

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="label text-[10px]">Interval (min)</label>
                            <input type="number" name="slot_interval_minutes" class="input-control !py-2 !text-sm" value="{{ $settings->slot_interval_minutes ?? 15 }}" required>
                        </div>
                        <div class="space-y-1">
                            <label class="label text-[10px]">Min. notice (min)</label>
                            <input type="number" name="min_notice_minutes" class="input-control !py-2 !text-sm" value="{{ $settings->min_notice_minutes ?? 60 }}" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="label text-[10px]">Buffer pred (m)</label>
                            <input type="number" name="buffer_before_minutes" class="input-control !py-2 !text-sm" value="{{ $settings->buffer_before_minutes ?? 0 }}" required>
                        </div>
                        <div class="space-y-1">
                            <label class="label text-[10px]">Buffer po (m)</label>
                            <input type="number" name="buffer_after_minutes" class="input-control !py-2 !text-sm" value="{{ $settings->buffer_after_minutes ?? 0 }}" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="label text-[10px]">Max. vopred (d)</label>
                            <input type="number" name="max_advance_days" class="input-control !py-2 !text-sm" value="{{ $settings->max_advance_days ?? 90 }}" required>
                        </div>
                        <div class="space-y-1">
                            <label class="label text-[10px]">Storno limit (h)</label>
                            <input type="number" name="cancellation_limit_hours" class="input-control !py-2 !text-sm" value="{{ $settings->cancellation_limit_hours ?? 24 }}" required>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-50 flex justify-end">
                        <button class="px-4 py-2 rounded-lg bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition shadow-md">
                            Uložiť nastavenia
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
