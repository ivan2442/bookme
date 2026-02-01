@extends('layouts.app')

@section('content')
<div class="space-y-8 pb-12">
    <!-- Hero Banner & Logo -->
    <div class="relative h-64 md:h-80 w-full rounded-3xl overflow-hidden shadow-lg mt-6">
        @if($profile->banner_path)
            <img src="{{ asset('storage/' . $profile->banner_path) }}" alt="{{ $profile->name }} banner" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-r from-emerald-400 to-emerald-600 flex items-center justify-center">
                <span class="text-white/20 text-6xl font-bold">{{ $profile->name }}</span>
            </div>
        @endif

        <div class="absolute bottom-6 left-6 flex items-end gap-6">
            <div class="h-24 w-24 md:h-32 md:w-32 rounded-2xl bg-white p-2 shadow-xl border border-white/20 overflow-hidden">
                @if($profile->logo_path)
                    <img src="{{ asset('storage/' . $profile->logo_path) }}" alt="{{ $profile->name }} logo" class="w-full h-full object-contain rounded-xl">
                @else
                    <div class="w-full h-full bg-slate-100 flex items-center justify-center rounded-xl">
                        <span class="text-slate-400 font-bold text-2xl">{{ substr($profile->name, 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <div class="mb-2">
                <h1 class="text-3xl md:text-4xl font-display font-bold text-white drop-shadow-md">{{ $profile->name }}</h1>
                <p class="text-white/90 font-medium drop-shadow-sm">{{ $profile->category }} • {{ $profile->city }}</p>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Left Column: Description & Services Info -->
        <div class="lg:col-span-2 space-y-8">
            <section class="bg-white/90 backdrop-blur rounded-3xl p-8 shadow-sm border border-slate-100 space-y-4">
                <h2 class="text-2xl font-display font-bold text-slate-900">O prevádzke</h2>
                <div class="text-slate-600 leading-relaxed whitespace-pre-line">
                    {{ $profile->description ?? 'Táto prevádzka zatiaľ nemá pridaný popis.' }}
                </div>
            </section>

            <section class="bg-white/90 backdrop-blur rounded-3xl p-8 shadow-sm border border-slate-100 space-y-6">
                <h2 class="text-2xl font-display font-bold text-slate-900">Naša ponuka</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    @foreach($profile->services as $service)
                        <div class="p-4 rounded-2xl border border-slate-100 hover:border-emerald-200 transition-colors">
                            <p class="font-bold text-slate-900">{{ $service->name }}</p>
                            <p class="text-sm text-slate-500">{{ $service->base_duration_minutes }} min • od €{{ number_format($service->base_price, 2) }}</p>
                            @if($service->employees->count() > 0)
                                <div class="mt-2 flex flex-wrap gap-1">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold">Zamestnanci:</span>
                                    @foreach($service->employees as $employee)
                                        <span class="text-[11px] bg-slate-50 text-slate-600 px-2 py-0.5 rounded-full border border-slate-100">{{ $employee->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="pt-4">
                    <a href="{{ route('home') }}#booking" class="inline-flex items-center px-6 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-semibold transition shadow-lg shadow-emerald-200/50">
                        Rezervovať termín
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </section>
        </div>

        <!-- Right Column: Opening Hours & Location -->
        <div class="space-y-8">
            <!-- Opening Hours -->
            <section class="bg-white/90 backdrop-blur rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
                <h2 class="text-xl font-display font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Otváracie hodiny
                </h2>
                <div class="space-y-2">
                    @php
                        $days = [
                            1 => 'Pondelok',
                            2 => 'Utorok',
                            3 => 'Streda',
                            4 => 'Štvrtok',
                            5 => 'Piatok',
                            6 => 'Sobota',
                            7 => 'Nedeľa'
                        ];
                        $schedules = $profile->schedules->groupBy('day_of_week');
                    @endphp
                    @foreach($days as $dayNum => $dayName)
                        <div class="flex items-center justify-between text-sm py-1 border-b border-slate-50 last:border-0">
                            <span class="text-slate-600 font-medium">{{ $dayName }}</span>
                            @if($schedules->has($dayNum))
                                <span class="text-slate-900 font-bold">
                                    {{ \Carbon\Carbon::parse($schedules[$dayNum]->first()->start_time)->format('H:i') }} —
                                    {{ \Carbon\Carbon::parse($schedules[$dayNum]->first()->end_time)->format('H:i') }}
                                </span>
                            @else
                                <span class="text-slate-400 italic">Zatvorené</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Location & Map -->
            <section class="bg-white/90 backdrop-blur rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
                <h2 class="text-xl font-display font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Kde nás nájdete
                </h2>
                <div class="text-sm text-slate-600 space-y-1">
                    <p class="font-bold text-slate-900">{{ $profile->address_line1 }}</p>
                    <p>{{ $profile->postal_code }} {{ $profile->city }}</p>
                </div>

                @if($profile->latitude && $profile->longitude)
                    <div class="h-48 rounded-2xl overflow-hidden border border-slate-100 mt-4">
                        <iframe
                            width="100%"
                            height="100%"
                            frameborder="0"
                            style="border:0"
                            src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google_maps.api_key', 'YOUR_API_KEY') }}&q={{ $profile->latitude }},{{ $profile->longitude }}"
                            allowfullscreen>
                        </iframe>
                    </div>
                @else
                     <div class="h-48 rounded-2xl overflow-hidden border border-slate-100 mt-4">
                        <iframe
                            width="100%"
                            height="100%"
                            frameborder="0"
                            style="border:0"
                            src="https://www.google.com/maps?q={{ urlencode($profile->address_line1 . ', ' . $profile->city) }}&output=embed"
                            allowfullscreen>
                        </iframe>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection
