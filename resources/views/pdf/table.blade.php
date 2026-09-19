<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; color: #333; }
        .header { border-bottom: 3px solid #2563eb; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 20px; color: #1e293b; }
        .header p { margin: 4px 0 0; font-size: 11px; color: #64748b; }
        .meta { width: 100%; margin-bottom: 14px; table-layout: auto; }
        .meta td { padding: 2px 0; font-size: 11px; }
        .meta td.label { color: #64748b; width: 120px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th {
            background: #2563eb; color: #fff; padding: 7px 8px; text-align: left;
            font-size: 10px; text-transform: uppercase; letter-spacing: 0.4px;
        }
        table.data td {
            border-bottom: 1px solid #e2e8f0; padding: 7px 8px; font-size: 11px;
        }
        table.data tr:nth-child(even) { background: #f8fafc; }
        .footer { margin-top: 24px; font-size: 10px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated {{ now()->format('d M Y H:i') }} via {{ config('app.name') }}</p>
    </div>

    @if (! empty($meta))
        <table class="meta">
            @foreach ($meta as $label => $value)
                <tr>
                    <td class="label">{{ $label }}</td>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    @if (! empty($rows))
        <table class="data">
            <thead>
                <tr>
                    @foreach ($headings as $heading)
                        <th>{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($headings as $index => $heading)
                            <td>{{ is_array($row) ? ($row[$index] ?? '') : $row }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p><em>Tidak ada data untuk ditampilkan.</em></p>
    @endif

    <div class="footer">
        {{ config('app.name') }} — {{ now()->format('Y') }}. Dokumen ini dibuat otomatis oleh sistem.
    </div>
</body>
</html>