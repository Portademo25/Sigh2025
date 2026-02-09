<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-person-bounding-box text-primary" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="fw-bold"><?php echo e($empleado->nomper); ?></h4>
                    <h5 class="text-muted"><?php echo e($empleado->apeper); ?></h5>
                    <div class="badge bg-primary px-3 mb-2">C.I: <?php echo e($empleado->cedper); ?></div>
                    <p class="small text-muted mt-2">
                        <i class="bi bi-envelope-at"></i> <?php echo e($empleado->coreleins ?? $empleado->coreleper); ?>

                    </p>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Resumen Institucional</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><strong>RIF:</strong> <?php echo e($empleado->rifper); ?></li>
                        <li class="mb-2"><strong>Ingreso:</strong> <?php echo e(\Carbon\Carbon::parse($empleado->fecingper)->format('d/m/Y')); ?></li>
                        <li class="mb-2"><strong>Antigüedad:</strong> <br><span class="text-primary"><?php echo e($antiguedad); ?></span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2 text-success"></i> Ficha de Datos Personales</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Fecha de Nacimiento</label>
                            <div class="fs-6"><?php echo e(\Carbon\Carbon::parse($empleado->fecnacper)->format('d/m/Y')); ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Estado Civil / Sexo</label>
                            <div class="fs-6">
                                <?php
                                    $estados = ['S' => 'Soltero(a)', 'C' => 'Casado(a)', 'V' => 'Viudo(a)', 'D' => 'Divorciado(a)'];
                                    $estado = $estados[$empleado->edocivper] ?? 'No definido';
                                    $sexo = $empleado->sexper == 'M' ? 'Masculino' : 'Femenino';
                                ?>
                                <?php echo e($estado); ?> - <?php echo e($sexo); ?>

                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Teléfono Móvil</label>
                            <div class="fs-6"><?php echo e($empleado->telmovper ?? 'No registrado'); ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Teléfono Habitación</label>
                            <div class="fs-6"><?php echo e($empleado->telhabper ?? 'No registrado'); ?></div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Dirección de Domicilio</label>
                            <div class="fs-6 border-start ps-3 py-1 bg-light">
                                <?php echo e($empleado->dirper); ?>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Nivel Académico</label>
                            <div class="fs-6">
                                <?php
                                    $niveles = ['1' => 'Primaria', '2' => 'Bachiller', '3' => 'Universitario', '4' => 'Postgrado'];
                                ?>
                                <?php echo e($niveles[$empleado->nivacaper] ?? 'No especificado'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Nacionalidad</label>
                            <div class="fs-6"><?php echo e($empleado->nacper == 'V' ? 'Venezolano' : 'Extranjero'); ?></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted">Si observa algún dato incorrecto, por favor diríjase a la oficina de Recursos Humanos.</small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Sigh2025/resources/views/empleado/perfil.blade.php ENDPATH**/ ?>