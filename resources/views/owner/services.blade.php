@extends('layouts.owner')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">{{ __('My services') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Manage the service offering for your business.') }}</p>
        </div>
        <button onclick="openAddServiceModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md shadow-emerald-200/50 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>{{ __('Add service') }}</span>
        </button>
    </div>

    <div class="grid lg:grid-cols-[1fr,350px] gap-6 items-start">
        <div class="card space-y-3">
            <h2 class="font-semibold text-lg text-slate-900">{{ __('Service list') }}</h2>
            <div class="space-y-4 max-h-[75vh] overflow-y-auto pr-1">
                @forelse($services as $service)
                    <div class="border border-slate-100 rounded-xl p-4 bg-white/80 space-y-3 shadow-sm hover:border-emerald-100 transition-colors">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-bold text-slate-900">{{ $service->name }}</h3>
                                <p class="text-xs text-slate-500 uppercase tracking-wider">{{ $service->profile->name }} • {{ $service->category }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-bold">€{{ number_format($service->base_price, 2) }}</span>
                                <form action="{{ route('owner.services.delete', $service) }}" method="POST" class="inline" onsubmit="confirmDelete(event, '{{ __('Are you sure you want to delete this service?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors" title="{{ __('Delete') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <details class="text-sm">
                            <summary class="cursor-pointer text-slate-600 hover:text-slate-900 font-medium py-1">{{ __('Edit service') }}</summary>
                            <form method="POST" action="{{ route('owner.services.update', $service) }}" class="space-y-3 mt-2 pt-2 border-t border-slate-100">
                                @csrf
                                <div class="grid grid-cols-1 gap-3">
                                    @if($service->profile->is_multilingual)
                                        <div class="space-y-2">
                                            <label class="text-[10px] uppercase font-bold text-slate-500">{{ __('Service name (SK / EN / UA-RU)') }}</label>
                                            <div class="grid grid-cols-3 gap-2">
                                                <input type="text" name="name[sk]" class="input-control !py-1.5 !text-sm" value="{{ $service->getTranslations('name')['sk'] ?? '' }}" placeholder="{{ __('Slovak') }}" required>
                                                <input type="text" name="name[en]" class="input-control !py-1.5 !text-sm" value="{{ $service->getTranslations('name')['en'] ?? '' }}" placeholder="{{ __('English') }}">
                                                <input type="text" name="name[ru]" class="input-control !py-1.5 !text-sm" value="{{ $service->getTranslations('name')['ru'] ?? '' }}" placeholder="{{ __('Russian') }}">
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-[10px] uppercase font-bold text-slate-500">{{ __('Category (SK / EN / UA-RU)') }}</label>
                                            <div class="grid grid-cols-3 gap-2">
                                                <input type="text" name="category[sk]" class="input-control !py-1.5 !text-sm" value="{{ $service->getTranslations('category')['sk'] ?? '' }}" placeholder="SK">
                                                <input type="text" name="category[en]" class="input-control !py-1.5 !text-sm" value="{{ $service->getTranslations('category')['en'] ?? '' }}" placeholder="EN">
                                                <input type="text" name="category[ru]" class="input-control !py-1.5 !text-sm" value="{{ $service->getTranslations('category')['ru'] ?? '' }}" placeholder="RU">
                                            </div>
                                        </div>
                                    @else
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="text-[10px] uppercase font-bold text-slate-500">{{ __('Service name') }}</label>
                                                <input type="text" name="name" class="input-control !py-1.5 !text-sm" value="{{ $service->name }}" required>
                                            </div>
                                            <div>
                                                <label class="text-[10px] uppercase font-bold text-slate-500">{{ __('Category') }}</label>
                                                <input type="text" name="category" class="input-control !py-1.5 !text-sm" value="{{ $service->category }}" placeholder="{{ __('Category') }}">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-slate-500">{{ __('Duration (min)') }}</label>
                                        <input type="number" name="base_duration_minutes" class="input-control !py-1.5 !text-sm" value="{{ $service->base_duration_minutes }}" required>
                                    </div>
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-slate-500">{{ __('Price (€)') }}</label>
                                        <input type="number" name="base_price" class="input-control !py-1.5 !text-sm" value="{{ $service->base_price }}" step="0.01" required>
                                    </div>
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-slate-500">{{ __('Slot interval (min)') }}</label>
                                        <input type="number" name="slot_interval_minutes" class="input-control !py-1.5 !text-sm" value="{{ $service->slot_interval_minutes }}" placeholder="{{ __('Auto') }}">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-slate-500">{{ __('Employees') }}</label>
                                    <div class="grid grid-cols-2 gap-1 mt-1">
                                        @foreach($employees->where('profile_id', $service->profile_id) as $employee)
                                            <label class="flex items-center gap-1 text-xs">
                                                <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" @checked($service->employees->contains($employee->id))>
                                                {{ $employee->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                @if($service->profile->isApiAvailable('pakavoz'))
                                <div class="grid grid-cols-2 gap-2 border-t border-slate-50 pt-2 mt-2">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="is_pakavoz_enabled" value="1" @checked($service->is_pakavoz_enabled) id="pakavoz_enabled_{{ $service->id }}" onchange="togglePakavozKey('{{ $service->id }}')">
                                        <label for="pakavoz_enabled_{{ $service->id }}" class="text-[10px] uppercase font-bold text-slate-500">{{ __('Pakavoz API') }}</label>
                                    </div>
                                    <div id="pakavoz_key_container_{{ $service->id }}" @style(['display: none' => !$service->is_pakavoz_enabled])>
                                        <label class="text-[10px] uppercase font-bold text-slate-500">{{ __('Pakavoz API Key') }}</label>
                                        <input type="text" name="pakavoz_api_key" class="input-control !py-1.5 !text-sm" value="{{ $service->pakavoz_api_key }}" placeholder="pakavoz_secure_token_...">
                                    </div>
                                </div>
                                @endif

                                <div class="mt-4 pt-4 border-t border-slate-100">
                                    <label class="flex items-center gap-2 mb-2 cursor-pointer">
                                        <input type="checkbox" name="is_special" value="1" @checked($service->is_special)>
                                        <span class="text-xs font-bold uppercase text-slate-500">{{ __('Special service (exclusive time)') }}</span>
                                    </label>

                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Availability rules') }}</label>
                                            <p class="text-[9px] text-slate-400 italic">{{ __('If special, service is ONLY available in these windows.') }}</p>
                                        </div>

                                        @php $rules = $service->availabilityRules->whereNull('service_variant_id'); @endphp

                                        <div class="space-y-3">
                                            @foreach($rules as $index => $rule)
                                                <div class="p-3 rounded-xl bg-slate-50/50 border border-slate-100 space-y-3">
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div class="space-y-1">
                                                            <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Day') }}</label>
                                                            <select name="availability_rules[{{ $index }}][day_of_week]" class="input-control !py-1 !text-xs">
                                                                <option value="">{{ __('Every day') }}</option>
                                                                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $i => $day)
                                                                    <option value="{{ $i }}" @selected($rule->day_of_week !== null && (int)$rule->day_of_week === $i)>{{ __($day) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <div class="space-y-1">
                                                                <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('From') }}</label>
                                                                <input type="time" name="availability_rules[{{ $index }}][start_time]" value="{{ substr($rule->start_time, 0, 5) }}" class="input-control !py-1 !text-xs">
                                                            </div>
                                                            <div class="space-y-1">
                                                                <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('To') }}</label>
                                                                <input type="time" name="availability_rules[{{ $index }}][end_time]" value="{{ substr($rule->end_time, 0, 5) }}" class="input-control !py-1 !text-xs">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            <div class="p-3 rounded-xl bg-emerald-50/30 border border-emerald-100/50 space-y-3">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[9px] font-bold uppercase text-emerald-600">{{ __('Add new window') }}</span>
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div class="space-y-1">
                                                        <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Day') }}</label>
                                                        <select name="availability_rules[new][day_of_week]" class="input-control !py-1 !text-xs">
                                                            <option value="">{{ __('Every day') }}</option>
                                                            @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $i => $day)
                                                                <option value="{{ $i }}">{{ __($day) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div class="space-y-1">
                                                            <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('From') }}</label>
                                                            <input type="time" name="availability_rules[new][start_time]" class="input-control !py-1 !text-xs">
                                                        </div>
                                                        <div class="space-y-1">
                                                            <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('To') }}</label>
                                                            <input type="time" name="availability_rules[new][end_time]" class="input-control !py-1 !text-xs">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button class="w-full px-3 py-1.5 mt-3 rounded-lg bg-slate-900 text-white text-xs font-semibold">{{ __('Save changes') }}</button>
                            </form>

                            <div class="mt-4 pt-4 border-t border-slate-100 space-y-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Variants') }}</h4>
                                <div class="space-y-2">
                                    @foreach($service->variants as $variant)
                                        <div class="p-2 rounded-lg bg-slate-50 text-xs space-y-2">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="font-bold text-slate-700">{{ $variant->name }}</span>
                                                    <span class="text-slate-500 ml-2">{{ $variant->duration_minutes }} min • €{{ number_format($variant->price, 2) }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button type="button" onclick="toggleEditVariant('{{ $variant->id }}')" class="text-slate-400 hover:text-slate-600" title="{{ __('Edit') }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                    </button>
                                                    <form action="{{ route('owner.services.variants.delete', [$service, $variant]) }}" method="POST" class="inline" onsubmit="confirmDelete(event, '{{ __('Are you sure you want to delete this variant?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700" title="{{ __('Delete') }}">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <form id="edit-variant-{{ $variant->id }}" method="POST" action="{{ route('owner.services.variants.update', [$service, $variant]) }}" class="hidden space-y-2 pt-2 border-t border-slate-200">
                                                @csrf
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Name') }}</label>
                                                        <input type="text" name="name" class="input-control !py-1 !text-[10px]" value="{{ $variant->name }}" required>
                                                    </div>
                                                    <div>
                                                        <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Duration') }}</label>
                                                        <input type="number" name="duration_minutes" class="input-control !py-1 !text-[10px]" value="{{ $variant->duration_minutes }}" required>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Price') }}</label>
                                                        <input type="number" name="price" step="0.01" class="input-control !py-1 !text-[10px]" value="{{ $variant->price }}" required>
                                                    </div>
                                                    <div class="flex items-end">
                                                        <button type="submit" class="w-full px-2 py-1 rounded bg-slate-800 text-white font-bold text-[10px]">{{ __('Save') }}</button>
                                                    </div>
                                                </div>

                                                <div class="mt-2 pt-2 border-t border-slate-200">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <label class="flex items-center gap-2 cursor-pointer">
                                                            <input type="checkbox" name="is_special" value="1" @checked($variant->is_special)>
                                                            <span class="text-[9px] font-bold uppercase text-slate-400">{{ __('Special variant (exclusive time)') }}</span>
                                                        </label>
                                                        <p class="text-[8px] text-slate-400 italic">{{ __('Variant ONLY available in these windows.') }}</p>
                                                    </div>

                                                    <div class="space-y-2">
                                                        @foreach($variant->availabilityRules as $index => $rule)
                                                            <div class="grid grid-cols-2 gap-2 p-1.5 rounded-lg bg-white border border-slate-100">
                                                                <select name="availability_rules[{{ $index }}][day_of_week]" class="input-control !py-0.5 !text-[9px]">
                                                                    <option value="">{{ __('Every day') }}</option>
                                                                    @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $i => $day)
                                                                        <option value="{{ $i }}" @selected($rule->day_of_week !== null && (int)$rule->day_of_week === $i)>{{ __($day) }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="grid grid-cols-2 gap-1">
                                                                    <input type="time" name="availability_rules[{{ $index }}][start_time]" value="{{ substr($rule->start_time, 0, 5) }}" class="input-control !py-0.5 !text-[9px]">
                                                                    <input type="time" name="availability_rules[{{ $index }}][end_time]" value="{{ substr($rule->end_time, 0, 5) }}" class="input-control !py-0.5 !text-[9px]">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        <div class="grid grid-cols-2 gap-2 p-1.5 rounded-lg bg-emerald-50/50 border border-emerald-100/30">
                                                            <select name="availability_rules[new][day_of_week]" class="input-control !py-0.5 !text-[9px]">
                                                                <option value="">{{ __('Every day') }}</option>
                                                                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $i => $day)
                                                                    <option value="{{ $i }}">{{ __($day) }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="grid grid-cols-2 gap-1">
                                                                <input type="time" name="availability_rules[new][start_time]" class="input-control !py-0.5 !text-[9px]">
                                                                <input type="time" name="availability_rules[new][end_time]" class="input-control !py-0.5 !text-[9px]">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>

                                <details class="text-[10px]">
                                    <summary class="cursor-pointer text-emerald-600 hover:text-emerald-700 font-bold uppercase tracking-tight">{{ __('Add variant') }}</summary>
                                    <form method="POST" action="{{ route('owner.services.variants.store', $service) }}" class="space-y-2 mt-2">
                                        @csrf
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="text" name="name" class="input-control !py-1 !text-xs" placeholder="{{ __('Variant name') }}" required>
                                            <input type="number" name="duration_minutes" class="input-control !py-1 !text-xs" placeholder="{{ __('Duration (min)') }}" required>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="number" name="price" step="0.01" class="input-control !py-1 !text-xs" placeholder="{{ __('Price (€)') }}" required>
                                            <button class="px-3 py-1 rounded-lg bg-emerald-500 text-white font-bold">{{ __('Add') }}</button>
                                        </div>
                                    </form>
                                </details>
                            </div>
                        </details>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 italic p-8 text-center">{{ __('You have no services added yet.') }}</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('Information') }}
                </h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    {{ __('Services define what you offer to your clients. You can set the duration, price, and assign specific employees who perform the service.') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Add Service Modal -->
<div id="addServiceModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeAddServiceModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-display font-semibold text-slate-900">{{ __('Add new service') }}</h3>
                <button onclick="closeAddServiceModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('owner.services.store') }}" class="space-y-4">
                @csrf
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

                <div class="grid sm:grid-cols-2 gap-4" id="service_name_container">
                    <div>
                        <label class="label">{{ __('Service name') }}</label>
                        <input type="text" name="name" id="add_service_name_single" class="input-control" placeholder="{{ __('e.g. Men\'s haircut') }}" required>
                    </div>
                    <div>
                        <label class="label">{{ __('Category') }}</label>
                        <input type="text" name="category" id="add_service_category_single" class="input-control" placeholder="{{ __('e.g. Hairdressing') }}">
                    </div>
                </div>

                <div id="service_name_multilingual" class="hidden space-y-4">
                    <div class="space-y-2">
                        <label class="label !mb-0">{{ __('Service name (SK / EN / UA-RU)') }}</label>
                        <div class="grid grid-cols-3 gap-2">
                            <input type="text" name="name[sk]" class="input-control" placeholder="SK">
                            <input type="text" name="name[en]" class="input-control" placeholder="EN">
                            <input type="text" name="name[ru]" class="input-control" placeholder="RU">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="label !mb-0">{{ __('Category (SK / EN / UA-RU)') }}</label>
                        <div class="grid grid-cols-3 gap-2">
                            <input type="text" name="category[sk]" class="input-control" placeholder="SK">
                            <input type="text" name="category[en]" class="input-control" placeholder="EN">
                            <input type="text" name="category[ru]" class="input-control" placeholder="RU">
                        </div>
                    </div>
                </div>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="label">{{ __('Base duration (min)') }}</label>
                        <input type="number" name="base_duration_minutes" class="input-control" value="30" min="5" required>
                    </div>
                    <div>
                        <label class="label">{{ __('Base price (€)') }}</label>
                        <input type="number" name="base_price" class="input-control" value="0" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label class="label">{{ __('Slot interval (min)') }}</label>
                        <input type="number" name="slot_interval_minutes" class="input-control" placeholder="{{ __('Auto') }}" min="5">
                    </div>
                </div>

                <div>
                    <label class="label">{{ __('Assign employees') }}</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($employees as $employee)
                            <label class="flex items-center gap-2 p-2 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                                <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 rounded border-slate-300">
                                <span class="text-sm font-medium text-slate-700">{{ $employee->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4 border-t border-slate-50 pt-4 hidden" id="pakavoz_section_add">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_pakavoz_enabled" value="1" id="add_pakavoz_enabled" onchange="togglePakavozKey('add')">
                        <label for="add_pakavoz_enabled" class="label !mb-0">{{ __('Activate Pakavoz API') }}</label>
                    </div>
                    <div id="pakavoz_key_container_add" style="display: none">
                        <label class="label">{{ __('Pakavoz API Key') }}</label>
                        <input type="text" name="pakavoz_api_key" class="input-control" placeholder="pakavoz_secure_token_...">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-50">
                    <button type="button" onclick="closeAddServiceModal()" class="px-6 py-2 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-lg shadow-emerald-200/50">{{ __('Create service') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const multilingualProfiles = @json($profiles->where('is_multilingual', true)->pluck('id')->values());
    const pakavozProfiles = @json($profiles->filter(fn($p) => $p->isApiAvailable('pakavoz'))->pluck('id')->values());

    $(document).ready(function() {
        if ($.fn.niceSelect) {
            $('.nice-select').niceSelect();
        }

        $('select[name="profile_id"]').on('change', function() {
            const profileId = parseInt($(this).val());
            const isMultilingual = multilingualProfiles.includes(profileId);
            const isPakavoz = pakavozProfiles.includes(profileId);

            if (isMultilingual) {
                $('#service_name_container').addClass('hidden');
                $('#add_service_name_single').removeAttr('required');
                $('#service_name_multilingual').removeClass('hidden');
                $('#service_name_multilingual input[name="name[sk]"]').attr('required', 'required');
            } else {
                $('#service_name_container').removeClass('hidden');
                $('#add_service_name_single').attr('required', 'required');
                $('#service_name_multilingual').addClass('hidden');
                $('#service_name_multilingual input[name="name[sk]"]').removeAttr('required');
            }

            if (isPakavoz) {
                $('#pakavoz_section_add').removeClass('hidden');
            } else {
                $('#pakavoz_section_add').addClass('hidden');
            }
        });

        // Trigger change on load to set initial state
        $('select[name="profile_id"]').trigger('change');
    });

    function openAddServiceModal() {
        document.getElementById('addServiceModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        if ($.fn.niceSelect) {
            $('#addServiceModal .nice-select').niceSelect('update');
        }
    }

    function closeAddServiceModal() {
        document.getElementById('addServiceModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function togglePakavozKey(id) {
        const checkbox = id === 'add' ? document.getElementById('add_pakavoz_enabled') : document.getElementById('pakavoz_enabled_' + id);
        const container = document.getElementById('pakavoz_key_container_' + id);
        if (checkbox.checked) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    function toggleEditVariant(id) {
        const form = document.getElementById('edit-variant-' + id);
        if (form.classList.contains('hidden')) {
            form.classList.remove('hidden');
        } else {
            form.classList.add('hidden');
        }
    }
</script>
@endsection
