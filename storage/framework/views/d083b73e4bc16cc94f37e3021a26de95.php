<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Panel</a></li>
                        <li class="breadcrumb-item active">Configuración de Correo</li>
                    </ol>
                </nav>
                
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Regresar al Dashboard
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex align-items-center">
                    <i class="bi bi-envelope-at-fill me-2"></i>
                    <h5 class="mb-0">Servidor de Correo Institucional (SMTP)</h5>
                </div>
                <div class="card-body p-4">

                    <?php if(session('success')): ?>
                        <div class="alert alert-success border-0 shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i> <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($errors->has('test')): ?>
                        <div class="alert alert-danger border-0 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo e($errors->first('test')); ?>

                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('admin.mail.update')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-uppercase">Servidor (Host)</label>
                                <input type="text" name="host" class="form-control" value="<?php echo e(old('host', $mail->host ?? '')); ?>" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase">Puerto</label>
                                <input type="number" name="port" class="form-control" value="<?php echo e(old('port', $mail->port ?? '')); ?>" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase">Cifrado</label>
                                <select name="encryption" class="form-select">
                                    <option value="tls" <?php echo e((old('encryption', $mail->encryption ?? '') == 'tls') ? 'selected' : ''); ?>>TLS</option>
                                    <option value="ssl" <?php echo e((old('encryption', $mail->encryption ?? '') == 'ssl') ? 'selected' : ''); ?>>SSL</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Usuario / Correo</label>
                                <input type="text" name="username" class="form-control" value="<?php echo e(old('username', $mail->username ?? '')); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Contraseña</label>
                                <input type="password" name="password" class="form-control" value="<?php echo e(old('password', $mail->password ?? '')); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Remitente (Email)</label>
                                <input type="email" name="from_address" class="form-control" value="<?php echo e(old('from_address', $mail->from_address ?? '')); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Nombre Remitente</label>
                                <input type="text" name="from_name" class="form-control" value="<?php echo e(old('from_name', $mail->from_name ?? '')); ?>" required>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Configuración
                            </button>
                    </form>

                    
                    <?php if($mail): ?>
                        <form action="<?php echo e(route('admin.mail.test')); ?>" method="POST" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-outline-info">
                                <i class="bi bi-send-check"></i> Enviar Correo de Prueba
                            </button>
                        </form>
                    <?php endif; ?>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Sigh2025/resources/views/admin/settings/mail.blade.php ENDPATH**/ ?>