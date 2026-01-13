<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0.5cm 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9px; margin: 0; }
        .header-table { width: 100%; margin-bottom: 10px; }
        .logo { height: 50px; }
        .inst-text { text-align: center; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        .title { text-align: center; font-size: 11px; font-weight: bold; text-decoration: underline; margin-bottom: 5px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 1px 0; vertical-align: top; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 4px; text-align: left; }
        .data-table td { padding: 2px 4px; font-family: 'Courier', monospace; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .footer { font-size: 8px; margin-top: 15px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="20%"><img src="{{ public_path('images/logo_ministerio.png') }}" class="logo"></td>
            <td width="60%" class="inst-text">
                REPUBLICA BOLIVARIANA DE VENEZUELA <br>
                MINISTERIO DEL PODER POPULAR PARA RELACIONES INTERIORES, JUSTICIA Y PAZ <br>
                SUPERINTENDENCIA NACIONAL ANTIDROGAS <br>
                FONDO NACIONAL ANTIDROGAS
            </td>
            <td width="20%" class="text-right"><img src="{{ public_path('images/logo_fona.png') }}" class="logo"></td>
        </tr>
    </table>

    <div class="title">COMPROBANTE DE PAGO</div>

    <div class="bold" style="margin-bottom: 5px;">
        Periodo {{ $resumen->codperi }} Del {{ \Carbon\Carbon::parse($resumen->fecdesper)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($resumen->fechasper)->format('d/m/Y') }}
    </div>

    <table class="info-table">
        <tr>
            <td width="20%" class="bold">NOMBRES Y APELLIDOS:</td>
            <td width="50%">{{ $user->name }} {{ $user->lastname }} </td>
            <td width="10%" class="bold">C.I. Nro.</td>
            <td width="20%">V{{ number_format($user->codper, 0, '', '.') }} </td>
        </tr>
        <tr>
            <td class="bold">TIPO DE PERSONAL:</td>
            <td colspan="3">{{ $resumen->desnom }} </td>
        </tr>
        <tr>
            <td class="bold">FECHA DE INGRESO:</td>
            <td colspan="3">{{ \Carbon\Carbon::parse($resumen->fecingper)->format('d/m/Y') }} </td>
        </tr>
        <tr>
            <td class="bold">DEPENDENCIA:</td>
            <td colspan="3">{{ $resumen->desuniadm }} </td>
        </tr>
        <tr>
            <td class="bold">CUENTA BANCARIA:</td>
            <td colspan="3">{{ $resumen->ctabanper }} </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="15%">CONCEPTO [cite: 9]</th>
                <th width="55%">DESCRIPCIÓN [cite: 9]</th>
                <th width="15%" class="text-right">ASIGNACIONES </th>
                <th width="15%" class="text-right">DEDUCCIONES </th>
            </tr>
        </thead>
        <tbody>
            @foreach($conceptos as $concepto)
            <tr>
                <td>{{ str_pad($concepto->codcon, 10, "0", STR_PAD_LEFT) }} </td>
                <td>{{ $concepto->nomcon }} </td>
                <td class="text-right">
                    {{ in_array($concepto->tipcon, ['P', 'A']) ? number_format($concepto->valcalcur, 2, ',', '.') : '' }}
                </td>
                <td class="text-right">
                    {{ $concepto->tipcon == 'D' ? '-' . number_format($concepto->valcalcur, 2, ',', '.') : '' }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right bold" style="border-top: 1px solid #000;">TOTALES </td>
                <td class="text-right bold" style="border-top: 1px solid #000;">{{ number_format($resumen->asires, 2, ',', '.') }} </td>
                <td class="text-right bold" style="border-top: 1px solid #000;">-{{ number_format($resumen->dedres, 2, ',', '.') }} </td>
            </tr>
            <tr>
                <td colspan="2" class="text-right bold">NETO </td>
                <td colspan="2" class="text-right bold" style="border-top: 1px solid #000; font-size: 11px;">
                    {{ number_format($resumen->monnetres, 2, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Sigh Sistema de Gestion Humana {{ date('d/m/Y h:i:s a') }}
    </div>
</body>
</html>
