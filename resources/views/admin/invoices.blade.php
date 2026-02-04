@extends('layouts.app')

@section('content')
<section class="pt-12 pb-6 space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500">Admin</p>
            <h1 class="font-display text-3xl text-slate-900">Faktúry predplatného</h1>
        </div>
        <button onclick="openAddInvoiceModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Nová faktúra</span>
        </button>
    </div>

    @include('admin.partials.nav')

    @if(session('status'))
        <div class="card border-emerald-200 bg-emerald-50 text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-widest font-bold">
                        <th class="px-6 py-4">Číslo faktúry</th>
                        <th class="px-6 py-4">Prevádzka</th>
                        <th class="px-6 py-4">Suma</th>
                        <th class="px-6 py-4">Splatnosť</th>
                        <th class="px-6 py-4">Stav</th>
                        <th class="px-6 py-4 text-right">Akcie</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-sm text-slate-900">{{ $invoice->invoice_number }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $invoice->profile->name }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">€{{ number_format($invoice->amount, 2) }}</td>
                            <td class="px-6 py-4 text-sm {{ $invoice->due_at->isPast() && $invoice->status === 'unpaid' ? 'text-rose-600 font-bold' : 'text-slate-500' }}">
                                {{ $invoice->due_at->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase
                                    {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($invoice->status === 'unpaid' ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-600') }}">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($invoice->status === 'unpaid')
                                    <form action="{{ route('admin.invoices.status.update', $invoice) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="paid">
                                        <button type="submit" class="text-xs font-bold text-emerald-600 hover:underline">Označiť ako uhradené</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 italic">
                                Žiadne faktúry neboli nájdené.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-slate-100">
            {{ $invoices->links() }}
        </div>
    </div>
</section>

<!-- Add Invoice Modal -->
<div id="addInvoiceModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeAddInvoiceModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
            <h3 class="text-xl font-bold text-slate-900 mb-6">Vytvoriť faktúru</h3>
            <form method="POST" action="{{ route('admin.invoices.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="label">Prevádzka</label>
                    <select name="profile_id" class="input-control" required>
                        @foreach($profiles as $profile)
                            <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Suma (€)</label>
                        <input type="number" step="0.01" name="amount" class="input-control" value="20.00" required>
                    </div>
                    <div>
                        <label class="label">Splatnosť</label>
                        <input type="date" name="due_at" class="input-control" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required>
                    </div>
                </div>
                <div>
                    <label class="label">Číslo faktúry (voliteľné)</label>
                    <input type="text" name="invoice_number" class="input-control" placeholder="automaticky">
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeAddInvoiceModal()" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">Zrušiť</button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg">Vytvoriť faktúru</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddInvoiceModal() {
        document.getElementById('addInvoiceModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeAddInvoiceModal() {
        document.getElementById('addInvoiceModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
</script>
@endsection
