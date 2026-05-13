<?php $__env->startSection('title', 'Iniciar sesión | Punto de Venta UBM'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fondoLogin = asset('images/fondo-login.jpg');
?>

<main class="login-screen login-screen-bg" style="background-image: linear-gradient(rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.65)), url('<?php echo e($fondoLogin); ?>');">
    <section class="login-card">
        <div class="brand-badge">PV</div>

        <span class="eyebrow">Acceso seguro</span>

        <h1>Iniciar sesión</h1>

        <p class="login-description">
            Ingresa con tu correo y contraseña para acceder al punto de venta.
        </p>

        <?php if(session('error')): ?>
            <div class="alert alert-error"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-error">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div><?php echo e($error); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login.store')); ?>" class="login-form">
            <?php echo csrf_field(); ?>

            <label>
                <span>Correo electrónico</span>
                <input 
                    type="email" 
                    name="email" 
                    value="<?php echo e(old('email')); ?>" 
                    placeholder="correo@ejemplo.com" 
                    autocomplete="email" 
                    required
                >
            </label>

            <label>
                <span>Contraseña</span>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="••••••••" 
                    autocomplete="current-password" 
                    required
                >
            </label>

            <button type="submit">Entrar al sistema</button>
        </form>

        <div class="auth-switch">
            ¿No tienes cuenta?
            <a href="<?php echo e(route('register')); ?>">Crear usuario cliente</a>
        </div>
    </section>

    <section class="login-info">
        <div>
            <span class="eyebrow light">Proyecto Con Laravel</span>

            <h2>
                Punto de venta con registro de clientes, compras y administración de productos.
            </h2>


        </div>
    </section>
</main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\adria\Desktop\xampp\htdocs\Proyecto\resources\views/auth/login.blade.php ENDPATH**/ ?>