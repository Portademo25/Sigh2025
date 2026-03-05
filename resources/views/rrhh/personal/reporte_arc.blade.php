<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Comprobante ARC - {{ $trabajador->cedper }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; line-height: 1.4; color: #333; }

        /* Encabezado Institucional */
        .header-container { width: 100%; border-bottom: 2px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        .ministerio-text { text-align: center; text-transform: uppercase; font-size: 10px; font-weight: bold; }
        .ente-text { text-align: center; font-size: 12px; font-weight: bold; margin-top: 5px; }

        .document-title { text-align: center; margin: 15px 0; }
        .title { font-weight: bold; font-size: 14px; text-decoration: underline; display: block; }

        .table-datos { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table-datos td, .table-datos th { padding: 6px; border: 1px solid #000; }
        .bg-gray { background-color: #f2f2f2; }

        .footer { margin-top: 40px; text-align: center; }
        .sign-box { margin-top: 60px; display: inline-block; width: 250px; border-top: 1px solid #000; padding-top: 5px; }
        .watermark { font-size: 8px; color: #777; margin-top: 40px; text-align: right; }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="ministerio-text">
            República Bolivariana de Venezuela<br>
            Ministerio del Poder Popular para la Educación </div>
        <div class="ente-text">
            {{ $empresa }} </div>
    </div>

    <div class="document-title">
        <span class="title">COMPROBANTE DE RETENCIÓN DE I.S.L.R. (ARC)</span>
        <strong>EJERCICIO FISCAL: {{ $ano }}</strong>
    </div>

    <table class="table-datos">
        <tr>
            <td colspan="2" class="bg-gray"><strong>1. DATOS DEL AGENTE DE RETENCIÓN (PATRONO)</strong></td>
        </tr>
        <tr>
            <td colspan="2"><strong>NOMBRE O RAZÓN SOCIAL:</strong> {{ $empresa }}</td>
        </tr>
        <tr>
            <td class="bg-gray"><strong>2. DATOS DEL BENEFICIARIO (TRABAJADOR)</strong></td>
            <td class="bg-gray"><strong>3. IDENTIFICACIÓN</strong></td>
        </tr>
        <tr>
            <td><strong>APELLIDOS Y NOMBRES:</strong><br>{{ $trabajador->nomper }} {{ $trabajador->apeper }}</td>
            <td><strong>CÉDULA DE IDENTIDAD:</strong><br>V-{{ $trabajador->cedper }}</td>
        </tr>
    </table>

    <table class="table-datos" style="text-align: right;">
        <thead>
            <tr class="bg-gray">
                <th style="text-align: left; width: 70%;">CONCEPTO O DESCRIPCIÓN</th>
                <th style="text-align: center; width: 30%;">TOTALES ACUMULADOS Bs.</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: left;">Total Remuneraciones Pagadas o Abonadas en Cuenta (Sueldos, Salarios y otros conceptos)</td>
                <td><strong>{{ number_format($movimientos->total_sueldo ?? 0, 2, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td style="text-align: left;">Total Impuesto sobre la Renta Retenido</td>
                <td><strong>{{ number_format($movimientos->total_retencion ?? 0, 2, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Se emite el presente comprobante cumpliendo con las disposiciones de la Ley de Impuesto Sobre la Renta y su Reglamento vigente, para los fines de su declaración definitiva de rentas.</p>

        <div class="sign-box">
            <strong>Firma y Sello Autorizado</strong><br>
            Dirección de Gestión Humana
        </div>

        <div class="watermark">
            Documento digital generado el {{ $fecha }}<br>
            Sincronizado con Sistema SIGESP
        </div>
    </div>
</body>
</html>
