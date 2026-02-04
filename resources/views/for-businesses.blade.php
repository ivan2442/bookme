@extends('layouts.app')

@section('content')
<div class="overflow-x-hidden">
    <!-- Hero Section -->
    <section class="pt-12 pb-20 relative">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-100/50 blur-3xl"></div>
            <div class="absolute right-0 bottom-10 h-72 w-72 rounded-full bg-blue-50/50 blur-3xl"></div>
        </div>

        <div class="relative max-w-4xl mx-auto text-center space-y-8">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-bold shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Premeňte svoje podnikanie na digitálne
            </div>
            <h1 class="text-4xl md:text-6xl font-display font-bold text-slate-900 leading-tight">
                Získajte viac času na to, <br/>
                <span class="text-emerald-500">čo robíte najlepšie</span>
            </h1>
            <p class="text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
                BookMe je komplexný rezervačný systém, ktorý za vás vybaví termíny, pripomenie sa klientom a poskytne vám dokonalý prehľad o vašej prevádzke.
            </p>
            <div class="inline-flex flex-col sm:flex-row items-center gap-2 sm:gap-4 px-6 py-3 rounded-2xl bg-white/80 backdrop-blur-sm border border-emerald-100 shadow-sm mx-auto">
                <div class="flex items-center gap-2">
                    <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <p class="text-slate-900 font-bold">Prvé 3 mesiace <span class="text-emerald-500">úplne zadarmo</span></p>
                </div>
                <span class="hidden sm:block text-emerald-200">|</span>
                <p class="text-slate-500 text-sm font-semibold">potom iba 20 € mesačne</p>
            </div>
            <div class="flex justify-center">
                <a href="#register" class="px-10 py-5 rounded-2xl bg-slate-900 text-white font-bold text-lg shadow-2xl shadow-slate-200 hover:bg-slate-800 hover:-translate-y-1 transition-all">
                    Zaregistrovať prevádzku zadarmo
                </a>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="py-20 grid md:grid-cols-3 gap-8">
        <div class="card p-8 space-y-4 hover:border-emerald-200 transition-colors">
            <div class="h-14 w-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900">Automatický kalendár</h3>
            <p class="text-slate-600">Zabudnite na papierové diáre. Váš kalendár je prístupný 24/7 pre vás aj vašich klientov.</p>
        </div>
        <div class="card p-8 space-y-4 hover:border-blue-200 transition-colors">
            <div class="h-14 w-14 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900">SMS & E-mail notifikácie</h3>
            <p class="text-slate-600">Znížte počet "no-show" termínov vďaka automatickým pripomienkam pre vašich zákazníkov.</p>
        </div>
        <div class="card p-8 space-y-4 hover:border-purple-200 transition-colors">
            <div class="h-14 w-14 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900">Prehľadné štatistiky</h3>
            <p class="text-slate-600">Sledujte svoje tržby, vyťaženosť zamestnancov a rast vášho podnikania v reálnom čase.</p>
        </div>
    </section>

    <!-- Registration Section -->
    <section id="register" class="py-20 bg-white rounded-[48px] shadow-2xl shadow-slate-200/50 border border-slate-50 p-8 md:p-16 mb-20 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 h-64 w-64 rounded-full bg-emerald-500/5 blur-3xl"></div>

        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-8">
                <div>
                    <h2 class="text-3xl font-display font-bold text-slate-900 mb-4">Začnite ešte dnes</h2>
                    <p class="text-slate-600">Vyplňte základné údaje o vašej prevádzke a my vás prevedieme zvyškom procesu. Registrácia trvá menej ako 2 minúty.</p>
                </div>

                <ul class="space-y-4">
                    <li class="flex items-center gap-3 text-slate-700">
                        <div class="h-6 w-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Vlastný unikátny rezervačný odkaz</span>
                    </li>
                    <li class="flex items-center gap-3 text-slate-700">
                        <div class="h-6 w-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Správa služieb a zamestnancov</span>
                    </li>
                    <li class="flex items-center gap-3 text-slate-700">
                        <div class="h-6 w-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Kompletná história platieb</span>
                    </li>
                </ul>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-100">
                    <p class="text-sm italic text-slate-500">"Odkedy používame BookMe, počet telefonátov klesol o 70% a máme oveľa viac času na prácu."</p>
                    <div class="mt-4 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xs">B</div>
                        <div>
                            <p class="text-sm font-bold text-slate-900">Peter, Barber Shop</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <form action="{{ route('auth.register.business') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="label">Názov prevádzky</label>
                            <input type="text" name="business_name" class="input-control" placeholder="Napr. Štúdio Krásy" required value="{{ old('business_name') }}">
                        </div>
                        <div class="space-y-1">
                            <label class="label">Kategória</label>
                            <input type="text" name="category" class="input-control" placeholder="Napr. Kaderníctvo" required value="{{ old('category') }}">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="label">Mesto</label>
                        <input type="text" name="city" class="input-control" placeholder="Kde pôsobíte?" required value="{{ old('city') }}">
                    </div>

                    <div class="border-t border-slate-100 my-6 pt-6">
                        <p class="text-xs uppercase font-bold text-slate-400 mb-4">Kontaktná osoba a prístup</p>

                        <div class="space-y-4">
                            <div class="space-y-1">
                                <label class="label">Meno a priezvisko</label>
                                <input type="text" name="name" class="input-control" placeholder="Meno majiteľa / manažéra" required value="{{ old('name') }}">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="label">E-mail</label>
                                    <input type="email" name="email" class="input-control" placeholder="vas@email.sk" required value="{{ old('email') }}">
                                </div>
                                <div class="space-y-1">
                                    <label class="label">Telefón</label>
                                    <input type="text" name="phone" class="input-control" placeholder="+421..." value="{{ old('phone') }}">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="label">Heslo</label>
                                    <input type="password" name="password" class="input-control" placeholder="Minimálne 8 znakov" required>
                                </div>
                                <div class="space-y-1">
                                    <label class="label">Potvrdenie hesla</label>
                                    <input type="password" name="password_confirmation" class="input-control" placeholder="Zopakujte heslo" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 rounded-2xl bg-emerald-500 text-white font-bold text-lg hover:bg-emerald-600 transition shadow-lg shadow-emerald-200/50 mt-4">
                        Vytvoriť účet a začať
                    </button>

                    <p class="text-[11px] text-center text-slate-400 mt-4">
                        Kliknutím na tlačidlo súhlasíte s obchodnými podmienkami a spracovaním osobných údajov.
                    </p>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
