<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h3 class="fw-bold">Centro de Reportes</h3>
            <p class="text-muted">Selecciona el documento que deseas generar o consultar.</p>
        </div>
        <div class="col-auto">
            <a href="<?php echo e(route('empleado.dashboard')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-house-door"></i> Volver al Inicio
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm text-center p-3">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-cash-stack text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Recibos de Pago</h5>
                    <p class="card-text text-muted small">Consulta tus quincenas pagadas, detalles de conceptos y montos netos.</p>
                    <a href="<?php echo e(route('empleado.reportes.recibos')); ?>" class="btn btn-success w-100">
                        Entrar a Recibos
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm text-center p-3">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-file-earmark-person text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Constancia de Trabajo</h5>
                    <p class="card-text text-muted small">Genera tu constancia laboral con sello digital y firma autorizada.</p>
                    <a href="<?php echo e(route('empleado.reportes.constancia_pdf')); ?>" class="btn btn-primary w-100 shadow-sm">
                        Generar Constancia
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm text-center p-3">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-calculator text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Planilla ARC</h5>
                    <p class="card-text text-muted small">Comprobante de retenciones de ISLR acumulado durante el año fiscal.</p>
                    <a href="<?php echo e(route('empleado.reportes.arc_index')); ?>" class="btn btn-warning w-100 text-dark fw-bold">
                        Consultar ARC
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12 text-center">
            <div class="alert alert-info border-0 shadow-sm d-inline-block px-4">
                <i class="bi bi-info-circle-fill me-2"></i> Los documentos generados tienen validez oficial y código de verificación.
            </div>
        </div>
    </div>
</div>

<style>
    .card { transition: transform 0.2s; }
    .card:hover { transform: translateY(-5px); }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/empleado/reportes/menu.blade.php ENDPATH**/ ?>