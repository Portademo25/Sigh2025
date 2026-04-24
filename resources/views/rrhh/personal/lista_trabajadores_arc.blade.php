@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-3">
    <div class="card shadow border-0">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i> Personal para ARC - Ejercicio {{ $anio }}</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('rrhh.personal.gestion_arc', ['ano' => $anio]) }}" class="btn btn-sm btn-light shadow-sm fw-bold">
                    <i class="fas fa-cog me-1 text-success"></i> Ajustar Configuración {{ $anio }}
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <form action="{{ route('rrhh.personal.lista_trabajadores_arc') }}" method="GET" class="input-group">
                        <input type="hidden" name="anio" value="{{ $anio }}">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="buscar" class="form-control border-start-0 shadow-none"
                               placeholder="Buscar por cédula o nombre..." value="{{ request('buscar') }}">
                        <button type="submit" class="btn btn-success px-4">Buscar</button>
                    </form>
                </div>
                <div class="col-md-6 text-end d-flex align-items-center justify-content-end">
                    <span class="badge bg-light text-dark border p-2">
                        <i class="fas fa-info-circle me-1 text-info"></i>
                        Mostrando {{ $trabajadores->firstItem() }} - {{ $trabajadores->lastItem() }} de {{ $trabajadores->total() }} registros
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle border-start border-end">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th width="150" class="ps-3">Cédula</th>
                            <th>Nombres y Apellidos</th>
                            <th class="text-center" width="200">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trabajadores as $t)
                        <tr>
                            <td class="fw-bold ps-3">
                                <span class="badge bg-secondary font-monospace">{{ $t->cedper }}</span>
                            </td>
                            <td class="text-uppercase small fw-semibold text-dark">
                                {{ trim($t->nomper) }} {{ trim($t->apeper) }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('rrhh.personal.pdf_arc', ['cedula' => trim($t->cedper), 'anio' => $anio]) }}"
                                   class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm hover-pdf"
                                   target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i> Generar ARC {{ $anio }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <i class="fas fa-user-slash fa-3x text-light mb-3"></i>
                                <p class="text-muted">No se encontraron trabajadores con los criterios actuales.</p>
                                <a href="{{ route('rrhh.personal.lista_trabajadores_arc', ['anio' => $anio]) }}" class="btn btn-sm btn-link">Limpiar búsqueda</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {!! $trabajadores->appends(['anio' => $anio, 'buscar' => request('buscar')])->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<style>
    .hover-pdf:hover {
        background-color: #dc3545 !important;
        color: white !important;
        transform: translateY(-1px);
        transition: all 0.2s;
    }
    .font-monospace { font-family: 'Courier New', Courier, monospace; }
</style>
@endsection
