<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ARC - {{ $personal->cedper }} - Año {{ $ano }}</title>
    <style>
    @page { margin: 1cm 1.5cm; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9px; color: #333; line-height: 1.4; }

    /* Encabezado */
    .header-table { width: 100%; border: none; margin-bottom: 15px; }
    .header-text { text-align: center; font-size: 10px; color: #003366; font-weight: bold; }

    /* Secciones */
    .section-header {
        background-color: #003366;
        color: white;
        padding: 4px 8px;
        font-weight: bold;
        font-size: 10px;
        margin-top: 15px;
        border-radius: 3px 3px 0 0;
    }

    /* Tablas */
    .table-data { width: 100%; border-collapse: collapse; margin-bottom: 0; }
    .table-data th, .table-data td { border: 1px solid #ccc; padding: 5px; }
    .table-data th { background-color: #f2f2f2; color: #003366; text-transform: uppercase; font-size: 8px; }

    /* Alineaciones y utilidades */
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .bg-light { background-color: #f9f9f9; font-weight: bold; color: #555; }
    .total-row { background-color: #eee; font-weight: bold; border-top: 2px solid #003366; }

    /* QR y Firma */
    .footer-container { margin-top: 30px; width: 100%; }
    .signature-box { border-top: 1px solid #000; width: 200px; margin-top: 50px; text-align: center; padding-top: 5px; }
</style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="15%"><img src="data:image/png;base64,{{ $logoRepublica }}" style="width: 70px;"></td>
            <td class="header-text">
                REPÚBLICA BOLIVARIANA DE VENEZUELA<br>
                {{ $agente['ente'] }}<br>
                <span style="font-size: 12px;">COMPROBANTE DE RETENCIONES AR-C</span><br>
                EJERCICIO FISCAL {{ $ano }}
            </td>
            <td width="15%" style="text-align: right;"><img src="data:image/png;base64,{{ $logoEnte }}" style="width: 70px;"></td>
        </tr>
    </table>

    <div class="section-header">Identificación del Beneficiario</div>
    <table class="table-data">
        <tr>
            <td width="15%" class="bg-light">Nombre:</td>
            <td width="45%">{{ $personal->nomper }} {{ $personal->apeper }}</td>
            <td width="15%" class="bg-light">Cédula:</td>
            <td width="25%">V-{{ number_format($personal->cedper, 0, '', '.') }}</td>
        </tr>
    </table>

  <div class="section-header">DATOS DEL AGENTE DE RETENCIÓN (DEPENDENCIA OFICIAL)</div>
<table class="table-data">
    <tr>
        <td width="20%" class="bg-light">NOMBRE / REPRESENTANTE:</td>
        <td width="30%">{{ $agente['nombre'] }}</td>
        <td width="20%" class="bg-light">CARGO:</td>
        <td width="30%">{{ $agente['cargo'] }}</td>
    </tr>
    <tr>
        <td class="bg-light">ENTE / INSTITUCIÓN:</td>
        <td>{{ $agente['ente'] }}</td>
        <td class="bg-light">RIF:</td>
        <td>{{ $agente['rif'] }}</td>
    </tr>
    <tr>
        <td class="bg-light">CIUDAD / ESTADO:</td>
        <td>{{ $agente['ciudad'] }} / {{ $agente['estado'] }}</td>
        <td class="bg-light">TELÉFONO:</td>
        <td>{{ $agente['telefono'] }}</td>
    </tr>
    <tr>
        <td class="bg-light">DIRECCIÓN FISCAL:</td>
        <td colspan="3">{{ $agente['direccion'] }}</td>
    </tr>
</table>

    <div class="section-header">Detalle de Remuneraciones y Retenciones Mensuales</div>
    <table class="table-data">
        <thead>
            <tr>
                <th width="15%">Mes</th>
                <th width="30%">Remuneraciones Pagadas</th>
                <th width="25%">Impuesto Retenido (ISLR)</th>
                <th width="30%">Otras Retenciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($meses as $num => $mesNombre)
                @php $item = $detalles->firstWhere('mes', $num); @endphp
                <tr>
                    <td>{{ $mesNombre }}</td>
                    <td class="text-right">{{ number_format($item->asignacion ?? 0, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->ret_islr ?? 0, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->otras_retenciones ?? 0, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>TOTAL ANUAL</td>
                <td class="text-right">Bs. {{ number_format($detalles->sum('asignacion'), 2, ',', '.') }}</td>
                <td class="text-right">Bs. {{ number_format($detalles->sum('ret_islr'), 2, ',', '.') }}</td>
                <td class="text-right">Bs. {{ number_format($detalles->sum('otras_retenciones'), 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td width="45%" style="vertical-align: top;">
                <div style="font-weight: bold; margin-bottom: 5px; color: #003366; border-bottom: 1px solid #ccc;">Desglose Otras Retenciones (Anual)</div>
                <table class="table-data" style="font-size: 8px;">
                    <tr><td>S.S.O.</td><td class="text-right">{{ number_format($detalles->sum('monto_sso'), 2, ',', '.') }}</td></tr>
                    <tr><td>P.I.E.</td><td class="text-right">{{ number_format($detalles->sum('monto_pie'), 2, ',', '.') }}</td></tr>
                    <tr><td>F.A.O.V.</td><td class="text-right">{{ number_format($detalles->sum('monto_faov'), 2, ',', '.') }}</td></tr>
                    <tr><td>I.N.C.E.S.</td><td class="text-right">{{ number_format($detalles->sum('monto_inces'), 2, ',', '.') }}</td></tr>
                    <tr><td>Fondo Pensión</td><td class="text-right">{{ number_format($detalles->sum('monto_pension'), 2, ',', '.') }}</td></tr>
                </table>
            </td>
            <td width="10%"></td>
            <td width="45%" class="text-center" style="vertical-align: bottom;">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="60">
                <div style="font-size: 7px; margin-top: 5px;">Escanee para verificar validez</div>
            </td>
        </tr>
    </table>
</body>
