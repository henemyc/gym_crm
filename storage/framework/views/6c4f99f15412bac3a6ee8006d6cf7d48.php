<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="theme-color" content="#f5f6f7">
    <title><?php echo $__env->yieldContent('title', 'Sign in'); ?> · GymXBook CRM</title>
    <?php
        $publicBase = rtrim(request()->getBaseUrl(), '/');
        $cssFile = public_path('css/app.css');
        $jsFile = public_path('js/app.js');
        $cssVersion = is_file($cssFile) ? filemtime($cssFile) : '1';
        $jsVersion = is_file($jsFile) ? filemtime($jsFile) : '1';
    ?>
    <link rel="stylesheet" href="<?php echo e($publicBase); ?>/css/app.css?v=<?php echo e($cssVersion); ?>">
    <script src="<?php echo e($publicBase); ?>/js/app.js?v=<?php echo e($jsVersion); ?>" defer></script>
</head>
<body class="auth-page auth-minimal-page">
    <?php echo $__env->make('layouts.partials.icons', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <main class="auth-minimal-shell">
        <a class="brand brand-auth auth-minimal-brand" href="<?php echo e(route('login')); ?>" aria-label="GymXBook CRM home">
            <span class="brand-mark"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'spark','size' => '19']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'spark','size' => '19']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span>
            <span class="brand-copy"><strong>GymXBook</strong><small>GROWTH DESK</small></span>
        </a>
        <div class="auth-wrap">
            <section class="auth-card">
                <?php if(session('success')): ?>
                    <div class="alert alert-success"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check-circle','size' => '18']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check-circle','size' => '18']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><?php echo e(session('success')); ?></div>
                <?php endif; ?>
                <?php echo $__env->yieldContent('content'); ?>
            </section>
            <footer class="auth-foot"><span>© <?php echo e(date('Y')); ?> <span class="brand-word">GymXBook</span> CRM</span><span aria-hidden="true">·</span><span>Private workspace · Secure sign-in</span></footer>
        </div>
    </main>
</body>
</html>
<?php /**PATH C:\Laravel_Project\gxb_crm\resources\views/layouts/guest.blade.php ENDPATH**/ ?>