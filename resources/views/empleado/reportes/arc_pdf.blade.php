<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ARC - {{ $personal->cedper ?? '' }} - Año {{ $ano }}</title>
    <style>
        @page { margin: 1cm 1.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9px; color: #333; line-height: 1.4; }
        .header-table { width: 100%; border: none; margin-bottom: 15px; }
        .header-text { text-align: center; font-size: 10px; color: #003366; font-weight: bold; }
        .section-header { background-color: #003366; color: white; padding: 4px 8px; font-weight: bold; font-size: 10px; margin-top: 15px; border-radius: 3px 3px 0 0; }
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        .table-data th, .table-data td { border: 1px solid #ccc; padding: 5px; }
        .table-data th { background-color: #f2f2f2; color: #003366; text-transform: uppercase; font-size: 8px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bg-light { background-color: #f9f9f9; font-weight: bold; color: #555; }
        .total-row { background-color: #eee; font-weight: bold; border-top: 2px solid #003366; }
        .footer-table { width: 100%; margin-top: 20px; }
        .signature-box { border-top: 1px solid #000; width: 180px; text-align: center; padding-top: 5px; margin-top: 40px; }
        .desglose-table { font-size: 8px; }
        .desglose-table td { padding: 3px 5px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td width="15%">
                @if($logoRepublica)
                    <img src="{{ $logoRepublica }}" style="width: 70px;">
                @endif
            </td>
            <td class="header-text">
                REPÚBLICA BOLIVARIANA DE VENEZUELA<br>
                MINISTERIO DEL PODER POPULAR PARA RELACIONES INTERIORES, JUSTICIA Y PAZ<br>
                {{ $agente['ente'] }}<br>
                <span style="font-size: 11px; font-weight: bold; margin-top: 5px; display: block; text-decoration: underline;">
                    COMPROBANTE DE RETENCIONES AR-C
                </span>
                <strong>EJERCICIO FISCAL {{ $ano }}</strong>
            </td>
            <td width="15%" style="text-align: right;">
                @if($logoFona)
                    <img src="{{ $logoFona }}" style="width: 70px;">
                @endif
            </td>
        </tr>
    </table>

    {{-- IDENTIFICACIÓN DEL BENEFICIARIO --}}
    <div class="section-header">Identificación del Beneficiario</div>
    <table class="table-data">
        <tr>
            <td width="15%" class="bg-light">Cédula:</td>
            <td width="35%">V-{{ number_format($personal->cedper ?? 0, 0, '', '.') }}</td>
            <td width="15%" class="bg-light">Código:</td>
            <td width="35%">{{ $personal->codper ?? '' }}</td>
        </tr>
        <tr>
            <td class="bg-light">Nombre:</td>
           <td colspan="3">{{ ($personal->nomper ?? '') . ' ' . ($personal->apeper ?? '') }}</td>
        </tr>
    </table>

    {{-- DATOS DEL AGENTE DE RETENCIÓN --}}
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

    {{-- DETALLE MENSUAL --}}
    <div class="section-header">Detalle de Remuneraciones y Retenciones Mensuales</div>
    <table class="table-data">
        <thead>
            <tr>
                <th width="12%">MES</th>
                <th width="28%">REMUNERACIONES PAGADAS</th>
                <th width="25%">IMPUESTO RETENIDO (ISLR)</th>
                <th width="35%">OTRAS RETENCIONES</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles as $item)
                <tr>
                    <td class="text-center">{{ $meses[$item->mes] }}</td>
                    <td class="text-right">{{ number_format($item->asignacion, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->ret_islr, 2, ',', '.') }}</td>
                    {{-- Columna en cero por requerimiento --}}
                    <td class="text-right">0,00</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td><strong>TOTAL ANUAL</strong></td>
                <td class="text-right"><strong>Bs. {{ number_format($totales['total_asignacion'], 2, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Bs. {{ number_format($totales['total_ret_islr'], 2, ',', '.') }}</strong></td>
                <td class="text-right"><strong>0,00</strong></td>
            </tr>
        </tfoot>
    </table>

    {{-- DESGLOSE DE RETENCIONES DINÁMICO --}}
    @php
        $conceptosAcumulados = [];
        foreach($detalles as $mesData) {
            // detalle_original ya viene filtrado por el Service según arc_parametros
            $detalleOriginal = (array)$mesData->detalle_original;
            foreach($detalleOriginal as $cod => $info) {
                $monto = is_object($info) ? ($info->monto ?? 0) : ($info['monto'] ?? 0);
                $nombre = is_object($info) ? ($info->nombre ?? "Concepto $cod") : ($info['nombre'] ?? "Concepto $cod");

                if($monto > 0) {
                    $llave = ltrim(trim($cod), '0');
                    if(!isset($conceptosAcumulados[$llave])) {
                        $conceptosAcumulados[$llave] = ['nombre' => $nombre, 'total' => 0];
                    }
                    $conceptosAcumulados[$llave]['total'] += $monto;
                }
            }
        }
    @endphp

    <table class="footer-table">
        <tr>
            <td width="60%" style="vertical-align: top;">
                <div style="font-weight: bold; margin-bottom: 8px; color: #003366; border-bottom: 2px solid #003366; padding-bottom: 3px;">
                    DESGLOSE OTRAS RETENCIONES (ANUAL)
                </div>
                <table class="table-data desglose-table">
                    @forelse($conceptosAcumulados as $data)
                        <tr>
                            <td width="70%">{{ $data['nombre'] }}</td>
                            <td width="30%" class="text-right">Bs. {{ number_format($data['total'], 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No se encontraron retenciones para desglosar.</td>
                        </tr>
                    @endforelse

                    <tr class="total-row">
                        <td><strong>TOTAL OTRAS RETENCIONES</strong></td>
                        <td class="text-right"><strong>Bs. {{ number_format($total_desglose_ley, 2, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </td>

        </tr>
    </table>

    <div style="position: fixed; bottom: 0; width: 100%; font-size: 7px; text-align: center; color: #666; margin-top: 20px;">
        Este documento tiene validez oficial conforme a las normas del SENIAT - Comprobante de Retenciones Varias
    </div>
</body>
</html>
