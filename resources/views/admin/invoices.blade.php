@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-3xl text-slate-900">Faktúry predplatného</h1>
            <p class="text-sm text-slate-500">Správa faktúr za využívanie systému BookMe.</p>
        </div>
        <button onclick="openAddInvoiceModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-md flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Nová faktúra</span>
        </button>
    </div>

    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('admin.invoices') }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ !$status ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100' }}">
            Vystavené
        </a>
        <a href="{{ route('admin.invoices', ['status' => 'paid']) }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ $status === 'paid' ? 'bg-emerald-500 text-white shadow-lg' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100' }}">
            Uhradené
        </a>
        <a href="{{ route('admin.invoices', ['status' => 'overdue']) }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ $status === 'overdue' ? 'bg-rose-500 text-white shadow-lg' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100' }}">
            Neuhradené po splatnosti
        </a>
    </div>

    <div class="card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-widest font-bold">
                        <th class="px-6 py-4">Číslo faktúry</th>
                        <th class="px-6 py-4">Prevádzka</th>
                        <th class="px-6 py-4">Suma</th>
                        <th class="px-6 py-4">Splatnosť</th>
                        <th class="px-6 py-4">Stav</th>
                        <th class="px-6 py-4 text-right">Akcie</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white/50">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-slate-900 font-bold">{{ $invoice->invoice_number }}</td>
                            <td class="px-6 py-4 text-slate-600 font-medium">{{ $invoice->profile->name }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">€{{ number_format($invoice->amount, 2) }}</td>
                            <td class="px-6 py-4 {{ $invoice->due_at->isPast() && $invoice->status === 'unpaid' ? 'text-rose-600 font-bold' : 'text-slate-500' }}">
                                {{ $invoice->due_at->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-tight
                                    {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($invoice->status === 'unpaid' ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-600') }}">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.invoices.preview', $invoice) }}" target="_blank" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition shadow-sm" title="Náhľad">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    <form action="{{ route('admin.invoices.send', $invoice) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition shadow-sm" title="Odoslať emailom">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </button>
                                    </form>

                                    @if($invoice->status === 'unpaid')
                                        <form action="{{ route('admin.invoices.status.update', $invoice) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="paid">
                                            <button type="submit" class="p-2 rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition shadow-sm" title="Označiť ako uhradené">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.invoices.delete', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Naozaj chcete odstrániť túto faktúru?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-slate-100 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition shadow-sm" title="Odstrániť">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
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
        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>

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
                        <input type="date" name="due_at" class="input-control" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required data-allow-past>
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
