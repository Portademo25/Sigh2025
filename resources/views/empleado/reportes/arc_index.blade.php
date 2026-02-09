@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar mr-2"></i> Generar Planilla ARC</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted">
                        Descargue su Comprobante de Retención de ISLR (ARC) correspondiente al año fiscal en curso.
                    </p>

                    <div class="form-group">
    <label for="ano" class="font-weight-bold">Año Fiscal:</label>
    <select name="ano" id="ano" class="form-control form-control-lg">
        {{-- Año Actual --}}
        <option value="{{ date('Y') }}">{{ date('Y') }} (Año en curso)</option>

        {{-- Año Anterior --}}
        <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }} (Año anterior)</option>
    </select>
</div>

                        <div class="mt-4">
                            <button type="button" onclick="descargarArc()" class="btn btn-success btn-block btn-lg shadow">
                                <i class="fas fa-download mr-1"></i> Descargar PDF
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light text-center">
                    <small class="text-muted">Los datos son extraídos directamente del sistema SIGESP.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function descargarArc() {
        const ano = document.getElementById('ano').value;
        if(ano) {
            window.location.href = `/empleado/reporte/arc/${ano}`;
        }
    }
</script>
@endsection
