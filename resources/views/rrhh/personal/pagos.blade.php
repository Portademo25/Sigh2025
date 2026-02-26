@extends('layouts.app')

@section('content')
@if(session('error'))
    <div class="alert alert-danger shadow-sm border-0 mb-4">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
    </div>
@endif
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('rrhh.personal.pagos.index') }}">Personal</a></li>
            <li class="breadcrumb-item active">Gestión de Pagos</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-primary text-white rounded">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-user-circle fa-4x"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h4 class="mb-0 text-uppercase">{{ $personal->nomper }} {{ $personal->apeper }}</h4>
                    <p class="mb-0 opacity-75">{{ $personal->descar }} | C.I: {{ $personal->cedper }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-file-invoice-dollar me-2 text-success"></i> Generar Recibo de Pago
                </div>
                <div class="card-body">
                    <form action="{{ route('rrhh.recibo.descargar') }}" method="POST">
                        @csrf
                        <input type="hidden" name="cedper" value="{{ $personal->cedper }}">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Año</label>
                            <select name="ano" class="form-select border-primary">
                                @for($i=date('Y'); $i>=2023; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mes</label>
                            <select name="mes" class="form-select border-primary">
                                @foreach($meses as $num => $nombre)
                                    <option value="{{ $num }}" {{ date('n') == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold d-block">Quincena / Periodo</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="periodo" id="q1" value="1" checked>
                                <label class="btn btn-outline-primary" for="q1">1ra Quincena</label>

                                <input type="radio" class="btn-check" name="periodo" id="q2" value="2">
                                <label class="btn btn-outline-primary" for="q2">2da Quincena</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                            <i class="fas fa-download me-2"></i> DESCARGAR RECIBO
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-history me-2 text-info"></i> Otros Reportes
                </div>
                <div class="card-body">
                    <p class="text-muted small">Generar ARC acumulado para este trabajador:</p>
                    <div class="input-group">
                        <select id="ano_arc" class="form-select">
                            <option value="2026">Año 2026</option>
                        </select>
                        <button onclick="descargarARC()" class="btn btn-info text-white">Descargar ARC</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function descargarARC() {
        const ano = document.getElementById('ano_arc').value;
        const url = "{{ route('rrhh.arc.generar', [$personal->cedper, ':ano']) }}".replace(':ano', ano);
        window.location.href = url;
    }
</script>
@endsection
