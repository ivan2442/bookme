@extends('layouts.admin')

@section('content')
<div class="max-w-4xl space-y-6">
    <div>
        <h1 class="font-display text-3xl text-slate-900">Fakturačné údaje systému</h1>
        <p class="text-sm text-slate-500">Tieto údaje sa zobrazujú na faktúrach vydaných prevádzkam (ako dodávateľ).</p>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.billing.settings.store') }}" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-50 pb-2">Základné údaje</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2 md:col-span-2">
                        <label class="label">Obchodné meno / Názov</label>
                        <input type="text" name="name" class="input-control" value="{{ old('name', $billingData['name']) }}" required>
                    </div>
                    <div class="space-y-2">
                        <label class="label">Adresa (Ulica a číslo)</label>
                        <input type="text" name="address" class="input-control" value="{{ old('address', $billingData['address']) }}" required>
                    </div>
                    <div class="space-y-2">
                        <label class="label">Mesto</label>
                        <input type="text" name="city" class="input-control" value="{{ old('city', $billingData['city']) }}" required>
                    </div>
                    <div class="space-y-2">
                        <label class="label">PSČ</label>
                        <input type="text" name="postal_code" class="input-control" value="{{ old('postal_code', $billingData['postal_code']) }}" required>
                    </div>
                    <div class="space-y-2">
                        <label class="label">Krajina</label>
                        <input type="text" name="country" class="input-control" value="{{ old('country', $billingData['country']) }}" required>
                    </div>
                </div>
            </div>

            <div class="space-y-4 pt-6 border-t border-slate-50">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-50 pb-2">Identifikátory</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="label">IČO</label>
                        <input type="text" name="ico" class="input-control" value="{{ old('ico', $billingData['ico']) }}">
                    </div>
                    <div class="space-y-2">
                        <label class="label">DIČ</label>
                        <input type="text" name="dic" class="input-control" value="{{ old('dic', $billingData['dic']) }}">
                    </div>
                    <div class="space-y-2">
                        <label class="label">IČ DPH</label>
                        <input type="text" name="ic_dph" class="input-control" value="{{ old('ic_dph', $billingData['ic_dph']) }}">
                    </div>
                </div>
            </div>

            <div class="space-y-4 pt-6 border-t border-slate-50">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-50 pb-2">Bankové spojenie</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="label">IBAN</label>
                        <input type="text" name="iban" class="input-control" value="{{ old('iban', $billingData['iban']) }}">
                    </div>
                    <div class="space-y-2">
                        <label class="label">SWIFT / BIC</label>
                        <input type="text" name="swift" class="input-control" value="{{ old('swift', $billingData['swift']) }}">
                    </div>
                </div>
            </div>

            <div class="pt-6 flex justify-end">
                <button type="submit" class="px-8 py-3 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                    Uložiť nastavenia
                </button>
            </div>
        </form>
    </div>

    <div class="card mt-10">
        <h2 class="text-xl font-display font-bold text-slate-900 mb-6 flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            Revolut API (Open Banking)
        </h2>

        <form method="POST" action="{{ route('admin.billing.settings.store') }}" class="space-y-6">
            @csrf
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="label">Revolut Client ID</label>
                    <input type="text" name="revolut_client_id" class="input-control" value="{{ old('revolut_client_id', $revolutData['client_id']) }}">
                </div>

                <div class="space-y-2">
                    <label class="label">Revolut JWT (Client Assertion)</label>
                    <textarea name="revolut_jwt" class="input-control" rows="4" placeholder="Vložiť podpísaný JWT token">{{ old('revolut_jwt', $revolutData['jwt']) }}</textarea>
                    <p class="text-[10px] text-slate-400 font-medium italic">JWT musí byť podpísaný vaším súkromným kľúčom registrovaným v Revolut Business portáli.</p>
                </div>

                <div class="space-y-2">
                    <label class="label">Revolut Refresh Token</label>
                    <input type="text" name="revolut_refresh_token" class="input-control" value="{{ old('revolut_refresh_token', $revolutData['refresh_token']) }}">
                </div>
            </div>

            <div class="pt-6 flex justify-end">
                <button type="submit" class="px-8 py-3 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                    Uložiť Revolut nastavenia
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
