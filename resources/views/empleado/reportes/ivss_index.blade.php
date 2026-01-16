@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white fw-bold">
                    Generar Forma 14-100 (IVSS)
                </div>
                <div class="card-body p-4 text-center">
                    <p class="text-muted">Selecciona el año fiscal para el desglose de salarios:</p>

                    <form action="#" id="formIvss">
                        <div class="mb-4">
                            <select class="form-select form-select-lg" id="selectAno">
                                @foreach($anos as $a)
                                    <option value="{{ (int)$a->ano }}">{{ (int)$a->ano }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" onclick="descargarPDF()" class="btn btn-danger btn-lg">
                                <i class="bi bi-file-pdf"></i> Descargar Planilla
                            </button>
                            <a href="{{ route('empleado.reportes.menu') }}" class="btn btn-link text-muted">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function descargarPDF() {
        const ano = document.getElementById('selectAno').value;
        // Redirige a la ruta de descarga pasando el año
        window.location.href = "{{ url('/reportes/ivss/descargar') }}/" + ano;
    }
</script>
@endsection
