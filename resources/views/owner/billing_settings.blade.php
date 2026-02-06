@extends('layouts.owner')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div>
            <h1 class="font-display text-3xl text-slate-900">{{ __('Billing details') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Details for issuing invoices for using the BookMe system.') }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-1 gap-6">
        @foreach($profiles as $profile)
            <div class="card max-w-3xl p-6">
                <div class="border-b border-slate-50 pb-4 mb-6">
                    <h2 class="text-xl font-bold text-slate-900">{{ $profile->name }}</h2>
                    <p class="text-sm text-slate-500">{{ __('Enter the billing details for this business, which will be displayed on your subscription invoices.') }}</p>
                </div>

                <form method="POST" action="{{ route('owner.billing.settings.store') }}" class="space-y-8">
                    @csrf
                    <input type="hidden" name="profile_id" value="{{ $profile->id }}">

                    <div class="space-y-4">
                        <h3 class="text-xs uppercase font-bold text-slate-400 tracking-widest">{{ __('Company / Subscriber') }}</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="space-y-1">
                                <label class="label text-[10px]">{{ __('Business name / Company name') }}</label>
                                <input type="text" name="billing_name" class="input-control" value="{{ old('billing_name', $profile->billing_name) }}" placeholder="{{ __('Your company name') }}" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="label text-[10px]">{{ __('Street and number') }}</label>
                                <input type="text" name="billing_address" class="input-control" value="{{ old('billing_address', $profile->billing_address) }}" placeholder="{{ __('Street 123') }}" required>
                            </div>
                            <div class="space-y-1">
                                <label class="label text-[10px]">{{ __('City') }}</label>
                                <input type="text" name="billing_city" class="input-control" value="{{ old('billing_city', $profile->billing_city) }}" placeholder="{{ __('City') }}" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="label text-[10px]">{{ __('Postal code') }}</label>
                                <input type="text" name="billing_postal_code" class="input-control" value="{{ old('billing_postal_code', $profile->billing_postal_code) }}" placeholder="811 01" required>
                            </div>
                            <div class="space-y-1">
                                <label class="label text-[10px]">{{ __('Country') }}</label>
                                <input type="text" name="billing_country" class="input-control" value="{{ old('billing_country', $profile->billing_country) }}" placeholder="{{ __('Country') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-6 border-t border-slate-50">
                        <h3 class="text-xs uppercase font-bold text-slate-400 tracking-widest">{{ __('Identifiers') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label class="label text-[10px]">{{ __('IČO') }}</label>
                                <input type="text" name="billing_ico" class="input-control" value="{{ old('billing_ico', $profile->billing_ico) }}" placeholder="12345678">
                            </div>
                            <div class="space-y-1">
                                <label class="label text-[10px]">{{ __('DIČ') }}</label>
                                <input type="text" name="billing_dic" class="input-control" value="{{ old('billing_dic', $profile->billing_dic) }}" placeholder="1234567890">
                            </div>
                            <div class="space-y-1">
                                <label class="label text-[10px]">{{ __('IČ DPH') }}</label>
                                <input type="text" name="billing_ic_dph" class="input-control" value="{{ old('billing_ic_dph', $profile->billing_ic_dph) }}" placeholder="SK1234567890">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-6 border-t border-slate-50">
                        <h3 class="text-xs uppercase font-bold text-slate-400 tracking-widest">{{ __('Bank connection (optional)') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="label text-[10px]">{{ __('IBAN') }}</label>
                                <input type="text" name="billing_iban" class="input-control" value="{{ old('billing_iban', $profile->billing_iban) }}" placeholder="SK12...">
                            </div>
                            <div class="space-y-1">
                                <label class="label text-[10px]">{{ __('SWIFT / BIC') }}</label>
                                <input type="text" name="billing_swift" class="input-control" value="{{ old('billing_swift', $profile->billing_swift) }}" placeholder="GIBA...">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full px-6 py-3 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-200/50">
                            {{ __('Save billing details') }}
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
