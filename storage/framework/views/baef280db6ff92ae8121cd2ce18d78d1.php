<?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php $avatarColors = ['orange','blue','green','purple','rose']; $avatarColor = $avatarColors[($lead->id - 1) % count($avatarColors)]; ?>
    <tr data-lead-row data-lead-id="<?php echo e($lead->id); ?>">
        <td class="check-cell"><?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $lead)): ?><input class="row-checkbox" type="checkbox" value="<?php echo e($lead->id); ?>" data-row-select aria-label="Select <?php echo e($lead->name); ?>"><?php else: ?><span class="row-check-empty" aria-label="Selection unavailable"></span><?php endif; ?></td>
        <td><div class="lead-cell"><span class="avatar avatar-soft avatar-<?php echo e($avatarColor); ?>"><?php echo e(collect(explode(' ', $lead->name))->take(2)->map(fn($piece) => mb_substr($piece, 0, 1))->implode('')); ?></span><div class="lead-cell-text"><a href="<?php echo e(route('leads.show', $lead)); ?>" data-lead-preview-url="<?php echo e(route('leads.show', $lead)); ?>"><?php echo e($lead->name); ?></a><small>Lead #<?php echo e(str_pad((string) $lead->id, 4, '0', STR_PAD_LEFT)); ?></small></div></div></td>
        <td><div class="gym-cell"><strong><?php echo e($lead->gym_name); ?></strong><small><?php echo e($lead->location ?: 'Location not set'); ?></small></div></td>
        <td><a class="phone-link" href="tel:<?php echo e(preg_replace('/[^0-9+]/', '', $lead->phone)); ?>"><?php echo e($lead->phone); ?></a></td>
        <td><?php if($lead->source === 'Google Maps'): ?><span class="place-source-badge place-source-table-tag">Google Maps</span><?php else: ?><span class="table-muted"><?php echo e($lead->source); ?></span><?php endif; ?></td>
        <td><span class="priority-pill priority-<?php echo e($lead->priority); ?>"><i></i><?php echo e(ucfirst($lead->priority)); ?></span></td>
        <td><span class="status-pill status-<?php echo e(str_replace('_', '-', $lead->status)); ?>"><?php echo e(\App\Models\Lead::STATUSES[$lead->status] ?? crm_label($lead->status)); ?></span></td>
        <td><span class="assignee-cell"><span class="avatar avatar-soft"><?php echo e($lead->assignee?->initials() ?? '?'); ?></span><span><?php echo e($lead->assignee?->name ?? 'Unassigned'); ?></span></span></td>
        <td class="table-muted"><?php echo e($lead->created_at->format('d M Y')); ?></td>
        <td><span class="row-actions">
            <a class="row-action" href="<?php echo e(route('leads.show', $lead)); ?>" data-lead-preview-url="<?php echo e(route('leads.show', $lead)); ?>" title="Preview lead" aria-label="Preview <?php echo e($lead->name); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'external','size' => '15']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'external','size' => '15']); ?>
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
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $lead)): ?><a class="row-action" href="<?php echo e(route('leads.edit', $lead)); ?>" data-lead-form-url="<?php echo e(route('leads.edit', $lead)); ?>" title="Edit lead" aria-label="Edit <?php echo e($lead->name); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'edit','size' => '15']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit','size' => '15']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></a><?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $lead)): ?><form class="inline-row-form" method="POST" action="<?php echo e(route('leads.destroy', $lead)); ?>" data-ajax-lead-delete data-lead-delete-name="<?php echo e($lead->name); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="row-action danger" type="submit" title="Delete lead" aria-label="Delete <?php echo e($lead->name); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'trash','size' => '15']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','size' => '15']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></button></form><?php endif; ?>
        </span></td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr><td colspan="10"><div class="empty-state"><span class="empty-icon"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'users','size' => '24']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'users','size' => '24']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span><strong><?php echo e($activeFilters->isNotEmpty() ? 'No leads match those filters' : 'Your pipeline starts here'); ?></strong><p><?php echo e($activeFilters->isNotEmpty() ? 'Try a different search or clear a filter to see more leads.' : 'Add your first gym lead or import a spreadsheet to build your pipeline.'); ?></p><?php if($activeFilters->isNotEmpty()): ?><button class="btn btn-secondary btn-sm" type="button" data-clear-lead-filters>Clear filters</button><?php else: ?><a class="btn btn-primary btn-sm" href="<?php echo e(route('leads.create')); ?>" data-lead-form-url="<?php echo e(route('leads.create')); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','size' => '15']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','size' => '15']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Add your first lead</a><?php endif; ?></div></td></tr>
<?php endif; ?>
<?php /**PATH C:\Laravel_Project\gxb_crm\resources\views/leads/partials/rows.blade.php ENDPATH**/ ?>