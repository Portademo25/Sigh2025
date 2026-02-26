<!DOCTYPE html>
<html>
<head>
    <title>Comprobante ARC - {{ $trabajador->cedper }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-weight: bold; font-size: 14px; text-decoration: underline; }
        .table-datos { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-datos td { padding: 5px; border: 1px solid #000; }
        .footer { margin-top: 50px; text-align: center; }
        .sign-box { border-top: 1px solid #000; width: 200px; margin: 0 auto; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <strong>{{ $empresa }}</strong><br>
        <strong>COMPROBANTE DE RETENCIÓN DE I.S.L.R. (ARC)</strong><br>
        <span>AÑO FISCAL: {{ $ano }}</span>
    </div>

    <table class="table-datos">
        <tr>
            <td colspan="2"><strong>NOMBRE O RAZÓN SOCIAL DEL AGENTE DE RETENCIÓN:</strong><br>{{ $empresa }}</td>
        </tr>
        <tr>
            <td><strong>APELLIDOS Y NOMBRES DEL BENEFICIARIO:</strong><br>{{ $trabajador->nomper }} {{ $trabajador->apeper }}</td>
            <td><strong>CÉDULA DE IDENTIDAD:</strong><br>{{ $trabajador->cedper }}</td>
        </tr>
    </table>

    <table class="table-datos" style="text-align: right;">
        <thead>
            <tr style="background-color: #eee;">
                <th style="text-align: left;">DESCRIPCIÓN</th>
                <th>TOTALES Bs.</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: left;">Total Remuneraciones Pagadas o Abonadas en Cuenta</td>
                <td>{{ number_format($movimientos->total_sueldo ?? 0, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="text-align: left;">Total Impuesto Retenido</td>
                <td>{{ number_format($movimientos->total_retencion ?? 0, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Se expide el presente comprobante a los fines de su declaración definitiva de rentas.</p>
        <br><br><br>
        <div class="sign-box">
            Firma y Sello Autorizado
        </div>
        <p style="font-size: 8px; color: #555; margin-top: 30px;">Documento generado por el Sistema de Gestión de RRHH - {{ $fecha }}</p>
    </div>
</body>
</html>
