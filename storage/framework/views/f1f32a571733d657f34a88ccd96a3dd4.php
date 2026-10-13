<section class="surface-card form-card google-places-settings-card" data-google-settings-card>
    <div class="card-heading google-settings-heading">
        <div>
            <h2>Google Places integration</h2>
            <p>Configure server-side gym discovery and review live API usage safeguards.</p>
        </div>
        <span class="status-pill <?php echo e($placesIntegration['configured'] ? 'status-converted' : 'status-orange'); ?>" data-google-key-state>
            <?php echo e($placesIntegration['configured'] ? 'Key saved' : 'Not connected'); ?>

        </span>
    </div>

    <div class="google-key-status" data-google-key-status role="status" aria-live="polite">
        <?php if($placesIntegration['configured']): ?>
            <strong>Configured key:</strong> <?php echo e($placesIntegration['key_hint']); ?>.
            <?php if($placesIntegration['verified_at']): ?>
                Last verified <?php echo e($placesIntegration['verified_at']->format('d M Y, g:i A')); ?>.
            <?php else: ?>
                Connection has not been tested after the latest key change.
            <?php endif; ?>
        <?php else: ?>
            Add an API key restricted to the server IP and to Places API (New) plus Geocoding API.
        <?php endif; ?>
    </div>

    <form class="google-key-form" method="POST" action="<?php echo e(route('settings.google-places.update')); ?>" data-google-settings-form data-no-loading>
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="form-field">
            <label class="field-label" for="google-places-api-key">Google Maps Platform API key</label>
            <div class="google-key-input-row">
                <input class="form-control" id="google-places-api-key" type="password" name="api_key" minlength="20" maxlength="255" autocomplete="new-password" spellcheck="false" autocapitalize="off" placeholder="Paste a new server-side key" required>
                <button class="btn btn-primary" type="submit" data-google-key-save>Save key</button>
            </div>
            <span class="field-hint">Encrypted in the database; never sent to the browser. Saving a replacement clears the previous verification.</span>
            <?php $__errorArgs = ['api_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="field-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="google-key-actions">
            <button class="btn btn-secondary btn-sm" type="button" data-google-key-test data-test-url="<?php echo e(route('settings.google-places.test')); ?>" <?php if(! $placesIntegration['configured']): echo 'disabled'; endif; ?>>
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'activity','size' => '14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'activity','size' => '14']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Test connection
            </button>
            <small class="google-test-note">A test uses one Geocoding request and one Places Text Search Pro request.</small>
            <button class="btn btn-secondary btn-sm google-disconnect-btn" type="button" data-google-key-clear data-delete-url="<?php echo e(route('settings.google-places.destroy')); ?>" <?php if (! ($placesIntegration['configured'])): ?> hidden <?php endif; ?>>
                Disconnect key
            </button>
            <a class="google-cloud-help-link" href="https://console.cloud.google.com/google/maps-apis/credentials" target="_blank" rel="noopener noreferrer">Manage keys in Google Cloud <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'external','size' => '13']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'external','size' => '13']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></a>
        </div>
    </form>

    <div class="google-usage-panel" aria-labelledby="google-usage-title">
        <div class="google-usage-heading">
            <div><h3 id="google-usage-title">GymXBook monthly request guard</h3><p>Billing month <?php echo e($placesUsage['month']); ?> · resets on Google billing time (<?php echo e($placesUsage['timezone']); ?>).</p></div>
            <span class="status-pill status-orange">Hard stop enabled</span>
        </div>
        <div class="google-usage-grid">
            <?php $__currentLoopData = $placesUsage['categories']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sku => $usage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="google-usage-item" data-google-usage-sku="<?php echo e($sku); ?>">
                    <div class="google-usage-item-heading"><span><?php echo e($usage['label']); ?></span><strong><span data-google-usage-value><?php echo e(number_format($usage['used'])); ?></span> <small>/ <?php echo e(number_format($usage['cap'])); ?></small></strong></div>
                    <div class="google-usage-track" role="progressbar" aria-label="<?php echo e($usage['label']); ?>" aria-valuemin="0" aria-valuemax="<?php echo e($usage['cap']); ?>" aria-valuenow="<?php echo e($usage['used']); ?>" data-google-usage-progress><span style="width: <?php echo e($usage['percent']); ?>%"></span></div>
                    <small><span><?php echo e($usage['tier']); ?></span> · <span data-google-usage-remaining><?php echo e(number_format($usage['remaining'])); ?></span> requests left in the GymXBook cap</small>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <p class="google-usage-warning"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'alert','size' => '15']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert','size' => '15']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> These are per-app safeguards, not a Google Cloud invoice meter. They assume an India billing profile and leave headroom below current India free caps; usage by other projects or APIs on the same billing account can consume that allowance. Google Cloud budget alerts do not stop charges—set API quotas lower than the account’s remaining free allowance.</p>
    </div>
</section>
<?php /**PATH C:\Laravel_Project\gxb_crm\resources\views/settings/partials/google-places-card.blade.php ENDPATH**/ ?>