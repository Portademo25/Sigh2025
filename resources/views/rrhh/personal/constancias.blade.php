@extends('layouts.app')

@section('content')
@if(session('error'))
    <div class="alert alert-danger shadow-sm border-0 mb-4">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
    </div>
@endif
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="fas fa-file-signature text-success me-2"></i> Emisión de Constancias
            </h2>
            <p class="text-muted small mb-0">Seleccione un trabajador para generar el documento institucional.</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-3 shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver al Menú
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <form action="{{ route('rrhh.personal.constancias.index') }}" method="GET" class="row g-2">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0 ps-0"
                           placeholder="Buscar por cédula, nombre o apellido..." value="{{ request('buscar') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100 fw-bold rounded-3">
                    Buscar
                </button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Cédula de Identidad</th>
                        <th>Nombre Completo del Trabajador</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($personal as $p)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                                V-{{ number_format($p->cedper, 0, '', '.') }}
                            </span>
                        </td>
                        <td class="fw-bold text-dark">{{ $p->nomper }} {{ $p->apeper }}</td>
                        <td class="text-center">
                            <a href="{{ route('rrhh.personal.constancia', $p->cedper) }}"
   class="btn btn-success btn-sm px-4 py-2 rounded-3 shadow-sm text-white fw-bold">
    <i class="fas fa-print me-2"></i> Descargar Constancia
</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">No se encontraron trabajadores con esos criterios.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $personal->appends(['buscar' => request('buscar')])->links() }}
    </div>
</div>
@endsection
