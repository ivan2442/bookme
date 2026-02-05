@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Admin</p>
            <h1 class="font-display text-3xl text-slate-900">Fakturačné údaje firmy</h1>
        </div>
        <span class="badge">BookMe s.r.o.</span>
    </div>

    @include('admin.partials.nav')

    @if(session('status'))
        <div class="card border-emerald-200 bg-emerald-50 text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="card max-w-2xl">
        <form method="POST" action="{{ route('admin.billing.settings.store') }}" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-900 border-b pb-2">Základné údaje</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div class="space-y-1">
                        <label class="label">Obchodné meno / Názov</label>
                        <input type="text" name="name" class="input-control" value="{{ old('name', $billingData['name']) }}" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label">Ulica a číslo</label>
                        <input type="text" name="address" class="input-control" value="{{ old('address', $billingData['address']) }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="label">Mesto</label>
                        <input type="text" name="city" class="input-control" value="{{ old('city', $billingData['city']) }}" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label">PSČ</label>
                        <input type="text" name="postal_code" class="input-control" value="{{ old('postal_code', $billingData['postal_code']) }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="label">Krajina</label>
                        <input type="text" name="country" class="input-control" value="{{ old('country', $billingData['country']) }}" required>
                    </div>
                </div>
            </div>

            <div class="space-y-4 pt-4">
                <h3 class="text-lg font-bold text-slate-900 border-b pb-2">Identifikátory</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="label">IČO</label>
                        <input type="text" name="ico" class="input-control" value="{{ old('ico', $billingData['ico']) }}">
                    </div>
                    <div class="space-y-1">
                        <label class="label">DIČ</label>
                        <input type="text" name="dic" class="input-control" value="{{ old('dic', $billingData['dic']) }}">
                    </div>
                    <div class="space-y-1">
                        <label class="label">IČ DPH</label>
                        <input type="text" name="ic_dph" class="input-control" value="{{ old('ic_dph', $billingData['ic_dph']) }}">
                    </div>
                </div>
            </div>

            <div class="space-y-4 pt-4">
                <h3 class="text-lg font-bold text-slate-900 border-b pb-2">Bankové spojenie</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label">IBAN</label>
                        <input type="text" name="iban" class="input-control" value="{{ old('iban', $billingData['iban']) }}">
                    </div>
                    <div class="space-y-1">
                        <label class="label">SWIFT / BIC</label>
                        <input type="text" name="swift" class="input-control" value="{{ old('swift', $billingData['swift']) }}">
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
</section>
@endsection
