<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktúra {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 40px; font-size: 14px; }
        .invoice-box { max-width: 800px; margin: auto; }
        .flex { display: flex; justify-content: space-between; }
        .col { flex: 1; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .mt-4 { margin-top: 20px; }
        .mt-8 { margin-top: 40px; }
        .header { border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #10b981; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f9fafb; text-align: left; padding: 12px; border-bottom: 1px solid #eee; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .total { font-size: 18px; margin-top: 20px; border-top: 2px solid #eee; padding-top: 10px; }
        .footer { margin-top: 50px; font-size: 12px; color: #666; border-top: 1px solid #eee; padding-top: 10px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Vytlačiť faktúru (PDF)</button>
    </div>

    <div class="invoice-box">
        <div class="header flex">
            <div class="col">
                <h1>BookMe</h1>
                <p>Online rezervačný systém</p>
            </div>
            <div class="col text-right">
                <h2 style="margin:0">FAKTÚRA</h2>
                <p class="bold">{{ $invoice->invoice_number }}</p>
            </div>
        </div>

        <div class="flex mt-8">
            <div class="col">
                <p class="bold">Dodávateľ:</p>
                <p>{{ $ourBilling['name'] ?? 'BookMe s.r.o.' }}</p>
                <p>{{ $ourBilling['address'] ?? '' }}</p>
                <p>{{ $ourBilling['postal_code'] ?? '' }} {{ $ourBilling['city'] ?? '' }}</p>
                <p>{{ $ourBilling['country'] ?? '' }}</p>
                <p class="mt-4">
                    IČO: {{ $ourBilling['ico'] ?? '-' }}<br>
                    DIČ: {{ $ourBilling['dic'] ?? '-' }}<br>
                    IČ DPH: {{ $ourBilling['ic_dph'] ?? '-' }}
                </p>
            </div>
            <div class="col" style="margin-left: 40px;">
                <p class="bold">Odberateľ:</p>
                <p>{{ $invoice->profile->billing_name ?: $invoice->profile->name }}</p>
                <p>{{ $invoice->profile->billing_address ?: $invoice->profile->address_line1 }}</p>
                <p>{{ $invoice->profile->billing_postal_code ?: $invoice->profile->postal_code }} {{ $invoice->profile->billing_city ?: $invoice->profile->city }}</p>
                <p>{{ $invoice->profile->billing_country ?: $invoice->profile->country }}</p>
                <p class="mt-4">
                    IČO: {{ $invoice->profile->billing_ico ?: '-' }}<br>
                    DIČ: {{ $invoice->profile->billing_dic ?: '-' }}<br>
                    IČ DPH: {{ $invoice->profile->billing_ic_dph ?: '-' }}
                </p>
            </div>
        </div>

        <div class="mt-8" style="background: #f9fafb; padding: 20px; border-radius: 8px;">
            <div class="flex">
                <div class="col">
                    <p>Dátum vystavenia: <span class="bold">{{ $invoice->created_at->format('d.m.Y') }}</span></p>
                    <p>Dátum splatnosti: <span class="bold">{{ $invoice->due_at->format('d.m.Y') }}</span></p>
                </div>
                <div class="col text-right">
                    <p>Spôsob úhrady: <span class="bold">Prevodom na účet</span></p>
                    <p>Variabilný symbol: <span class="bold">{{ preg_replace('/[^0-9]/', '', $invoice->invoice_number) }}</span></p>
                </div>
            </div>
            <div class="mt-4">
                <p>IBAN: <span class="bold">{{ $ourBilling['iban'] ?? '-' }}</span></p>
                <p>SWIFT: <span class="bold">{{ $ourBilling['swift'] ?? '-' }}</span></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Popis položky</th>
                    <th class="text-right">Suma</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Predplatné BookMe - {{ $invoice->profile->name }}<br>
                        <small style="color: #666">Obdobie: {{ $invoice->created_at->format('m/Y') }}</small>
                    </td>
                    <td class="text-right bold">€{{ number_format($invoice->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total flex">
            <div class="col"></div>
            <div class="col text-right">
                <p>Celkom k úhrade:</p>
                <p class="bold" style="font-size: 24px; color: #10b981;">€{{ number_format($invoice->amount, 2) }}</p>
            </div>
        </div>

        <div class="footer">
            <p>Faktúra slúži zároveň ako dodací list. Nie sme platcami DPH (ak nie je uvedené inak).</p>
            <p>Ďakujeme, že využívate systém BookMe!</p>
        </div>
    </div>
</body>
</html>
