@extends('layouts.app')

@section('content')
<div class="space-y-12 pb-20">
    <!-- Hero Banner & Logo -->
    <div class="relative h-72 md:h-96 w-full rounded-[40px] overflow-hidden shadow-2xl mt-8">
        @if($profile->banner_path)
            <img src="{{ asset('storage/' . $profile->banner_path) }}" alt="{{ $profile->name }} banner" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center">
                <span class="text-white/10 text-9xl font-bold select-none">{{ $profile->name }}</span>
            </div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>

        <div class="absolute bottom-8 left-8 right-8 flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="h-24 w-24 md:h-32 md:w-32 rounded-3xl bg-white p-2 shadow-2xl border border-white/20 overflow-hidden transform hover:scale-105 transition-transform duration-500">
                    @if($profile->logo_path)
                        <img src="{{ asset('storage/' . $profile->logo_path) }}" alt="{{ $profile->name }} logo" class="w-full h-full object-contain rounded-2xl">
                    @else
                        <div class="w-full h-full bg-slate-50 flex items-center justify-center rounded-2xl">
                            <span class="text-emerald-500 font-bold text-3xl">{{ substr($profile->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-3 py-1 rounded-full bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-emerald-500/40">{{ $profile->category }}</span>
                        <div class="flex items-center gap-1 text-white text-sm font-bold">
                            <svg class="w-4 h-4 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            4.9
                        </div>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-display font-bold text-white drop-shadow-xl">{{ $profile->name }}</h1>
                    <p class="text-white/80 font-medium flex items-center gap-2 mt-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $profile->city }}
                    </p>
                </div>
            </div>
            <a href="{{ route('home') }}#booking" class="px-8 py-4 rounded-2xl bg-white text-slate-900 font-bold hover:bg-emerald-500 hover:text-white transition-all shadow-2xl hover:-translate-y-1 active:translate-y-0">
                Rezervovať teraz
            </a>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-10">
        <!-- Left Column: Description & Services Info -->
        <div class="lg:col-span-2 space-y-12">
            <section class="space-y-6">
                <h2 class="text-2xl font-display font-bold text-slate-900 flex items-center gap-3">
                    <span class="w-8 h-1 bg-emerald-500 rounded-full"></span>
                    O nás
                </h2>
                <div class="text-slate-600 text-lg leading-relaxed whitespace-pre-line bg-white rounded-[32px] p-8 border border-slate-50 shadow-sm">
                    {{ $profile->description ?? 'Táto prevádzka zatiaľ nemá pridaný popis.' }}
                </div>
            </section>

            <section class="space-y-6">
                <h2 class="text-2xl font-display font-bold text-slate-900 flex items-center gap-3">
                    <span class="w-8 h-1 bg-emerald-500 rounded-full"></span>
                    Naša ponuka
                </h2>
                <div class="grid sm:grid-cols-1 gap-4">
                    @foreach($profile->services as $service)
                        <div class="group p-6 rounded-[32px] bg-white border border-slate-50 hover:border-emerald-100 transition-all shadow-sm hover:shadow-xl hover:shadow-emerald-200/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex-1">
                                <p class="font-bold text-xl text-slate-900 group-hover:text-emerald-600 transition-colors">{{ $service->name }}</p>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-sm font-medium text-slate-400 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $service->base_duration_minutes }} min
                                    </span>
                                    <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                                    <span class="text-sm font-bold text-emerald-600">od €{{ number_format($service->base_price, 2) }}</span>
                                </div>

                                @if($service->employees->count() > 0)
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach($service->employees as $employee)
                                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-50 border border-slate-100 shadow-sm">
                                                <div class="h-1.5 w-1.5 rounded-full bg-slate-400"></div>
                                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-tight">Zamestnanec: {{ $employee->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-4">
                                <a href="{{ route('home') }}#booking" class="p-4 rounded-[20px] bg-slate-50 text-slate-400 group-hover:bg-emerald-500 group-hover:text-white transition-all shadow-sm group-hover:shadow-lg group-hover:shadow-emerald-200 group-hover:-translate-y-0.5">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- Right Column: Opening Hours & Location -->
        <div class="space-y-10">
            <!-- Opening Hours -->
            <section class="bg-white rounded-[40px] p-8 shadow-xl shadow-slate-200/60 border border-slate-50 space-y-6">
                <h2 class="text-xl font-display font-bold text-slate-900 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    Otváracie hodiny
                </h2>
                <div class="space-y-4">
                    @php
                        $days = [1 => 'Pondelok', 2 => 'Utorok', 3 => 'Streda', 4 => 'Štvrtok', 5 => 'Piatok', 6 => 'Sobota', 7 => 'Nedeľa'];
                        $schedules = $profile->schedules->groupBy('day_of_week');
                    @endphp
                    @foreach($days as $dayNum => $dayName)
                        <div class="flex items-center justify-between text-sm group">
                            <span class="text-slate-500 font-medium group-hover:text-slate-900 transition-colors">{{ $dayName }}</span>
                            @if($schedules->has($dayNum))
                                <span class="text-slate-900 font-bold bg-slate-50 px-3 py-1 rounded-full group-hover:bg-emerald-50 transition-colors">
                                    {{ \Carbon\Carbon::parse($schedules[$dayNum]->first()->start_time)->format('H:i') }} —
                                    {{ \Carbon\Carbon::parse($schedules[$dayNum]->first()->end_time)->format('H:i') }}
                                </span>
                            @else
                                <span class="text-rose-400 font-medium italic">Zatvorené</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Location & Map -->
            <section class="bg-white rounded-[40px] p-8 shadow-xl shadow-slate-200/60 border border-slate-50 space-y-6">
                <h2 class="text-xl font-display font-bold text-slate-900 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    Kde nás nájdete
                </h2>
                <div class="bg-slate-50 p-4 rounded-2xl">
                    <p class="font-bold text-slate-900">{{ $profile->address_line1 }}</p>
                    <p class="text-sm text-slate-500">{{ $profile->postal_code }} {{ $profile->city }}</p>
                </div>

                @php
                    $mapQuery = urlencode($profile->address_line1 . ', ' . $profile->city);
                    $latLong = ($profile->latitude && $profile->longitude) ? $profile->latitude . ',' . $profile->longitude : null;
                @endphp

                <div class="h-64 rounded-[32px] overflow-hidden border border-slate-100 shadow-inner group">
                    <iframe
                        width="100%"
                        height="100%"
                        frameborder="0"
                        style="border:0"
                        src="https://www.google.com/maps?q={{ $latLong ?? $mapQuery }}&output=embed"
                        class="grayscale hover:grayscale-0 transition-all duration-700"
                        allowfullscreen>
                    </iframe>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
