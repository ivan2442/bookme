@extends('layouts.app')

@section('content')
<section class="pt-4 lg:pt-16 pb-10 relative">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -left-24 top-10 h-48 w-48 rounded-full bg-emerald-300/40 blur-3xl"></div>
        <div class="absolute right-0 bottom-10 h-56 w-56 rounded-full bg-orange-200/40 blur-3xl"></div>
    </div>
    <div class="relative grid lg:grid-cols-2 gap-10 items-center">
        <div class="space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/80 border border-emerald-100 text-sm shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Rezervácia do 3 minút, bez telefonovania
            </div>
            <h1 class="font-display text-4xl md:text-5xl leading-tight text-slate-900">
                BookMe spája prevádzky a ich klientov.
            </h1>
            <p class="text-lg text-slate-600 max-w-2xl">
                Vyhľadaj prevádzku, vyber službu a čas, potvrď jedným klikom.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="#booking" class="w-full md:w-auto mt-36 mb-0 md:mt-36 text-center px-4 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-lg shadow-slate-300/50 hover:translate-y-[-1px] transition">Začať rezerváciu</a>
                {{-- <a href="#services" class="px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-800 font-semibold hover:border-emerald-200 hover:shadow-md transition">Pozrieť služby</a> --}}
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl">
                <div class="p-4 rounded-2xl bg-white/80 border border-slate-100 shadow-sm">
                    <p class="text-2xl font-semibold text-slate-900">24/7</p>
                    <p class="text-sm text-slate-600">Rezervácie nonstop</p>
                </div>
             {{--   <div class="p-4 rounded-2xl bg-white/80 border border-slate-100 shadow-sm">
                    <p class="text-2xl font-semibold text-slate-900">Bez double-book</p>
                    <p class="text-sm text-slate-600">Sloty sú zamknuté</p>
                </div>--}}
                <div class="p-4 rounded-2xl bg-white/80 border border-slate-100 shadow-sm">
                    <p class="text-2xl font-semibold text-slate-900">Notifikácie</p>
                    <p class="text-sm text-slate-600">E-mail + SMS pripomienky</p>
                </div>
            </div>
        </div>
        {{--<div class="bg-white/80 backdrop-blur rounded-3xl border border-emerald-100/60 shadow-xl shadow-emerald-100/60 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase text-slate-500 tracking-widest">Live náhľad</p>
                    <p class="font-semibold text-lg text-slate-900">Kalendár BookMe</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Demo</span>
            </div>
            <div class="grid grid-cols-2 gap-3" data-demo-slots>
                <div class="slot-card">
                    <p class="font-semibold text-slate-900">10:00</p>
                    <p class="text-xs text-slate-600">voľný slot</p>
                </div>
                <div class="slot-card" data-status="busy">
                    <p class="font-semibold text-slate-900">10:30</p>
                    <p class="text-xs text-slate-600">obsadené čoskoro</p>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <p class="text-sm text-slate-700 mb-2">Klient</p>
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">Meno</span>
                        <span class="font-semibold text-slate-900">Lucia</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">Služba</span>
                        <span class="font-semibold text-slate-900">Balayage + styling</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">Prevádzka</span>
                        <span class="font-semibold text-slate-900">Halo Studio</span>
                    </div>
                </div>
            </div>
            <p class="text-xs text-slate-500">Ďalšie kroky: potvrdenie e-mailom, 24h pripomienka a ICS export.</p>
        </div>--}}
    </div>
</section>

<section id="search" class="py-12 space-y-6">
    <div class="flex items-center gap-3">
        <span class="h-10 w-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 font-semibold">1</span>
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Vyhľadať prevádzku</p>
            <h2 class="font-display text-2xl text-slate-900">Filtrovanie podľa mesta a kategórie</h2>
        </div>
    </div>
    <div class="grid md:grid-cols-[2fr,3fr] gap-6">
        <div class="p-5 bg-white/90 border border-slate-100 rounded-2xl shadow-sm space-y-4">
            <div class="space-y-2">
                <label class="text-sm text-slate-600 font-medium">Mesto</label>
                <input id="filter-city" type="text" placeholder="Bratislava" class="input-control" />
            </div>
            <div class="space-y-2">
                <label class="text-sm text-slate-600 font-medium">Kategória</label>
                <select id="filter-category" class="input-control">
                    <option value="">Všetky</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-sm text-slate-600 font-medium">Kľúčové slovo</label>
                <input id="filter-query" type="text" placeholder="strih, masáž, barber..." class="input-control" />
            </div>
            <p class="text-xs text-slate-500">Filtrování prebieha priamo na stránke, live pri písaní.</p>
        </div>
            <div class="grid sm:grid-cols-2 gap-4" data-shop-list>
                <p class="text-sm text-slate-500">Načítavam prevádzky...</p>
            </div>
    </div>
