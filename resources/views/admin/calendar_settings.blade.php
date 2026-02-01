@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Admin</p>
            <h1 class="font-display text-3xl text-slate-900">Kalendár – nastavenia slotov</h1>
        </div>
        <span class="badge">Kalendár</span>
    </div>

    @include('admin.partials.nav')

    @if(session('status'))
        <div class="card border-emerald-200 bg-emerald-50 text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="grid md:grid-cols-2 gap-4">
        @foreach($profiles as $profile)
            <div class="card space-y-3">
                <h2 class="font-semibold text-lg text-slate-900">{{ $profile->name }}</h2>
                <form method="POST" action="{{ route('admin.calendar.settings.store') }}" class="space-y-2">
                    @csrf
                    <input type="hidden" name="profile_id" value="{{ $profile->id }}">
                    @php $settings = $profile->calendarSetting; @endphp
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="label">Dĺžka slotu (min)</label>
                            <input type="number" name="slot_interval_minutes" class="input-control" min="5" max="120" value="{{ $settings->slot_interval_minutes ?? 15 }}" required>
                        </div>
                        <div>
                            <label class="label">Min. notice (min)</label>
                            <input type="number" name="min_notice_minutes" class="input-control" min="0" max="1440" value="{{ $settings->min_notice_minutes ?? 60 }}" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="label">Buffer pred (min)</label>
                            <input type="number" name="buffer_before_minutes" class="input-control" min="0" max="120" value="{{ $settings->buffer_before_minutes ?? 0 }}" required>
                        </div>
                        <div>
                            <label class="label">Buffer po (min)</label>
                            <input type="number" name="buffer_after_minutes" class="input-control" min="0" max="120" value="{{ $settings->buffer_after_minutes ?? 0 }}" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="label">Max. dopredu (dni)</label>
                            <input type="number" name="max_advance_days" class="input-control" min="1" max="365" value="{{ $settings->max_advance_days ?? 90 }}" required>
                        </div>
                        <div>
                            <label class="label">Limit storna (h)</label>
                            <input type="number" name="cancellation_limit_hours" class="input-control" min="0" max="720" value="{{ $settings->cancellation_limit_hours ?? 24 }}" required>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button class="px-3 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800">Uložiť</button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</section>
@endsection
