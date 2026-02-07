@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-display text-3xl text-slate-900">Správa API pre prevádzky</h1>
        <p class="text-sm text-slate-500">Tu môžete povoliť konkrétne API integrácie pre jednotlivé prevádzky.</p>
    </div>

    <form method="POST" action="{{ route('admin.api.settings.update') }}">
        @csrf
        <div class="card overflow-hidden !p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold">
                            <th class="px-6 py-4">Prevádzka</th>
                            <th class="px-6 py-4">Majiteľ</th>
                            <th class="px-6 py-4 text-center">Pakavoz API</th>
                            {{-- Tu môžete pridať ďalšie API v budúcnosti --}}
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white/50">
                        @foreach($profiles as $profile)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900 leading-tight">{{ $profile->name }}</p>
                                    <p class="text-[11px] text-slate-500 font-medium">{{ $profile->city }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-slate-700 font-medium">{{ $profile->owner->name ?? '—' }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $profile->owner->email ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer justify-center">
                                        <input type="checkbox" name="apis[{{ $profile->id }}][]" value="pakavoz" class="sr-only peer" {{ $profile->isApiAvailable('pakavoz') ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-200/50">
                Uložiť nastavenia API
            </button>
        </div>
    </form>
</div>
@endsection