</section>

<section id="services" class="py-12 space-y-6 hidden">
    <div class="flex items-center gap-3">
        <span class="h-10 w-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-700 font-semibold">2</span>
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Vyber službu</p>
            <h2 class="font-display text-2xl text-slate-900">Služby podľa dĺžky a ceny</h2>
        </div>
    </div>
    <div class="grid md:grid-cols-3 gap-4" data-services-list>
        <p class="text-sm text-slate-500">Načítavam služby...</p>
    </div>
</section>

<section id="booking" class="py-12 space-y-6 hidden">
    <div class="flex items-center gap-3">
        <span class="h-10 w-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-semibold">3</span>
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Potvrď termín</p>
            <h2 class="font-display text-2xl text-slate-900">Rezervácia s pripomienkou</h2>
        </div>
    </div>
    <div class="grid lg:grid-cols-[2fr,1.2fr] gap-6">
        <form class="p-6 bg-white/90 border border-slate-100 rounded-2xl shadow-sm space-y-4" data-booking-form>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="label">Prevádzka</label>
                    <select name="shop_id" class="input-control" required data-shop-select>
                        <option value="">Vyber prevádzku</option>
                    </select>
                </div>
                <div>
                    <label class="label">Služba</label>
                    <select name="service_id" class="input-control" required data-service-select>
                        <option value="">Vyber službu</option>
                    </select>
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-3" data-variant-wrapper>
                <div>
                    <label class="label">Variant</label>
                    <select name="service_variant_id" class="input-control" data-variant-select>
                        <option value="">Vyber variant</option>
                    </select>
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="label">Meno</label>
                    <input name="customer_name" type="text" class="input-control" value="{{ old('customer_name') }}" placeholder="Zadaj meno" required />
                </div>
                <div>
                    <label class="label">E-mail</label>
                    <input name="customer_email" type="email" class="input-control" value="{{ old('customer_email') }}" placeholder="na@priklad.sk" required />
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="label">Telefón</label>
                    <input name="customer_phone" type="text" class="input-control" value="{{ old('customer_phone') }}" placeholder="+421..." />
                </div>
                <div>
                    <label class="label">Dátum</label>
                    <div class="date-calendar" data-calendar>
                        <div class="flex items-center justify-between mb-2">
                            <button type="button" class="cal-nav cal-prev" data-cal-prev>
                                <span class="sr-only">Predchádzajúci týždeň</span>
                                ‹
                            </button>
                            <div class="text-center cal-month" data-cal-month>—</div>
                            <button type="button" class="cal-nav cal-next" data-cal-next>
                                <span class="sr-only">Ďalší týždeň</span>
                                ›
                            </button>
                        </div>
                        <div class="calendar-grid" data-cal-grid>
                            <div class="calendar-heading">po</div>
                            <div class="calendar-heading">ut</div>
                            <div class="calendar-heading">st</div>
                            <div class="calendar-heading">št</div>
                            <div class="calendar-heading">pi</div>
                            <div class="calendar-heading">so</div>
                            <div class="calendar-heading">ne</div>
                        </div>
                    </div>
                    <input name="date" type="hidden" data-date-input value="{{ date('Y-m-d') }}" />
                </div>
            </div>
            <div>
                <label class="label">Čas</label>
                <div class="flex flex-wrap gap-2" data-time-grid>
                    <span class="text-sm text-slate-500" data-time-placeholder>Vyber dátum a variant.</span>
                </div>
                <input type="hidden" name="start_at" data-time-input />
                <input type="hidden" name="employee_id" data-employee-input />
            </div>
            <div>
                <label class="label">Poznámka</label>
                <textarea name="notes" rows="2" class="input-control" placeholder="Upresnenie pre prevádzku"></textarea>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Slot sa zamkne na 3 minúty počas potvrdenia.
                </div>
                <button type="submit" class="px-4 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-semibold transition shadow-md shadow-emerald-200/70">
                    Dokončiť rezerváciu
                </button>
            </div>
            <div id="booking-output" class="text-sm text-slate-600 bg-emerald-50 border border-emerald-100 rounded-xl p-3 hidden" data-booking-output>
                Vyber službu a čas, systém preverí dostupnosť a potvrdí ti termín.
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

@endsection
