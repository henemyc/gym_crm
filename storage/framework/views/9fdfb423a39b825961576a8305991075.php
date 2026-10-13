<?php $__env->startSection('title', 'Leads'); ?>
<?php $__env->startSection('page-name', 'Leads'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading leads-page-heading">
        <div><div class="eyebrow"><span class="eyebrow-dot"></span> SALES PIPELINE</div><h1>Leads <span class="heading-count" data-total-leads><?php echo e(number_format($leads->total())); ?></span></h1><p class="page-description">Search, qualify, and move every gym opportunity forward.</p></div>
        <div class="heading-actions"><a class="btn btn-secondary" href="<?php echo e(route('leads.export', request()->query())); ?>" data-lead-export data-lead-export-url="<?php echo e(route('leads.export')); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'download','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'download','size' => '16']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Export CSV</a><a class="btn btn-primary" href="<?php echo e(route('leads.create')); ?>" data-lead-form-url="<?php echo e(route('leads.create')); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','size' => '17']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','size' => '17']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Add lead</a></div>
    </div>

    <section class="lead-workspace" data-lead-index>
        <form class="leads-filterbar" method="GET" action="<?php echo e(route('leads.index')); ?>" data-lead-filter-form>
            <div class="leads-filterbar-main">
                <label class="lead-search-control"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'search','size' => '17']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','size' => '17']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><input type="search" name="q" value="<?php echo e($filters['q'] ?? ''); ?>" placeholder="Search leads, gyms or phone..." aria-label="Search leads" autocomplete="off"><span class="lead-search-spinner" data-lead-spinner hidden aria-label="Searching"></span></label>
                <select class="lead-inline-filter" name="status" aria-label="Filter by status"><option value="">All statuses</option><?php $__currentLoopData = \App\Models\Lead::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($key); ?>" <?php if(($filters['status'] ?? '') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                <select class="lead-inline-filter" name="priority" aria-label="Filter by priority"><option value="">All priorities</option><?php $__currentLoopData = \App\Models\Lead::PRIORITIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($key); ?>" <?php if(($filters['priority'] ?? '') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                <button class="btn btn-secondary btn-sm lead-more-filters" type="button" data-filter-panel-toggle aria-expanded="false" aria-controls="lead-advanced-filters"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'filter','size' => '15']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'filter','size' => '15']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> More filters <span class="filter-count" data-active-filter-count hidden>0</span></button>
                <a class="btn btn-quiet btn-sm lead-reset-filters" href="<?php echo e(route('leads.index')); ?>" data-reset-lead-filters>Reset</a>
                <button class="btn btn-primary btn-sm lead-apply-filters" type="submit"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Apply</button>
                <span class="lead-live-count" role="status" aria-live="polite"><span data-lead-result-label><?php echo e($leads->total()); ?> <?php echo e(\Illuminate\Support\Str::plural('lead', $leads->total())); ?></span></span>
            </div>
            <div class="lead-filter-popover" id="lead-advanced-filters" data-filter-panel hidden>
                <div class="lead-filter-popover-heading"><div><strong>Refine your pipeline</strong><small>These filters update results as you change them.</small></div><button class="icon-button" type="button" data-filter-panel-close aria-label="Close filters"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'x','size' => '17']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x','size' => '17']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></button></div>
                <div class="lead-advanced-grid">
                    <label class="form-field"><span class="field-label">Source</span><select class="filter-select" name="source"><option value="">All sources</option><?php $__currentLoopData = $sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($source); ?>" <?php if(($filters['source'] ?? '') === $source): echo 'selected'; endif; ?>><?php echo e($source); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></label>
                    <label class="form-field"><span class="field-label">Location</span><select class="filter-select" name="location"><option value="">All locations</option><?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($location); ?>" <?php if(($filters['location'] ?? '') === $location): echo 'selected'; endif; ?>><?php echo e($location); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></label>
                    <label class="form-field"><span class="field-label">Added from</span><input class="filter-date" type="date" name="from" value="<?php echo e($filters['from'] ?? ''); ?>"></label>
                    <label class="form-field"><span class="field-label">Added to</span><input class="filter-date" type="date" name="to" value="<?php echo e($filters['to'] ?? ''); ?>"></label>
                    <?php if(auth()->user()->canManageTeam()): ?><label class="form-field"><span class="field-label">Owner</span><select class="filter-select" name="assigned_to"><option value="">All owners</option><?php $__currentLoopData = $team; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($member->id); ?>" <?php if((string) ($filters['assigned_to'] ?? '') === (string) $member->id): echo 'selected'; endif; ?>><?php echo e($member->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></label><?php endif; ?>
                    <label class="form-field"><span class="field-label">Rows per page</span><select class="filter-select" name="per_page"><option value="15" <?php if(($filters['per_page'] ?? '15') == '15'): echo 'selected'; endif; ?>>15 leads</option><option value="30" <?php if(($filters['per_page'] ?? '') == '30'): echo 'selected'; endif; ?>>30 leads</option><option value="50" <?php if(($filters['per_page'] ?? '') == '50'): echo 'selected'; endif; ?>>50 leads</option></select></label>
                </div>
            </div>
        </form>

        <div class="bulk-toolbar" data-bulk-toolbar hidden>
            <strong><span data-selected-count>0</span> selected</strong><span>Choose an action for these leads.</span><span class="toolbar-spacer"></span>
            <button type="button" class="btn btn-quiet btn-sm" data-clear-selection>Clear selection</button>
            <button type="button" class="btn btn-danger btn-sm" data-bulk-delete-trigger><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'trash','size' => '14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','size' => '14']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Delete selected</button>
        </div>
        <form id="bulk-delete-form" method="POST" action="<?php echo e(route('leads.bulk-destroy')); ?>" data-ajax-bulk-delete><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><div data-selected-hidden></div></form>

        <section class="table-card lead-table-card" aria-label="Lead results">
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr><th class="check-cell"><input class="select-all" type="checkbox" data-select-all aria-label="Select all leads on this page"></th><th>Lead</th><th>Gym & location</th><th>Phone</th><th>Source</th><th>Priority</th><th>Status</th><th>Assigned to</th><th>Added</th><th aria-label="Actions"></th></tr></thead>
                    <tbody data-lead-results><?php echo $__env->make('leads.partials.rows', ['leads' => $leads, 'activeFilters' => $activeFilters], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></tbody>
                </table>
            </div>
            <div data-lead-footer><?php echo $__env->make('leads.partials.footer', ['leads' => $leads], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
        </section>

        <?php echo $__env->make('leads.partials.place-prospects', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Laravel_Project\gxb_crm\resources\views/leads/index.blade.php ENDPATH**/ ?>