<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; color: #333; }
        .wrapper { width: 100%; max-width: 600px; margin: 0 auto; border: 1px solid #eee; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; border-bottom: 3px solid #004a99; }
        .content { padding: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #004a99; color: white; padding: 12px; text-align: left; font-size: 14px; }
        td { border-bottom: 1px solid #eee; padding: 12px; font-size: 14px; }
        .dia { font-weight: bold; color: #004a99; text-align: center; }
        .footer { padding: 20px; font-size: 11px; color: #777; text-align: center; background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            @if(isset($logoFona))
                <img src="{{ $logoFona }}" style="height: 60px; width: auto;">
            @endif
            <h2 style="margin: 10px 0 0 0; color: #004a99;">Cumpleañeros del Mes</h2>
            <p style="margin: 5px 0 0 0; text-transform: uppercase;"><strong>{{ $mesActual }}</strong></p>
        </div>

        <div class="content">
            <p>Estimada Directora,</p>
            <p>A continuación, presentamos la lista de los trabajadores que celebran su cumpleaños durante este mes:</p>

            <table>
                <thead>
                    <tr>
                        <th style="width: 15%; text-align: center;">Día</th>
                        <th>Trabajador</th>
                        <th style="width: 25%;">Cédula</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cumpleaneros as $c)
                        <tr>
                            <td class="dia">{{ intval(date('d', strtotime($c->fecnacper))) }}</td>
                            <td>{{ strtoupper($c->nomper) }} {{ strtoupper($c->apeper) }}</td>
                            <td>V.- {{ number_format($c->cedper, 0, '', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>Este es un reporte automático generado por el Sistema de Gestión Humana (SIGH).<br>
            © {{ date('Y') }} Fondo Nacional Antidrogas (FONA)</p>
        </div>
    </div>
</body>
</html>
