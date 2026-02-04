@extends('layouts.app')

@section('content')
<div class="overflow-x-hidden">
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Nastavenia kalendára</h1>
        </div>
        <span class="badge">Konfigurácia</span>
    </div>

    @include('owner.partials.nav')

    @if($errors->any())
        <div class="card border-rose-200 bg-rose-50 text-rose-800">
            <ul class="list-disc list-inside text-sm font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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

                <form method="POST" action="{{ route('owner.calendar.settings.store') }}" class="space-y-4" enctype="multipart/form-data">
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
                            <p class="text-[10px] text-slate-500 italic">Ako dlho vopred sa možno objednať (napr. 60 min).</p>
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

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="label">Logo prevádzky</label>
                                <input type="file" name="logo" class="input-control !p-2 text-xs">
                                @if($profile->logo_url)
                                    <div class="mt-2 relative group">
                                        <img src="{{ $profile->logo_url }}" class="h-12 w-12 object-contain rounded-lg border border-slate-200 bg-white" alt="Logo preview">
                                        <p class="text-[10px] text-emerald-600 mt-1 font-bold uppercase tracking-tight">Logo je nahrané</p>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <label class="label">Banner prevádzky</label>
                                <input type="file" name="banner" class="input-control !p-2 text-xs">
                                @if($profile->banner_url)
                                    <div class="mt-2 relative group">
                                        <img src="{{ $profile->banner_url }}" class="h-12 w-24 object-cover rounded-lg border border-slate-200 bg-white" alt="Banner preview">
                                        <p class="text-[10px] text-emerald-600 mt-1 font-bold uppercase tracking-tight">Banner je nahraný</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="label">Popis prevádzky</label>
                            <textarea name="description" class="input-control" rows="3" placeholder="Popis prevádzky...">{{ old('description', $profile->description) }}</textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="font-semibold text-slate-900 block">Verejná prevádzka</label>
                                <p class="text-xs text-slate-500">Ak je zapnuté, prevádzka a služby sú viditeľné na webe.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_public" value="0">
                                <input type="checkbox" name="is_public" value="1" class="sr-only peer" @checked(old('is_public', $settings->is_public ?? true))>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="font-semibold text-slate-900 block">Vyžadovať potvrdenie</label>
                                <p class="text-xs text-slate-500">Ak je vypnuté, rezervácie sa potvrdia automaticky.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="requires_confirmation" value="0">
                                <input type="checkbox" name="requires_confirmation" value="1" class="sr-only peer" @checked(old('requires_confirmation', $settings->requires_confirmation ?? false))>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
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
</div>
@endsection
