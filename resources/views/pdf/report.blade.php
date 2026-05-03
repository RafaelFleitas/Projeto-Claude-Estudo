<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório #{{ $report->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1a1a1a; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 16px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; color: #1e40af; margin-bottom: 4px; }
        .header p { color: #6b7280; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background: #1e40af; color: #fff; text-align: left; padding: 6px 8px; font-size: 10px; }
        td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) td { background: #f9fafb; }
        .footer { margin-top: 20px; text-align: center; color: #9ca3af; font-size: 9px; }
        .meta { margin-bottom: 16px; font-size: 10px; color: #374151; }
        .meta span { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório — {{ $report->module->label() }}</h1>
        <p>Gerado em {{ now()->format('d/m/Y \à\s H:i') }} — Formato: {{ $report->format->label() }}</p>
    </div>

    <div class="meta">
        <span>Total de registros:</span> {{ count($data) }}
    </div>

    @if (!empty($data))
        <table>
            <thead>
                <tr>
                    @foreach (array_keys($data[0]) as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td>{{ $cell ?? '—' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align:center; color:#6b7280; margin-top:40px;">Nenhum registro encontrado para os filtros selecionados.</p>
    @endif

    <div class="footer">
        Relatório gerado automaticamente pelo Sistema de Gestão de Contratos.
    </div>
</body>
</html>
