<?php if($showPlaceAssignmentNotice): ?>
    <div class="place-assignment-success" data-place-assignment-notice role="status" aria-live="polite">
        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check-circle','size' => '17']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check-circle','size' => '17']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
        <span>These Google Maps prospects are now available in Leads for follow-up. Existing assignments are left unchanged when a duplicate is selected.</span>
    </div>
<?php endif; ?>

<section class="surface-card place-prospect-queue lead-place-prospect-section" id="google-maps-prospects" data-place-queue data-queue-url="<?php echo e(route('prospecting.index', ['open_only' => 1])); ?>" data-queue-page-param="prospect_page" data-open-only="true" aria-labelledby="google-maps-prospects-title">
    <div class="card-heading place-queue-heading">
        <div>
            <div class="place-leads-eyebrow"><span class="place-source-badge">Google Maps</span><span>ASSIGNED PROSPECTS</span></div>
            <h2 id="google-maps-prospects-title">Google Maps prospects</h2>
            <p>Assigned prospects are visible to their teammate here. Google details stay live-only; create a standard lead after independently verifying contact details.</p>
        </div>
        <div class="place-leads-heading-actions">
            <span class="status-pill status-orange" data-place-open-count><?php echo e(number_format($placeProspectSummary['total'])); ?> open</span>
            <?php if(auth()->user()->canManageTeam()): ?>
                <a class="btn btn-secondary btn-sm" href="<?php echo e(route('prospecting.index')); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'search','size' => '14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','size' => '14']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Search gyms</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="place-queue-table-wrap">
        <table class="place-prospect-table">
            <thead><tr><th scope="col">Place / source</th><th scope="col">Assigned to</th><th scope="col">Status</th><th scope="col">Added</th><th scope="col">Actions</th></tr></thead>
            <tbody data-place-prospect-rows>
                <?php echo $__env->make('prospecting.partials.prospect-rows', [
                    'prospects' => $placeProspects,
                    'canManageTeam' => auth()->user()->canManageTeam(),
                    'team' => $team,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </tbody>
        </table>
    </div>
    <div data-place-pagination-wrap>
        <?php echo $__env->make('prospecting.partials.prospect-pagination', ['prospects' => $placeProspects], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</section>
<?php /**PATH C:\Laravel_Project\gxb_crm\resources\views/leads/partials/place-prospects.blade.php ENDPATH**/ ?>