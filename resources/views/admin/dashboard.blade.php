@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-speedometer2 me-2"></i> Dashboard Administrativo</h4>
                    <span class="badge bg-white text-primary">SIGESP Conectado</span>
                </div>

                <div class="card-body bg-light">

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body py-3">
                            <form action="{{ route('admin.dashboard') }}" method="GET" class="row g-3 align-items-end">
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label small fw-bold text-muted">Desde:</label>
                                    <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="form-control form-control-sm shadow-sm">
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label small fw-bold text-muted">Hasta:</label>
                                    <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="form-control form-control-sm shadow-sm">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">
                                        <i class="bi bi-filter me-1"></i> Filtrar Panel
                                    </button>
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
                                        <i class="bi bi-arrow-clockwise me-1"></i> Limpiar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm text-white bg-success h-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title mb-1">Usuarios</h5>
                                            <p class="small opacity-75">Base de datos local</p>
                                        </div>
                                        <i class="bi bi-people fs-1 opacity-50"></i>
                                    </div>
                                    <h2 class="fw-bold mb-0">{{ $usuariosActivos ?? '0' }}</h2>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm w-100 mt-3 text-success fw-bold">Gestionar Usuarios</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm text-white bg-info h-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title mb-1">Descargas del Periodo</h5>
                                            <p class="small opacity-75">Hoy: {{ $totalHoy ?? 0 }}</p>
                                        </div>
                                        <i class="bi bi-cloud-download fs-1 opacity-50"></i>
                                    </div>
                                    <h2 class="fw-bold mb-0">{{ $totalPeriodo ?? $totalHoy ?? '0' }}</h2>
                                    <a href="{{ route('admin.reportes.menu') }}" class="btn btn-light btn-sm w-100 mt-3 text-info fw-bold">Ver Reportes</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm text-white bg-warning h-100">
                                <div class="card-body d-flex flex-column justify-content-between text-dark">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title mb-1">Configuración</h5>
                                            <p class="small opacity-75">Parámetros globales</p>
                                        </div>
                                        <i class="bi bi-gear fs-1 opacity-50"></i>
                                    </div>
                                    <p class="small mb-0 fw-bold">Módulo de Auditoría Activo</p>
                                    <a href="{{ route('admin.settings.index') }}" class="btn btn-dark btn-sm w-100 mt-3 shadow">Ajustes del Sistema</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm mb-4 h-100">
                                <div class="row mb-4">
                                     <div class="col-lg-12">
                                          <div class="card border-0 shadow-sm">
                                               <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                                                     <span><i class="bi bi-bar-chart-line me-2 text-primary"></i> Generación Mensual: Planilla ARC</span>
                                                          <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Año {{ date('Y') }}</span>
                                                      </div>
                                                      <a href="{{ route('admin.export.excel', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}"
   class="btn btn-success btn-sm px-3 shadow-sm ms-1">
    <i class="bi bi-file-earmark-excel me-1"></i> Exportar a Excel
</a>
                                                  <div class="card-body">
                                                     <div style="height: 250px;">
                                                           <canvas id="chartArcBar"></canvas>
                                                      </div>
                                                    </div>
                     </div>
    </div>
</div>
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    @if(isset($labelsPie) && count($labelsPie) > 0)
                                        <div style="width: 100%; height: 300px;">
                                            <canvas id="chartPie"></canvas>
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="bi bi-graph-up-arrow display-4 text-light"></i>
                                            <p class="text-muted mt-3">No hay datos en el rango seleccionado</p>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm mb-4 h-100">
                                <div class="card-header bg-white fw-bold py-3 text-primary">
                                    <i class="bi bi-clock-history me-2"></i> Actividad Reciente
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @forelse($ultimasDescargas ?? [] as $descarga)
                                        <li class="list-group-item px-3 py-3 small border-0 border-bottom">
                                            <div class="d-flex w-100 justify-content-between align-items-start">
                                                <div>
                                                    <span class="fw-bold text-dark d-block">{{ $descarga->nombre_empleado }}</span>
                                                    <span class="text-muted">{{ $descarga->tipo_reporte }}</span>
                                                </div>
                                                <small class="badge bg-light text-muted border fw-normal">
                                                    {{ \Carbon\Carbon::parse($descarga->created_at)->diffForHumans() }}
                                                </small>
                                            </div>
                                        </li>
                                        @empty
                                        <li class="list-group-item text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                            Sin actividad registrada
                                        </li>
                                        @endforelse
                                    </ul>
                                </div>
                                <div class="card-footer bg-white text-center py-3">
                                    <a href="{{ route('admin.historial.descargas') }}" class="text-decoration-none small fw-bold">
                                        Ver Historial Completo <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. DATOS DESDE LARAVEL
        const labelsMeses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        // Datos de Barras (Comparativo)
        const dataArc = {!! json_encode($arcStats) !!};
        const dataRecibo = {!! json_encode($reciboStats) !!};
        const dataConstancia = {!! json_encode($constanciaStats) !!};

        // Datos de Torta (Distribución)
        const labelsPie = {!! json_encode($labelsPie ?? []) !!};
        const valuesPie = {!! json_encode($datosPie ?? []) !!};

        // --- GRÁFICA DE BARRAS COMPARATIVA ---
        const ctxBar = document.getElementById('chartArcBar').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labelsMeses,
                datasets: [
                    {
                        label: 'Planilla ARC',
                        data: dataArc,
                        backgroundColor: '#0d6efd', // Azul
                        borderRadius: 5,
                    },
                    {
                        label: 'Recibo de Pago',
                        data: dataRecibo,
                        backgroundColor: '#198754', // Verde
                        borderRadius: 5,
                    },
                    {
                        label: 'Constancia Trabajo',
                        data: dataConstancia,
                        backgroundColor: '#ffc107', // Amarillo
                        borderRadius: 5,
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, padding: 20 }
                    },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 }
                    }
                }
            }
        });

        // --- GRÁFICA DE TORTA (DOUGHNUT) ---
        if (labelsPie.length > 0) {
            const ctxPie = document.getElementById('chartPie').getContext('2d');
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: labelsPie,
                    datasets: [{
                        data: valuesPie,
                        backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#0dcaf0', '#d63384'],
                        hoverOffset: 15,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
                    }
                }
            });
        }
    });
</script>

<style>
    .card { transition: transform 0.2s ease-in-out; }
    .card:hover { transform: translateY(-3px); }
    .list-group-item:hover { bg-color: #f8f9fa; }
    .form-control-sm:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15); }
</style>
@endsection
