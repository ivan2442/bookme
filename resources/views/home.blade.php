@extends('layouts.app')

@section('content')
<div class="overflow-x-hidden">
<section class="pt-4 lg:pt-16 pb-10 relative">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-emerald-100/40 blur-[120px]"></div>
        <div class="absolute top-[20%] -right-[5%] w-[30%] h-[30%] rounded-full bg-orange-100/30 blur-[100px]"></div>

        <div class="absolute top-[15%] left-[5%] w-12 h-12 bg-emerald-200/20 rounded-2xl rotate-12 animate-float hidden md:block"></div>
        <div class="absolute bottom-[20%] left-[15%] w-8 h-8 bg-orange-200/20 rounded-full animate-float-delayed hidden md:block"></div>
        <div class="absolute top-[40%] right-[10%] w-10 h-10 border-2 border-emerald-200/30 rounded-full animate-float hidden md:block"></div>
    </div>
    <div class="relative grid lg:grid-cols-2 gap-10 items-center">
        <div class="space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/80 border border-emerald-100 text-sm shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                {{ __('Reservation within 3 minutes, without calling') }}
            </div>
            <h1 class="font-display text-4xl md:text-5xl leading-tight text-slate-900">
                BookMe {{ __('connects') }} <span class="relative inline-block">
                    <span class="relative z-10 text-emerald-600">{{ __('businesses') }}</span>
                    <span class="absolute bottom-1 left-0 w-full h-3 bg-emerald-100/60 -z-0 rounded-full"></span>
                </span> {{ __('and their clients.') }}
            </h1>
            <p class="text-lg text-slate-600 max-w-2xl leading-relaxed">
                {{ __('Modern reservation system for your business. Discover services in your area and book an appointment within seconds.') }}
            </p>
            <div class="flex flex-wrap gap-4 pt-2">
                <a href="#search" class="px-8 py-4 rounded-2xl bg-slate-900 text-white font-semibold shadow-xl shadow-slate-200 hover:bg-slate-800 hover:-translate-y-0.5 transition-all">
                    {{ __('Find business') }}
                </a>
            </div>
            <div class="flex items-center gap-6 pt-4">
                <div class="flex -space-x-3">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-emerald-400 to-emerald-200 border-2 border-white flex items-center justify-center text-white font-bold text-xs shadow-md">L</div>
                    <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-orange-400 to-orange-200 border-2 border-white flex items-center justify-center text-white font-bold text-xs shadow-md">M</div>
                    <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-blue-400 to-blue-200 border-2 border-white flex items-center justify-center text-white font-bold text-xs shadow-md">K</div>
                </div>
                <p class="text-sm text-slate-500 font-medium">{{ __('Hundreds of satisfied clients already') }}</p>
            </div>
        </div>

        <div class="relative">
            <div class="absolute -inset-4 bg-emerald-500/5 rounded-[40px] blur-2xl"></div>
            <div class="relative bg-white/90 backdrop-blur-xl rounded-3xl border border-white/60 shadow-2xl shadow-emerald-200/40 p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-emerald-600 tracking-widest mb-1">{{ __('Quick preview') }}</p>
                        <h3 class="font-display text-xl text-slate-900">{{ __('Choose your time') }}</h3>
                    </div>
                    <div class="flex gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-slate-200"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-slate-100"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-slate-50"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3" data-demo-slots>
                    <div class="p-4 rounded-2xl bg-emerald-500 text-white shadow-lg shadow-emerald-200 flex flex-col items-center justify-center gap-1 border border-emerald-400 transform scale-105">
                        <p class="font-bold text-lg">10:00</p>
                        <p class="text-[10px] text-emerald-100 font-medium">{{ __('Your choice') }}</p>
                    </div>
                    <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50 flex flex-col items-center justify-center gap-1 opacity-60">
                        <p class="font-bold text-slate-400 text-lg">10:30</p>
                        <p class="text-[10px] text-slate-400 font-medium">{{ __('busy') }}</p>
                    </div>
                </div>

                <div class="space-y-4 pt-2">
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-white border border-slate-50 shadow-sm">
                        <div class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-tight">{{ __('Client') }}</p>
                            <p class="text-sm font-semibold text-slate-900 leading-none">{{ __('Jane Doe') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-white border border-slate-50 shadow-sm">
                        <div class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-emerald-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-tight">{{ __('Service') }}</p>
                            <p class="text-sm font-semibold text-slate-900 leading-none">{{ __('Women\'s haircut & styling') }}</p>
                        </div>
                    </div>
                </div>

                <p class="text-center text-[11px] text-slate-400 pt-2 italic">{{ __('Booking will be done automatically after confirmation') }}</p>
            </div>
        </div>
    </div>
</section>

<section id="search" class="py-12 space-y-8">
    <div class="flex items-center gap-4">
        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-400 flex items-center justify-center text-white shadow-lg shadow-emerald-200 font-bold text-lg">1</div>
        <div>
            <h2 class="font-display text-3xl text-slate-900">{{ __('Search business') }}</h2>
            <p class="text-slate-500 text-sm italic">{{ __('Discover the best services in your area') }}</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                <svg class="h-6 w-6 text-slate-300 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input id="filter-city" type="text" placeholder="{{ __('In which city are you looking for a service?') }}"
                class="w-full pl-14 pr-6 py-5 bg-white border border-slate-100 rounded-[24px] shadow-xl shadow-slate-200/40 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all text-lg placeholder:text-slate-300 font-medium" />
        </div>

        <div class="flex justify-center">
            <button type="button" id="toggle-advanced" class="group px-6 py-2 rounded-full hover:bg-white hover:shadow-sm transition-all flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400 group-hover:text-emerald-600">{{ __('Advanced filters') }}</span>
                <svg id="advanced-icon" class="w-4 h-4 text-slate-300 group-hover:text-emerald-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        <div id="advanced-filters" class="grid md:grid-cols-2 gap-4 overflow-hidden transition-all duration-500 max-h-0 opacity-0">
            <div class="space-y-2">
                <label class="text-[11px] uppercase tracking-wider text-slate-400 font-bold ml-4">{{ __('Category') }}</label>
                <select id="filter-category" class="w-full px-5 py-4 bg-white border border-slate-100 rounded-2xl focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all font-medium appearance-none">
                    <option value="">{{ __('All categories') }}</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[11px] uppercase tracking-wider text-slate-400 font-bold ml-4">{{ __('Keyword') }}</label>
                <input id="filter-query" type="text" placeholder="{{ __('cut, massage, barber...') }}" class="w-full px-5 py-4 bg-white border border-slate-100 rounded-2xl focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all font-medium placeholder:text-slate-300" />
            </div>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-full overflow-x-hidden" data-shop-list>
        <p class="text-sm text-slate-500">{{ __('Loading businesses...') }}</p>
    </div>
</section>

<section id="services" class="py-12 space-y-6 hidden max-w-full overflow-x-hidden">
    <div class="flex items-center gap-4">
        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-400 flex items-center justify-center text-white shadow-lg shadow-orange-200 font-bold text-lg">2</div>
        <div>
            <h2 class="font-display text-3xl text-slate-900">{{ __('Choose a service') }}</h2>
            <p class="text-slate-500 text-sm italic">{{ __('Wide range of variants for everyone') }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-full" data-services-list>
        <p class="text-sm text-slate-500">{{ __('Loading services...') }}</p>
    </div>
</section>

<section id="booking" class="py-12 space-y-6 hidden max-w-full overflow-x-hidden">
    <div class="flex items-center gap-4">
        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-700 flex items-center justify-center text-white shadow-lg shadow-slate-200 font-bold text-lg">3</div>
        <div>
            <h2 class="font-display text-3xl text-slate-900">{{ __('Confirm appointment') }}</h2>
            <p class="text-slate-500 text-sm italic">{{ __('Just a few clicks and you are booked') }}</p>
        </div>
    </div>
    <div class="grid lg:grid-cols-[2fr,1.2fr] gap-6">
        <form class="p-4 sm:p-6 bg-white/90 border border-slate-100 rounded-2xl shadow-sm space-y-4 max-w-full overflow-hidden" data-booking-form>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 hidden">
                <div>
                    <label class="label">{{ __('Business') }}</label>
                    <select name="profile_id" class="input-control" required data-shop-select>
                        <option value="">{{ __('Choose business') }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">{{ __('Service') }}</label>
                    <select name="service_id" class="input-control" required data-service-select>
                        <option value="">{{ __('Choose service') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 hidden" data-variant-wrapper>
                <div>
                    <label class="label">{{ __('Variant') }}</label>
                    <select name="service_variant_id" class="input-control" data-variant-select>
                        <option value="">{{ __('Choose variant') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="label">{{ __('Name') }}</label>
                    <input name="customer_name" type="text" class="input-control" value="{{ old('customer_name') }}" placeholder="{{ __('Enter name') }}" required />
                </div>
                <div>
                    <label class="label">{{ __('Email') }}</label>
                    <input name="customer_email" type="email" class="input-control" value="{{ old('customer_email') }}" placeholder="na@priklad.sk" required />
                </div>
            </div>

            <div id="home-pakavoz-fields" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="label">{{ __('EČV (ŠPZ)') }}</label>
                    <input name="evc" id="home_evc" type="text" class="input-control" placeholder="{{ __('e.g. BA123XY') }}" />
                </div>
                <div>
                    <label class="label">{{ __('Vehicle model') }}</label>
                    <input name="vehicle_model" type="text" class="input-control" placeholder="{{ __('e.g. Skoda Octavia') }}" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="label">{{ __('Phone') }}</label>
                        <input name="customer_phone" id="home_customer_phone" type="text" class="input-control" value="{{ old('customer_phone') }}" placeholder="+421..." required />
                    </div>
                    <div>
                        <label class="label">{{ __('Note') }}</label>
                        <textarea name="notes" rows="4" class="input-control" placeholder="{{ __('Details for the business') }}"></textarea>
                    </div>
                </div>
                <div>
                    <label class="label">{{ __('Date') }}</label>
                    <div class="date-calendar" data-calendar>
                        <div class="flex items-center justify-between mb-2">
                            <button type="button" class="cal-nav cal-prev" data-cal-prev>
                                <span class="sr-only">{{ __('Previous week') }}</span>
                                ‹
                            </button>
                            <div class="text-center cal-month" data-cal-month>—</div>
                            <button type="button" class="cal-nav cal-next" data-cal-next>
                                <span class="sr-only">{{ __('Next week') }}</span>
                                ›
                            </button>
                        </div>
                        <div class="calendar-grid" data-cal-grid>
                            <div class="calendar-heading">{{ __('mon') }}</div>
                            <div class="calendar-heading">{{ __('tue') }}</div>
                            <div class="calendar-heading">{{ __('wed') }}</div>
                            <div class="calendar-heading">{{ __('thu') }}</div>
                            <div class="calendar-heading">{{ __('fri') }}</div>
                            <div class="calendar-heading">{{ __('sat') }}</div>
                            <div class="calendar-heading">{{ __('sun') }}</div>
                        </div>
                    </div>
                    <input name="date" type="hidden" data-date-input value="{{ date('Y-m-d') }}" />
                </div>
            </div>
            <div>
                <label class="label">{{ __('Time') }}</label>
                <div class="space-y-6" data-time-grid>
                    <span class="text-sm text-slate-500" data-time-placeholder>{{ __('Choose date and variant.') }}</span>
                </div>
                <input type="hidden" name="start_at" data-time-input />
                <input type="hidden" name="employee_id" data-employee-input />
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    {{ __('Slot will be locked for 3 minutes during confirmation.') }}
                </div>
                <button type="submit" class="px-4 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-semibold transition shadow-md shadow-emerald-200/70">
                    {{ __('Complete booking') }}
                </button>
            </div>
            <div id="booking-output" class="text-sm text-slate-600 bg-emerald-50 border border-emerald-100 rounded-xl p-3 hidden" data-booking-output>
                {{ __('Choose service and time, the system will check availability and confirm your appointment.') }}
            </div>
        </form>
      {{--  <div class="p-6 bg-slate-900 text-white rounded-2xl shadow-lg space-y-4">
            <p class="text-sm uppercase tracking-widest text-emerald-200">Náhľad pripomienky</p>
            <div class="rounded-xl bg-slate-800/60 border border-slate-700 p-4 space-y-2">
                <p class="text-slate-200 text-sm">Ahoj, tvoj termín je potvrdený.</p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-400">Služba</span>
                    <span class="font-semibold">Balayage + styling</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-400">Čas</span>
                    <span class="font-semibold">10:00 — 11:30</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-400">Prevádzka</span>
                    <span class="font-semibold">Halo Studio</span>
                </div>
                <p class="text-xs text-slate-400 pt-2">Pripomienka 24h pred termínom + ICS link.</p>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-xl bg-white/10 p-3 border border-white/10">
                    <p class="text-emerald-200 text-xs uppercase">API</p>
                    <p class="font-semibold">/api/availability</p>
                    <p class="text-slate-300 text-xs">kontroluje sloty a zamyká ich</p>
                </div>
                <div class="rounded-xl bg-white/10 p-3 border border-white/10">
                    <p class="text-emerald-200 text-xs uppercase">Notifikácie</p>
                    <p class="font-semibold">Mail + SMS</p>
                    <p class="text-slate-300 text-xs">fronta cez Redis/queue</p>
                </div>
            </div>
        </div>--}}
    </div>
</section>

{{-- @if(isset($latestArticles) && $latestArticles->count() > 0)
<section id="blog" class="py-16 space-y-10 border-t border-slate-100 mt-12">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <p class="text-xs uppercase tracking-widest text-slate-500 font-semibold">Užitočné čítanie</p>
            <h2 class="font-display text-3xl text-slate-900 font-bold">Inšpirujte sa na našom blogu</h2>
            <p class="text-slate-600">Tipy, triky a novinky zo sveta služieb a podnikania.</p>
        </div>
        <a href="{{ route('articles.index') }}" class="inline-flex items-center text-emerald-600 font-semibold hover:underline">
            Všetky články
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($latestArticles as $article)
            <article class="group bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-xl transition-all duration-300">
                <a href="{{ route('articles.show', $article->slug) }}" class="block">
                    @if($article->image_path)
                        <img src="{{ asset('storage/' . $article->image_path) }}" alt="{{ $article->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-48 bg-emerald-50 flex items-center justify-center text-emerald-200">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 3v5h5"/></svg>
                        </div>
                    @endif
                </a>
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded uppercase tracking-wider">{{ $article->category ?? 'Blog' }}</span>
                        <span class="text-slate-400 text-xs">{{ $article->published_at->format('d.m.Y') }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3 leading-tight group-hover:text-emerald-600 transition-colors">
                        <a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a>
                    </h3>
                    <p class="text-slate-600 text-sm mb-4 line-clamp-2">
                        {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 100) }}
                    </p>
                    <a href="{{ route('articles.show', $article->slug) }}" class="inline-flex items-center text-emerald-600 text-sm font-bold">
                        Čítať viac
                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endif --}}
</div>
@endsection
