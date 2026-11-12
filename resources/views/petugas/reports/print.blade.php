<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $report['meta']['title'] }}</title>
    <style>
        @page { margin: 28px 20px; }
        * { box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; color: #253247; font-size: 11px; }
        .report-header { display: flex; align-items: flex-start; justify-content: space-between; border-bottom: 3px solid #1E3A5F; padding-bottom: 10px; }
        .brand { font-size: 16px; font-weight: bold; color: #1E3A5F; }
        .brand small { display: block; font-size: 9px; font-weight: normal; color: #718096; margin-top: 2px; }
        .report-title { text-align: right; }
        .report-title h1 { font-size: 14px; margin: 0; color: #1E3A5F; }
        .report-title p { margin: 3px 0 0; font-size: 10px; color: #718096; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { border: 1px solid #E5EAF0; padding: 6px 8px; text-align: left; vertical-align: top; }
        thead th { background: #1E3A5F; color: #ffffff; font-size: 10px; text-transform: uppercase; letter-spacing: 0.4px; }
        tbody tr:nth-child(even) { background: #F7F9FC; }
        .report-footer { margin-top: 16px; display: flex; justify-content: space-between; font-size: 10px; color: #718096; }
        .signature { margin-top: 34px; text-align: right; font-size: 11px; }
        .signature .line { display: inline-block; margin-top: 56px; border-top: 1px solid #253247; padding-top: 4px; min-width: 180px; }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="brand">
            {{ \App\Models\Setting::get('app_name', 'SISTEMA') }}
            <small>{{ \App\Models\Setting::get('app_tagline', 'Sistem Peminjaman Alat') }}</small>
        </div>
        <div class="report-title">
            <h1>{{ $report['meta']['title'] }}</h1>
            <p>Periode: {{ $report['meta']['period'] }}</p>
            <p>Dicetak: {{ $report['meta']['generated_at'] }} oleh {{ auth()->user()?->name ?? 'Petugas' }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                @foreach ($report['columns'] as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($report['rows'] as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($report['columns']) + 1 }}" style="text-align:center; padding:18px;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="report-footer">
        <span>Total: {{ $report['total'] }} data</span>
        <span>Dicetak dari sistem SISTEMA</span>
    </div>

    <div class="signature">
        <span>{{ \App\Models\Setting::get('contact_address', '') }}</span><br>
        <span class="line">{{ auth()->user()?->name ?? 'Petugas Inventaris' }}</span>
    </div>

    @if (! request()->routeIs('petugas.reports.pdf'))
        <script>window.addEventListener('load', () => window.print());</script>
    @endif
</body>
</html>