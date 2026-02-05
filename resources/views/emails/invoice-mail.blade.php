<x-mail::message>
# Faktúra za predplatné BookMe

Dobrý deň,

zasielame Vám faktúru č. **{{ $invoice->invoice_number }}** za využívanie systému BookMe.

**Suma k úhrade:** €{{ number_format($invoice->amount, 2) }}
**Dátum splatnosti:** {{ $invoice->due_at->format('d.m.Y') }}

Faktúru si môžete prezrieť a stiahnuť v náhľade kliknutím na tlačidlo nižšie:

<x-mail::button :url="route('admin.invoices.preview', $invoice)">
Zobraziť faktúru
</x-mail::button>

Ak ste už faktúru uhradili, považujte tento e-mail za bezpredmetný.

Ďakujeme,<br>
Tím {{ config('app.name') }}
</x-mail::message>
