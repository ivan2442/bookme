@extends('layouts.owner')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">{{ __('Calendar settings') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Configuration of booking rules and your business profile.') }}</p>
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
                            <label class="label text-[10px]">{{ __('Slot interval (min)') }}</label>
                            <div class="nice-select-wrapper">
                                <select name="slot_interval_minutes" class="nice-select" required>
                                    @foreach([15, 20, 30, 45, 60] as $min)
                                        <option value="{{ $min }}" @selected(old('slot_interval_minutes', $settings->slot_interval_minutes ?? 15) == $min)>{{ $min }} min</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="label text-[10px]">{{ __('Min. notice (min)') }}</label>
                            <input type="number" name="min_notice_minutes" class="input-control !py-2 !text-sm" min="0" value="{{ old('min_notice_minutes', $settings->min_notice_minutes ?? 60) }}" required title="{{ __('How long in advance can a booking be made (e.g. 60 min).') }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="label text-[10px]">{{ __('Buffer BEFORE (min)') }}</label>
                            <input type="number" name="buffer_before_minutes" class="input-control !py-2 !text-sm" min="0" value="{{ old('buffer_before_minutes', $settings->buffer_before_minutes ?? 0) }}" required>
                        </div>
                        <div class="space-y-1">
                            <label class="label text-[10px]">{{ __('Buffer AFTER (min)') }}</label>
                            <input type="number" name="buffer_after_minutes" class="input-control !py-2 !text-sm" min="0" value="{{ old('buffer_after_minutes', $settings->buffer_after_minutes ?? 0) }}" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="label text-[10px]">{{ __('Max. advance (days)') }}</label>
                            <input type="number" name="max_advance_days" class="input-control !py-2 !text-sm" min="1" value="{{ old('max_advance_days', $settings->max_advance_days ?? 90) }}" required>
                        </div>
                        <div class="space-y-1">
                            <label class="label text-[10px]">{{ __('Cancellation limit (h)') }}</label>
                            <input type="number" name="cancellation_limit_hours" class="input-control !py-2 !text-sm" min="0" value="{{ old('cancellation_limit_hours', $settings->cancellation_limit_hours ?? 24) }}" required>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="label text-[10px]">{{ __('Logo') }}</label>
                                <input type="file" name="logo" class="input-control !p-2 !text-xs">
                                @if($profile->logo_url)
                                    <div class="mt-2 flex items-center gap-2">
                                        <img src="{{ $profile->logo_url }}" class="h-10 w-10 object-contain rounded-lg border border-slate-200 bg-white" alt="{{ __('Logo preview') }}">
                                        <p class="text-[10px] text-emerald-600 font-bold uppercase">{{ __('Active') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <label class="label text-[10px]">{{ __('Banner') }}</label>
                                <input type="file" name="banner" class="input-control !p-2 !text-xs">
                                @if($profile->banner_url)
                                    <div class="mt-2 flex items-center gap-2">
                                        <img src="{{ $profile->banner_url }}" class="h-10 w-20 object-cover rounded-lg border border-slate-200 bg-white" alt="{{ __('Banner preview') }}">
                                        <p class="text-[10px] text-emerald-600 font-bold uppercase">{{ __('Active') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="label text-[10px]">{{ __('Business description') }}</label>
                            @if($profile->is_multilingual)
                                <div class="space-y-3">
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="w-4 h-4 rounded-full overflow-hidden border border-slate-200 inline-flex items-center justify-center">
                                                <svg viewBox="0 0 640 480" class="w-full h-full object-cover transform scale-150"><g fill-rule="evenodd" stroke-width="1pt"><path fill="#fff" d="M0 0h640v480H0z"/><path fill="#0b4ea2" d="M0 160h640v320H0z"/><path fill="#ee1c25" d="M0 320h640v160H0z"/><g transform="matrix(1.238 0 0 1.238 181.8 259.6)"><path fill="#fff" d="M0-112.5c-37.5 0-60 22.5-60 60 0 42.5 35 70 60 82.5 25-12.5 60-40 60-82.5 0-37.5-22.5-60-60-60z"/><path fill="#ee1c25" d="M0-100c-30 0-50 17.5-50 50 0 35 30 60 50 70 20-10 50-35 50-70 0-32.5-20-50-50-50z"/><path fill="#0b4ea2" d="M-30-10h60v10h-60zm10 10h40v10h-40zM-1.2-40h2.4v30h-2.4zm-15 0h30v2.4h-30z"/></g></g></svg>
                                            </span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">{{ __('Slovak') }}</span>
                                        </div>
                                        <textarea name="description[sk]" class="input-control !border-0 !p-0 !text-sm focus:ring-0" rows="2" placeholder="{{ __('Description in Slovak...') }}">{{ old('description.sk', $profile->getTranslations('description')['sk'] ?? '') }}</textarea>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="w-4 h-4 rounded-full overflow-hidden border border-slate-200 inline-flex items-center justify-center">
                                                <svg viewBox="0 0 640 480" class="w-full h-full object-cover transform scale-150"><path fill="#012169" d="M0 0h640v480H0z"/><path fill="#FFF" d="m75 0 244 181L562 0h78v62L400 240l240 178v62h-78L320 300 78 480H0v-62l240-178L0 62V0h75z"/><path fill="#C8102E" d="m424 281 216 159v40L369 281h55zM640 0v3L391 191h55L640 0zM0 480v-3l249-191h-55L0 480zM0 0v40l216 151h55L0 0z"/><path fill="#FFF" d="M240 0v480h160V0H240zM0 160v160h640V160H0z"/><path fill="#C8102E" d="M280 0v480h80V0h-80zM0 200v80h640v-80H0z"/></svg>
                                            </span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">{{ __('English') }}</span>
                                        </div>
                                        <textarea name="description[en]" class="input-control !border-0 !p-0 !text-sm focus:ring-0" rows="2" placeholder="{{ __('Description in English...') }}">{{ old('description.en', $profile->getTranslations('description')['en'] ?? '') }}</textarea>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="w-4 h-4 rounded-full overflow-hidden border border-slate-200 inline-flex items-center justify-center">
                                                <svg viewBox="0 0 640 480" class="w-full h-full object-cover transform scale-150"><g fill-rule="evenodd" stroke-width="1pt"><path fill="#ffd500" d="M0 0h640v480H0z"/><path fill="#005bbb" d="M0 0h640v240H0z"/></g></svg>
                                            </span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">{{ __('Ukrainian (RU)') }}</span>
                                        </div>
                                        <textarea name="description[ru]" class="input-control !border-0 !p-0 !text-sm focus:ring-0" rows="2" placeholder="{{ __('Description in Russian...') }}">{{ old('description.ru', $profile->getTranslations('description')['ru'] ?? '') }}</textarea>
                                    </div>
                                </div>
                            @else
                                <textarea name="description" class="input-control !text-sm" rows="3" placeholder="{{ __('Business description...') }}">{{ old('description', $profile->description) }}</textarea>
                            @endif
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                            <div>
                                <label class="text-sm font-bold text-slate-900 block">{{ __('Multilingualism') }}</label>
                                <p class="text-[10px] text-slate-500">{{ __('Entering content in multiple languages') }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_multilingual" value="0">
                                <input type="checkbox" name="is_multilingual" value="1" class="sr-only peer" @checked(old('is_multilingual', $profile->is_multilingual))>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                            <div>
                                <label class="text-sm font-bold text-slate-900 block">{{ __('Public business') }}</label>
                                <p class="text-[10px] text-slate-500">{{ __('Visibility on BookMe') }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_public" value="0">
                                <input type="checkbox" name="is_public" value="1" class="sr-only peer" @checked(old('is_public', $settings->is_public ?? true))>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                            <div>
                                <label class="text-sm font-bold text-slate-900 block">{{ __('Require confirmation') }}</label>
                                <p class="text-[10px] text-slate-500">{{ __('Manual approval of appointments') }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="requires_confirmation" value="0">
                                <input type="checkbox" name="requires_confirmation" value="1" class="sr-only peer" @checked(old('requires_confirmation', $settings->requires_confirmation ?? false))>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                            </label>
                        </div>
                    </div>

                    <button class="w-full px-4 py-3 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-200/50">
                        {{ __('Save settings') }}
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
