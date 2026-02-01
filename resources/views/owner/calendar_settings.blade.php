@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Nastavenia kalendára</h1>
        </div>
        <span class="badge">Konfigurácia</span>
    </div>

    @include('owner.partials.nav')

    @if(session('status'))
        <div class="card border-emerald-200 bg-emerald-50 text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="grid md:grid-cols-2 gap-4">
        @foreach($profiles as $profile)
            <div class="card space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h2 class="font-bold text-lg text-slate-900">{{ $profile->name }}</h2>
                    <span class="text-[10px] uppercase font-bold text-slate-400">ID: {{ $profile->id }}</span>
                </div>

                <form method="POST" action="{{ route('owner.calendar.settings.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="profile_id" value="{{ $profile->id }}">
                    @php $settings = $profile->calendarSetting; @endphp

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="label">Interval slotov (min)</label>
                            <select name="slot_interval_minutes" class="input-control" required>
                                @foreach([15, 20, 30, 45, 60] as $min)
                                    <option value="{{ $min }}" @selected(old('slot_interval_minutes', $settings->slot_interval_minutes ?? 15) == $min)>{{ $min }} min</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="label">Min. čas pred (min)</label>
                            <input type="number" name="min_notice_minutes" class="input-control" min="0" value="{{ old('min_notice_minutes', $settings->min_notice_minutes ?? 60) }}" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="label">Buffer PRED (min)</label>
                            <input type="number" name="buffer_before_minutes" class="input-control" min="0" value="{{ old('buffer_before_minutes', $settings->buffer_before_minutes ?? 0) }}" required>
                        </div>
                        <div class="space-y-1">
                            <label class="label">Buffer PO (min)</label>
                            <input type="number" name="buffer_after_minutes" class="input-control" min="0" value="{{ old('buffer_after_minutes', $settings->buffer_after_minutes ?? 0) }}" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="label">Max. dopredu (dni)</label>
                            <input type="number" name="max_advance_days" class="input-control" min="1" value="{{ old('max_advance_days', $settings->max_advance_days ?? 90) }}" required>
                        </div>
                        <div class="space-y-1">
                            <label class="label">Limit storna (h)</label>
                            <input type="number" name="cancellation_limit_hours" class="input-control" min="0" value="{{ old('cancellation_limit_hours', $settings->cancellation_limit_hours ?? 24) }}" required>
                        </div>
                    </div>

                    <button class="w-full px-4 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition">
                        Uložiť nastavenia
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</section>
@endsection
