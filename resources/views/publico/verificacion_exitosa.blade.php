<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Documento - FONA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white text-center py-4">
                    <i class="bi bi-patch-check-fill" style="font-size: 3rem;"></i>
                    <h3 class="mt-2">Documento Verificado</h3>
                    <p class="mb-0">Sistema de Validación de Constancias FONA</p>
                </div>

                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <small class="text-muted text-uppercase fw-bold">Datos del Trabajador</small>
                        <h4 class="text-primary mt-1">{{ $registro->nombre_completo }}</h4>
                        <span class="badge bg-secondary">V-{{ number_format($registro->cedula, 0, '', '.') }}</span>
                    </div>

                    <table class="table table-sm mt-3">
                        <tbody>
                             <tr>
                                <th class="text-muted">Cargo:</th>
                                <td>{{ $registro->cargo }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Unidad:</th>
                                <td>{{ $registro->unidad }}</td>
                            </tr> 
                            <tr>
                                <th class="text-muted">Alimentación:</th>
                                <td class="fw-bold">Bs. {{ number_format($registro->monto_alimentacion, 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="alert {{ $activo ? 'alert-info' : 'alert-warning' }} text-center mt-4">
                        <small>
                            Este documento fue generado el <strong>{{ \Carbon\Carbon::parse($registro->fecha_generacion)->format('d/m/Y ') }}</strong>.
                            @if($activo)
                                <br><i class="bi bi-person-check-fill"></i> El trabajador se encuentra actualmente <strong>ACTIVO</strong> en la institución.
                            @else
                                <br><i class="bi bi-exclamation-triangle-fill"></i> El estatus actual del trabajador ha cambiado desde la emisión.
                            @endif
                        </small>
                    </div>
                </div>

                <div class="card-footer text-center py-3 bg-white">
                    <img src="{{ asset('images/logo_fona.png') }}" alt="FONA" style="height: 40px;">
                    <p class="text-muted small mt-2 mb-0">© {{ date('Y') }} Fondo Nacional Antidrogas</p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
