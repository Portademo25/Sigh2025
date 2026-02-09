<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-secondary font-weight-bold">
                <i class="fas fa-chart-pie mr-2"></i>Centro de Reportes Administrativos
            </h2>
            <p class="text-muted">Seleccione el módulo de auditoría o reporte que desea consultar.</p>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 border-left-primary hover-shadow" style="transition: all 0.3s ease;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Recursos Humanos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Historial de Constancias</div>
                            <p class="text-muted small mt-2">Auditoría de constancias emitidas, validación de tokens y sueldos registrados.</p>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-signature fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="<?php echo e(route('admin.reporte.constancias')); ?>" class="btn btn-primary btn-block btn-sm">
                        Abrir Reporte <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 border-left-success opacity-75">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Finanzas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Resumen de Nómina</div>
                            <p class="text-muted small mt-2">Consulta consolidada de conceptos y aportes patronales desde SIGESP.</p>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                     <a href="<?php echo e(route('admin.reportes.arc')); ?>" class="btn btn-primary btn-block btn-sm">
                        Abrir Reporte <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 border-left-info opacity-75">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Seguridad</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Accesos al Sistema</div>
                            <p class="text-muted small mt-2">Registro de inicios de sesión y descargas realizadas por el personal.</p>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="<?php echo e(route('admin.historial.descargas')); ?>" class="btn btn-primary btn-block btn-sm">
                        Abrir Reporte <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Sigh2025/resources/views/admin/reportes/menu.blade.php ENDPATH**/ ?>