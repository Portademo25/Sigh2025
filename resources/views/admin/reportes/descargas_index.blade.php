@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-dark text-white py-3">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="bi bi-journal-text me-2 text-warning"></i> Historial de Documentos Generados
                </h5>
                <a href="{{ route('admin.reportes.menu') }}" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-arrow-left-circle me-1"></i> Regresar al Menú
                </a>
            </div>
        </div>
        <div class="row mb-4">
    <div class="col-12">
        <div class="card shadow border-0">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-graph-up me-2 text-primary"></i> Actividad de los últimos 7 días</h6>
                <div style="height: 200px;">
                    <canvas id="graficoDescargas"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('graficoDescargas').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Documentos Generados',
                data: {!! json_encode($valores) !!},
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 180px;">Fecha y Hora</th>
                            <th>Empleado</th>
                            <th>Cédula</th>
                            <th>Tipo de Documento</th>
                            <th>Detalles Técnicos / Periodo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($descargas as $d)
                        <tr>
                            <td class="small text-muted">
                                {{ \Carbon\Carbon::parse($d->created_at)->format('d/m/Y') }}<br>
                                <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($d->created_at)->format('h:i A') }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $d->nombre_empleado }}</div>
                            </td>
                            <td>{{ $d->cedula }}</td>
                            <td>
                                @php
                                    $badgeClass = [
                                        'Recibo de Pago' => 'bg-success',
                                        'Constancia de Trabajo' => 'bg-primary',
                                        'Planilla ARC' => 'bg-warning text-dark',
                                        'Forma 14-100 IVSS' => 'bg-danger'
                                    ][$d->tipo_reporte] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2">
                                    {{ $d->tipo_reporte }}
                                </span>
                            </td>
                            <td>
                                <code class="small text-muted">{{ $d->detalles }}</code>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-info-circle fs-2 d-block mb-2"></i>
                                No se han registrado descargas de documentos todavía.
                            </li>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $descargas->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
    .table th { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge { font-weight: 600; font-size: 0.75rem; letter-spacing: 0.3px; }
    .card { border-radius: 12px; overflow: hidden; }
</style>
@endsection
