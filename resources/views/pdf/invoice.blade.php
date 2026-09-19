<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; color: #333; }
        .top { width: 100%; border-bottom: 3px solid #2563eb; padding-bottom: 14px; }
        .brand { font-size: 22px; font-weight: bold; color: #2563eb; }
        .invoice-title { text-align: right; font-size: 18px; color: #1e293b; }
        .invoice-title small { display: block; font-size: 11px; color: #64748b; }
        .from-to { width: 100%; margin: 18px 0; }
        .from-to td { vertical-align: top; padding: 4px 0; font-size: 11px; }
        .from-to .header { font-weight: bold; color: #64748b; text-transform: uppercase; font-size: 10px; }
        table.items { width: 100%; border-collapse: collapse; }
        table.items th {
            background: #2563eb; color: #fff; padding: 7px 8px; text-align: left;
            font-size: 10px; text-transform: uppercase;
        }
        table.items td { border-bottom: 1px solid #e2e8f0; padding: 7px 8px; }
        table.items td.num, table.items th.num { text-align: right; }
        .totals { width: 280px; margin-left: auto; margin-top: 14px; }
        .totals td { padding: 4px 8px; font-size: 11px; }
        .totals .grand { font-weight: bold; font-size: 13px; color: #1e293b; border-top: 2px solid #2563eb; }
        .notes { margin-top: 22px; font-size: 11px; }
        .footer { margin-top: 28px; text-align: center; font-size: 10px; color: #94a3b8; }
    </style>
</head>
<body>
    <table class="top">
        <tr>
            <td>
                <div class="brand">{{ config('app.name') }}</div>
                <div style="font-size:11px;color:#64748b;">Invoice Management System</div>
            </td>
            <td class="invoice-title">
                INVOICE
                <small>Nomor: {{ $invoice->number }}</small>
            </td>
        </tr>
    </table>

    <table class="from-to">
        <tr>
            <td width="50%">
                <div class="header">Dibuat untuk (Bill To)</div>
                <div><strong>{{ $invoice->customer_name ?? '-' }}</strong></div>
                <div>{{ $invoice->customer_company ?? '' }}</div>
                <div>{{ $invoice->customer_email ?? '' }}</div>
                <div>{{ $invoice->customer_phone ?? '' }}</div>
            </td>
            <td>
                <div class="header">Detail</div>
                <div>Tanggal Terbit : {{ $invoice->issue_date?->format('d M Y') }}</div>
                <div>Jatuh Tempo   : {{ $invoice->due_date?->format('d M Y') ?? '-' }}</div>
                <div>Status        : {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</div>
                <div>Pembayaran    : {{ ucfirst($invoice->payment_status) }}</div>
            </td>
        </tr>
    </table>

    @if (! empty($invoice->items))
        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="num">Qty</th>
                    <th class="num">Harga Satuan</th>
                    <th class="num">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $item['name'] ?? '-' }}</td>
                        <td class="num">{{ $item['quantity'] ?? 0 }}</td>
                        <td class="num">{{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p><em>Tidak ada item pada invoice ini.</em></p>
    @endif

    <table class="totals">
        <tr><td>Subtotal</td><td class="num">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>Tax</td><td class="num">{{ $invoice->currency }} {{ number_format($invoice->tax, 0, ',', '.') }}</td></tr>
        <tr><td>Discount</td><td class="num">{{ $invoice->currency }} {{ number_format($invoice->discount, 0, ',', '.') }}</td></tr>
        <tr><td>Dibayar</td><td class="num">{{ $invoice->currency }} {{ number_format($invoice->amount_paid, 0, ',', '.') }}</td></tr>
        <tr class="grand">
            <td>Total Tagihan</td>
            <td class="num">{{ $invoice->currency }} {{ number_format($invoice->balance_due, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if ($invoice->notes)
        <div class="notes">
            <strong>Catatan:</strong><br>
            {!! nl2br(e($invoice->notes)) !!}
        </div>
    @endif

    <div class="footer">
        Terima kasih atas kerja samanya. Dokumen ini dibuat otomatis oleh {{ config('app.name') }}.
    </div>
</body>
</html>