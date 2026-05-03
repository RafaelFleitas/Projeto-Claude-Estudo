<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Contrato #{{ $contract->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1a1a1a; padding: 40px; }
        .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-size: 22px; color: #1e40af; margin-bottom: 4px; }
        .header p { color: #6b7280; font-size: 11px; }
        .section { margin-bottom: 24px; }
        .section-title { font-size: 13px; font-weight: bold; color: #1e40af; border-bottom: 1px solid #dbeafe; padding-bottom: 6px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .field-row { display: flex; margin-bottom: 8px; }
        .field-label { width: 160px; font-weight: bold; color: #374151; flex-shrink: 0; }
        .field-value { color: #1f2937; flex: 1; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .status-pending   { background: #fef9c3; color: #854d0e; }
        .status-active    { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .qr-section { text-align: center; margin-top: 40px; padding: 20px; border: 1px dashed #93c5fd; border-radius: 8px; }
        .qr-section svg { width: 140px; height: 140px; }
        .qr-section .qr-title { font-size: 13px; font-weight: bold; color: #1e40af; margin-bottom: 8px; }
        .qr-section .validation-code { font-size: 10px; color: #6b7280; margin-top: 8px; word-break: break-all; }
        .footer { margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 12px; text-align: center; color: #9ca3af; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px 8px; }
        .info-table td:first-child { font-weight: bold; color: #374151; width: 160px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Contrato — {{ $contract->contrato }}</h1>
        <p>Gerado em {{ now()->format('d/m/Y \à\s H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Informações do Contrato</div>
        <table class="info-table">
            <tr>
                <td>Contrato</td>
                <td>{{ $contract->contrato }}</td>
            </tr>
            <tr>
                <td>Nº Relatório</td>
                <td>{{ $contract->numero_relatorio ?? '—' }}</td>
            </tr>
            <tr>
                <td>Projeto</td>
                <td>{{ $contract->projeto ?? '—' }}</td>
            </tr>
            <tr>
                <td>Task Azure</td>
                <td>{{ $contract->task_azure ?? '—' }}</td>
            </tr>
            <tr>
                <td>Nota Fiscal</td>
                <td>{{ $contract->nota_fiscal ?? '—' }}</td>
            </tr>
            <tr>
                <td>Valor Total</td>
                <td>R$ {{ $contract->valor_total ? number_format($contract->valor_total, 2, ',', '.') : '—' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <span class="status-badge status-{{ $contract->status->value }}">
                        {{ $contract->status->label() }}
                    </span>
                </td>
            </tr>
            <tr>
                <td>Criado Por</td>
                <td>{{ $contract->user?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td>Criado Em</td>
                <td>{{ $contract->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <div class="qr-section">
        <div class="qr-title">Verificação de Autenticidade</div>
        {!! $qrCodeSvg !!}
        <div class="validation-code">
            Código de validação: {{ $validationCode ?? '' }}<br>
            <small>{{ $validationUrl }}</small>
        </div>
    </div>

    <div class="footer">
        Este documento é gerado automaticamente e possui código de validação único.<br>
        Escaneie o QR Code para verificar a autenticidade deste contrato.
    </div>
</body>
</html>
