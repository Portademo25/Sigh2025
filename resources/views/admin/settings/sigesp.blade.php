@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Panel de Control SIGESP</h4>
        </div>
        <div class="card-body text-center">
            <div class="alert alert-info">
                <strong>Última Sincronización:</strong> {{ $lastSync }}
            </div>

            <p>Este proceso actualizará las empresas, trabajadores, nóminas y periodos históricos desde el servidor central.</p>

            <form action="{{ route('admin.settings.sigesp.sync') }}" method="POST" id="syncForm">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg" id="btnSync">
                    <i class="fas fa-sync"></i> Iniciar Sincronización Ahora
                </button>
            </form>

            <div id="loader" style="display:none;" class="mt-3">
                <div class="spinner-border text-primary" role="status"></div>
                <p>Sincronizando datos... por favor no cierre esta ventana.</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('syncForm').onsubmit = function() {
        document.getElementById('btnSync').disabled = true;
        document.getElementById('loader').style.display = 'block';
    };
</script>
@endsection
