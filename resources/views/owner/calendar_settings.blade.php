@extends('layouts.owner')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">Nastavenia kalendára</h1>
            <p class="text-sm text-slate-500">Konfigurácia pravidiel rezervácií a profilu vašej prevádzky.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="card border-rose-200 bg-rose-50 text-rose-800">
            <ul class="list-disc list-inside text-sm font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">
        @foreach($profiles as $profile)
            <div class="card p-6">
                <div class="flex items-center justify-between border-b border-slate-50 pb-4 mb-6">
                    <div>
                        <h2 class="font-bold text-xl text-slate-900 leading-tight">{{ $profile->name }}</h2>
                        <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">{{ $profile->city }}</p>
                    </div>
                    <span class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 font-bold uppercase">{{ substr($profile->name, 0, 1) }}</span>
                </div>

                <form method="POST" action="{{ route('owner.calendar.settings.store') }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="profile_id" value="{{ $profile->id }}">
                    @php $settings = $profile->calendarSetting; @endphp

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="label text-[10px]">Interval slotov (min)</label>
                            <div class="nice-select-wrapper">
                                <select name="slot_interval_minutes" class="nice-select" required>
                                    @foreach([15, 20, 30, 45, 60] as $min)
                                        <option value="{{ $min }}" @selected(old('slot_interval_minutes', $settings->slot_interval_minutes ?? 15) == $min)>{{ $min }} min</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="label text-[10px]">Min. čas pred (min)</label>
                            <input type="number" name="min_notice_minutes" class="input-control !py-2 !text-sm" min="0" value="{{ old('min_notice_minutes', $settings->min_notice_minutes ?? 60) }}" required title="Ako dlho vopred sa možno objednať (napr. 60 min).">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="label text-[10px]">Buffer PRED (min)</label>
                            <input type="number" name="buffer_before_minutes" class="input-control !py-2 !text-sm" min="0" value="{{ old('buffer_before_minutes', $settings->buffer_before_minutes ?? 0) }}" required>
                        </div>
                        <div class="space-y-1">
                            <label class="label text-[10px]">Buffer PO (min)</label>
                            <input type="number" name="buffer_after_minutes" class="input-control !py-2 !text-sm" min="0" value="{{ old('buffer_after_minutes', $settings->buffer_after_minutes ?? 0) }}" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="label text-[10px]">Max. dopredu (dni)</label>
                            <input type="number" name="max_advance_days" class="input-control !py-2 !text-sm" min="1" value="{{ old('max_advance_days', $settings->max_advance_days ?? 90) }}" required>
                        </div>
                        <div class="space-y-1">
                            <label class="label text-[10px]">Limit storna (h)</label>
                            <input type="number" name="cancellation_limit_hours" class="input-control !py-2 !text-sm" min="0" value="{{ old('cancellation_limit_hours', $settings->cancellation_limit_hours ?? 24) }}" required>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="label text-[10px]">Logo</label>
                                <input type="file" name="logo" class="input-control !p-2 !text-xs">
                                @if($profile->logo_url)
                                    <div class="mt-2 flex items-center gap-2">
                                        <img src="{{ $profile->logo_url }}" class="h-10 w-10 object-contain rounded-lg border border-slate-200 bg-white" alt="Logo preview">
                                        <p class="text-[10px] text-emerald-600 font-bold uppercase">Aktívne</p>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <label class="label text-[10px]">Banner</label>
                                <input type="file" name="banner" class="input-control !p-2 !text-xs">
                                @if($profile->banner_url)
                                    <div class="mt-2 flex items-center gap-2">
                                        <img src="{{ $profile->banner_url }}" class="h-10 w-20 object-cover rounded-lg border border-slate-200 bg-white" alt="Banner preview">
                                        <p class="text-[10px] text-emerald-600 font-bold uppercase">Aktívny</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="label text-[10px]">Popis prevádzky</label>
                            <textarea name="description" class="input-control !text-sm" rows="3" placeholder="Popis prevádzky...">{{ old('description', $profile->description) }}</textarea>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                            <div>
                                <label class="text-sm font-bold text-slate-900 block">Verejná prevádzka</label>
                                <p class="text-[10px] text-slate-500">Viditeľnosť na BookMe.sk</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_public" value="0">
                                <input type="checkbox" name="is_public" value="1" class="sr-only peer" @checked(old('is_public', $settings->is_public ?? true))>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                            <div>
                                <label class="text-sm font-bold text-slate-900 block">Vyžadovať potvrdenie</label>
                                <p class="text-[10px] text-slate-500">Manuálne schvaľovanie termínov</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="requires_confirmation" value="0">
                                <input type="checkbox" name="requires_confirmation" value="1" class="sr-only peer" @checked(old('requires_confirmation', $settings->requires_confirmation ?? false))>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                            </label>
                        </div>
                    </div>

                    <button class="w-full px-4 py-3 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-200/50">
                        Uložiť nastavenia
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</div>

<script>
    $(document).ready(function() {
        if ($.fn.niceSelect) {
            $('.nice-select').niceSelect();
        }
    });
</script>
@endsection
