<?php $__env->startSection('title', 'Settings'); ?>
<?php $__env->startSection('page-name', 'Settings'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading"><div><div class="eyebrow"><span class="eyebrow-dot"></span> YOUR SPACE, YOUR RHYTHM</div><h1>Settings</h1><p class="page-description">Manage your account, preferences, and the people in your workspace.</p></div></div>
    <div class="settings-layout">
        <div class="settings-main-stack">
            <section class="surface-card form-card"><div class="card-heading" style="padding:0 0 17px"><div><h2>Profile details</h2><p>How your teammates see you around <span class="brand-word">GymXBook</span>.</p></div><span class="status-pill status-orange"><?php echo e(\App\Models\User::ROLES[auth()->user()->role] ?? 'Team member'); ?></span></div>
                <?php if($errors->any()): ?><div class="form-error-summary"><?php echo e($errors->first()); ?></div><?php endif; ?>
                <form method="POST" action="<?php echo e(route('settings.profile')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="form-grid"><div class="form-field"><label class="field-label" for="name">Full name</label><input class="form-control" id="name" name="name" value="<?php echo e(old('name', auth()->user()->name)); ?>" required></div><div class="form-field"><label class="field-label" for="email">Work email</label><input class="form-control" type="email" id="email" name="email" value="<?php echo e(old('email', auth()->user()->email)); ?>" required></div><div class="form-field"><label class="field-label" for="phone">Phone number</label><input class="form-control" type="tel" id="phone" name="phone" value="<?php echo e(old('phone', auth()->user()->phone)); ?>" placeholder="+91 98765 43210"></div><div class="form-field"><label class="field-label" for="timezone">Timezone</label><select class="select-control" id="timezone" name="timezone"><?php $__currentLoopData = ['Asia/Kolkata'=>'India Standard Time · Kolkata','Asia/Dubai'=>'Gulf Standard Time · Dubai','Asia/Singapore'=>'Singapore Standard Time','Europe/London'=>'Greenwich Mean Time · London','America/New_York'=>'Eastern Time · New York','UTC'=>'Coordinated Universal Time']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($value); ?>" <?php if(old('timezone', auth()->user()->timezone) === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div></div>
                    <div class="form-actions"><button class="btn btn-primary" type="submit"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check','size' => '15']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','size' => '15']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Save profile</button></div>
                </form>
            </section>
            <section class="surface-card form-card"><div class="card-heading" style="padding:0 0 17px"><div><h2>Security</h2><p>Keep your <span class="brand-word">GymXBook</span> workspace in good hands.</p></div><span class="metric-icon metric-icon-green"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'shield','size' => '17']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield','size' => '17']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span></div>
                <form method="POST" action="<?php echo e(route('settings.password')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="form-grid"><div class="form-field"><label class="field-label" for="current_password">Current password</label><input class="form-control" type="password" id="current_password" name="current_password" autocomplete="current-password" required></div><div class="form-field"><label class="field-label" for="password">New password</label><input class="form-control" type="password" id="password" name="password" autocomplete="new-password" required><span class="field-hint">At least 10 characters with letters and numbers.</span></div><div class="form-field"><label class="field-label" for="password_confirmation">Confirm new password</label><input class="form-control" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required></div></div>
                    <div class="form-actions"><button class="btn btn-secondary" type="submit">Update password</button></div>
                </form>
            </section>
            <section class="surface-card form-card"><div class="card-heading" style="padding:0 0 13px"><div><h2>Notifications</h2><p>Choose how <span class="brand-word">GymXBook</span> keeps you in the loop.</p></div></div>
                <form method="POST" action="<?php echo e(route('settings.notifications')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?><div class="setting-line"><div><strong>Workspace reminders</strong><small>Keep follow-up and assignment reminders enabled in your workspace.</small></div><label class="toggle"><input type="checkbox" name="notifications_enabled" value="1" <?php if(auth()->user()->notifications_enabled): echo 'checked'; endif; ?>><span class="toggle-track"></span></label></div><div class="form-actions"><button class="btn btn-secondary" type="submit">Save preferences</button></div></form>
            </section>
            <?php if(auth()->user()->isAdmin()): ?>
                <?php echo $__env->make('settings.partials.google-places-card', ['placesIntegration' => $placesIntegration, 'placesUsage' => $placesUsage], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>
        </div>
        <aside class="settings-side-stack"><section class="surface-card settings-nav"><a class="active" href="<?php echo e(route('settings.index')); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'user','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user','size' => '16']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Profile & preferences</a><?php if(auth()->user()->canManageTeam()): ?><a href="<?php echo e(route('settings.users')); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'users','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'users','size' => '16']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Staff & activity</a><?php endif; ?></section><section class="surface-card settings-info-card"><h3><span class="brand-word">GymXBook</span> Fitness</h3><p>Workspace settings are managed by the people responsible for your sales team.</p><div class="setting-line"><div><strong>Your role</strong><small>Controls what you can view and manage.</small></div><span class="role-badge <?php echo e(auth()->user()->role); ?>"><?php echo e(\App\Models\User::ROLES[auth()->user()->role] ?? 'Member'); ?></span></div><?php if(auth()->user()->canManageTeam()): ?><a class="btn btn-secondary btn-sm" href="<?php echo e(route('settings.users')); ?>">Open staff panel <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-right','size' => '14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','size' => '14']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></a><?php endif; ?></section><section class="surface-card settings-info-card"><h3>Need a hand?</h3><p>Your team admin can help with access, assignment, and workspace questions.</p></section></aside>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Laravel_Project\gxb_crm\resources\views/settings/index.blade.php ENDPATH**/ ?>