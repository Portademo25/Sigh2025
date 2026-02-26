@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark text-uppercase mb-0">
                <i class="fas fa-shield-alt text-primary me-2"></i> Panel de Validación
            </h2>
            <p class="text-muted mb-0">Historial de constancias emitidas y verificación de integridad.</p>
        </div>
        <div class="col-md-4 text-end">
             <a href="{{ route('home') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver al Menú
        </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('rrhh.constancias.validar') }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-2 border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="buscar" class="form-control form-control-lg border-2 border-start-0" placeholder="BUSCAR POR CÉDULA, NOMBRE O TOKEN..." value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                        Consultar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold">Fecha / Hora</th>
                        <th class="text-uppercase small fw-bold">Trabajador</th>
                        <th class="text-uppercase small fw-bold">Cédula</th>
                        <th class="text-uppercase small fw-bold">Sueldo Ref.</th>
                        <th class="text-uppercase small fw-bold">Token</th>
                        <th class="text-center text-uppercase small fw-bold">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($constancias as $c)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold d-block">{{ \Carbon\Carbon::parse($c->fecha_generacion)->format('d/m/Y') }}</span>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($c->fecha_generacion)->format('h:i A') }}</small>
                        </td>
                        <td class="fw-bold text-uppercase">{{ $c->nombre_completo }}</td>
                        <td>{{ number_format($c->cedula, 0, '', '.') }}</td>
                        <td>Bs. {{ number_format($c->sueldo_integral, 2, ',', '.') }}</td>
                        <td>
                            <code class="text-primary">{{ substr($c->token, 0, 10) }}...</code>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                <i class="fas fa-check-circle me-1"></i> VÁLIDA
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            No se encontraron registros de constancias.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $constancias->appends(['buscar' => request('buscar')])->links() }}
    </div>
</div>
@endsection
