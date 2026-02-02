<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación Digital ARC - FONA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .card-verify { border: none; border-radius: 15px; overflow: hidden; }
        .bg-arc { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); }
        .data-label { color: #64748b; font-size: 0.85rem; font-weight: 600; text-uppercase: true; }
        .data-value { color: #1e293b; font-weight: 700; font-size: 1.1rem; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-verify shadow-lg">
                <div class="card-header bg-arc text-white text-center py-4">
                    <i class="bi bi-shield-check" style="font-size: 3.5rem;"></i>
                    <h4 class="mt-2 mb-0">Comprobante Verificado</h4>
                    <p class="small opacity-75">Sistema de Validación Fiscal FONA</p>
                </div>

                <div class="card-body p-4">
                    <div class="alert alert-success d-flex align-items-center mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div>Este documento es auténtico y coincide con los registros de nómina.</div>
                    </div>

                    <div class="mb-4 text-center">
                        <span class="data-label d-block">Contribuyente</span>
                        <span class="data-value text-uppercase">{{ $registro->nombre_completo }}</span>
                        <div class="text-muted small">C.I.: V-{{ number_format($registro->cedula, 0, '', '.') }}</div>
                    </div>

                    <hr class="text-muted opacity-25">

                    <div class="row g-3">
                        <div class="col-6">
                            <span class="data-label d-block">Año Fiscal</span>
                            <span class="data-value text-primary">{{ $registro->ano_fiscal }}</span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="data-label d-block">Fecha de Emisión</span>
                            <span class="data-value" style="font-size: 0.9rem;">
                                {{ \Carbon\Carbon::parse($registro->fecha_generacion)->format('d/m/Y') }}
                            </span>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Total Percibido:</span>
                                    <span class="text-dark fw-bold">Bs. {{ number_format($registro->total_remuneracion, 2, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold text-danger">Total Retenido:</span>
                                    <span class="text-danger fw-bold">Bs. {{ number_format($registro->total_retencion, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <p class="small text-muted mb-0">Código de Seguridad (Token):</p>
                        <code class="small text-break">{{ $registro->token }}</code>
                    </div>
                </div>

                <div class="card-footer bg-white text-center py-3 border-top">
                    <div class="small fw-bold text-secondary">FONDO NACIONAL ANTIDROGAS (FONA)</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Generado a través del Portal del Empleado</div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
