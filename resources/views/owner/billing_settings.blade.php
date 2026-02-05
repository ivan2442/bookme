@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Prevádzka</p>
            <h1 class="font-display text-3xl text-slate-900">Fakturačné údaje</h1>
        </div>
        <span class="badge">Moje údaje</span>
    </div>

    @include('owner.partials.nav')

    @if(session('status'))
        <div class="card border-emerald-200 bg-emerald-50 text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="grid md:grid-cols-1 gap-6">
        @foreach($profiles as $profile)
            <div class="card max-w-2xl">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="text-xl font-bold text-slate-900">{{ $profile->name }}</h2>
                    <p class="text-sm text-slate-500">Zadajte fakturačné údaje pre túto prevádzku, ktoré sa budú zobrazovať na vašich faktúrach za predplatné.</p>
                </div>

                <form method="POST" action="{{ route('owner.billing.settings.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="profile_id" value="{{ $profile->id }}">

                    <div class="space-y-4">
                        <h3 class="text-sm uppercase font-bold text-slate-400 tracking-wider">Firma / Odberateľ</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="space-y-1">
                                <label class="label">Obchodné meno / Názov firmy</label>
                                <input type="text" name="billing_name" class="input-control" value="{{ old('billing_name', $profile->billing_name) }}" placeholder="Názov vašej firmy" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="label">Ulica a číslo</label>
                                <input type="text" name="billing_address" class="input-control" value="{{ old('billing_address', $profile->billing_address) }}" placeholder="Ulica 123" required>
                            </div>
                            <div class="space-y-1">
                                <label class="label">Mesto</label>
                                <input type="text" name="billing_city" class="input-control" value="{{ old('billing_city', $profile->billing_city) }}" placeholder="Bratislava" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="label">PSČ</label>
                                <input type="text" name="billing_postal_code" class="input-control" value="{{ old('billing_postal_code', $profile->billing_postal_code) }}" placeholder="811 01" required>
                            </div>
                            <div class="space-y-1">
                                <label class="label">Krajina</label>
                                <input type="text" name="billing_country" class="input-control" value="{{ old('billing_country', $profile->billing_country) }}" placeholder="Slovensko" required>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4">
                        <h3 class="text-sm uppercase font-bold text-slate-400 tracking-wider">Identifikátory</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label class="label">IČO</label>
                                <input type="text" name="billing_ico" class="input-control" value="{{ old('billing_ico', $profile->billing_ico) }}" placeholder="12345678">
                            </div>
                            <div class="space-y-1">
                                <label class="label">DIČ</label>
                                <input type="text" name="billing_dic" class="input-control" value="{{ old('billing_dic', $profile->billing_dic) }}" placeholder="1234567890">
                            </div>
                            <div class="space-y-1">
                                <label class="label">IČ DPH</label>
                                <input type="text" name="billing_ic_dph" class="input-control" value="{{ old('billing_ic_dph', $profile->billing_ic_dph) }}" placeholder="SK1234567890">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4">
                        <h3 class="text-sm uppercase font-bold text-slate-400 tracking-wider">Bankové spojenie (voliteľné)</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="label">IBAN</label>
                                <input type="text" name="billing_iban" class="input-control" value="{{ old('billing_iban', $profile->billing_iban) }}" placeholder="SK12...">
                            </div>
                            <div class="space-y-1">
                                <label class="label">SWIFT / BIC</label>
                                <input type="text" name="billing_swift" class="input-control" value="{{ old('billing_swift', $profile->billing_swift) }}" placeholder="GIBA...">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full px-6 py-3 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition shadow-lg">
                            Uložiť fakturačné údaje
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</section>
@endsection
