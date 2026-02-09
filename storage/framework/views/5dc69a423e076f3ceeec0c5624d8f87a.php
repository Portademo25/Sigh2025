<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white"><?php echo e(__('Inicio de Sesión')); ?></div>

                <div class="card-body">
                    <?php
                        // Sincronizamos con la variable 'user_verified' enviada desde el OnboardingController
                        // También permitimos que se mantenga visible si hay errores de validación de contraseña
                        $showPassword = session('user_verified') || $errors->has('password');
                        $formAction = $showPassword ? route('login') : route('auth.check_email');
                    ?>

                    
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?>

                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    
                    <?php if(session('info')): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <?php echo e(session('info')); ?>

                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e($formAction); ?>">
                        <?php echo csrf_field(); ?>

                        
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end"><?php echo e(__('Correo Institucional')); ?></label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-secondary">
                                        <i class="bi bi-envelope-fill"></i>
                                    </span>
                                    <input id="email" type="email"
                                           class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           name="email" value="<?php echo e(old('email')); ?>"
                                           required autofocus <?php echo e($showPassword ? 'readonly' : ''); ?>>

                                    <?php if($showPassword): ?>
                                        
                                        <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-secondary" title="Cambiar correo">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        
                        <?php if($showPassword): ?>
                            <div class="row mb-3 animate__animated animate__fadeInDown">
                                <label for="password" class="col-md-4 col-form-label text-md-end"><?php echo e(__('Contraseña')); ?></label>
                                <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text bg-light text-secondary">
                <i class="bi bi-lock-fill"></i>
            </span>

            <input id="password" type="password"
                   class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   name="password" required autocomplete="current-password">



            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="invalid-feedback" role="alert">
                    <strong><?php echo e($message); ?></strong>
                </span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="remember">
                                            <?php echo e(__('Recordarme')); ?>

                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <?php if(!$showPassword): ?>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <?php echo e(__('Continuar')); ?> <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-success px-4">
                                        <?php echo e(__('Entrar al Sistema')); ?> <i class="fas fa-sign-in-alt ms-2"></i>
                                    </button>
                                <?php endif; ?>

                                <?php if(Route::has('password.request')): ?>
                                    <a class="btn btn-link" href="<?php echo e(route('password.request')); ?>">
                                        <?php echo e(__('¿Olvidó su contraseña?')); ?>

                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/auth/login.blade.php ENDPATH**/ ?>