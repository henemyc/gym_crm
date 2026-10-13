<?php $__env->startSection('title', 'Follow-ups'); ?>
<?php $__env->startSection('page-name', 'Follow-ups'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading"><div><div class="eyebrow"><span class="eyebrow-dot"></span> NO CONVERSATION LEFT BEHIND</div><h1>Follow-ups</h1><p class="page-description">A calm, reliable rhythm for every relationship in your pipeline.</p></div><div class="heading-actions"><a class="btn btn-secondary" href="<?php echo e(route('calendar.index')); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'calendar','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar','size' => '16']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Open calendar</a><a class="btn btn-primary" href="<?php echo e(route('follow-ups.create')); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Schedule touchpoint</a></div></div>
    <div class="toolbar-card followup-tabs-row"><div class="tab-bar" role="tablist" aria-label="Follow-up views">
        <?php $__currentLoopData = ['today' => 'Today', 'upcoming' => 'Upcoming', 'overdue' => 'Overdue', 'completed' => 'Completed', 'all' => 'All']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="tab-link <?php echo e($activeView === $key ? 'active' : ''); ?>" href="<?php echo e(route('follow-ups.index', array_merge(request()->except('page'), ['view' => $key]))); ?>" role="tab" aria-selected="<?php echo e($activeView === $key ? 'true' : 'false'); ?>"><?php echo e($label); ?><span class="tab-count"><?php echo e($counts[$key]); ?></span></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div><form class="table-search" method="GET" action="<?php echo e(route('follow-ups.index')); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'search','size' => '15']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','size' => '15']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><input type="hidden" name="view" value="<?php echo e($activeView); ?>"><input type="search" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search lead or gym..." aria-label="Search follow-ups"></form></div>

    <?php if($followUps->isEmpty()): ?>
        <section class="surface-card"><div class="empty-state"><span class="empty-icon"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => ''.e($activeView === 'completed' ? 'check-circle' : 'clock').'','size' => '24']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($activeView === 'completed' ? 'check-circle' : 'clock').'','size' => '24']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span><strong><?php echo e($activeView === 'today' ? 'A clear day, a good sign' : 'No touchpoints in this view'); ?></strong><p><?php echo e($activeView === 'today' ? 'There are no follow-ups due today. Check upcoming or schedule your next conversation.' : 'Try another view or create a new touchpoint to keep things moving.'); ?></p><a class="btn btn-primary btn-sm" href="<?php echo e(route('follow-ups.create')); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Schedule touchpoint</a></div></section>
    <?php else: ?>
        <div class="followup-list">
            <?php $__currentLoopData = $followUps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $followUp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $isOverdue = ! $followUp->completed_at && $followUp->due_at->lt($today->copy()->startOfDay()); $accent = $followUp->completed_at ? 'completed' : ($isOverdue ? 'overdue' : ($followUp->kind === 'meeting' ? 'meeting' : ($followUp->kind === 'trial' ? 'trial' : ''))); ?>
                <article class="followup-row-card">
                    <span class="followup-accent <?php echo e($accent); ?>"></span>
                    <div class="followup-lead-info"><a href="<?php echo e(route('leads.show', $followUp->lead)); ?>"><strong><?php echo e($followUp->lead->name); ?></strong></a><small><?php echo e($followUp->lead->gym_name); ?> <span class="mini-sep">·</span> <?php echo e($followUp->lead->location ?: 'Location not set'); ?></small></div>
                    <div class="followup-when"><strong><?php echo e($followUp->due_at->format('D, j M · g:i A')); ?></strong><small><?php if($followUp->completed_at): ?>Completed <?php echo e($followUp->completed_at->diffForHumans()); ?><?php elseif($isOverdue): ?>Overdue <?php echo e($followUp->due_at->diffForHumans()); ?><?php elseif($followUp->due_at->isToday()): ?>Due today@else<?php echo e($followUp->due_at->diffForHumans()); ?><?php endif; ?> <span class="mini-sep">·</span> <?php echo e(\App\Models\FollowUp::KINDS[$followUp->kind] ?? crm_label($followUp->kind)); ?></small></div>
                    <div class="followup-owner"><span class="avatar avatar-soft avatar-blue"><?php echo e($followUp->assignee?->initials() ?? '?'); ?></span><div><strong><?php echo e($followUp->assignee?->name ?? 'Unassigned'); ?></strong><small>Owner</small></div></div>
                    <div class="followup-controls">
                        <?php if($followUp->completed_at): ?>
                            <div class="followup-result" title="<?php echo e($followUp->remark); ?>"><span class="status-pill status-converted"><?php echo e(\App\Models\FollowUp::OUTCOMES[$followUp->outcome] ?? 'Completed'); ?></span></div>
                            <a class="row-action" href="<?php echo e(route('leads.show', $followUp->lead)); ?>" title="Open lead"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
                        <?php else: ?>
                            <details class="complete-details"><summary class="btn btn-secondary btn-sm"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check','size' => '14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','size' => '14']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Complete</summary>
                                <form class="complete-panel" method="POST" action="<?php echo e(route('follow-ups.complete', $followUp)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <strong>Log the outcome</strong>
                                    <div class="form-field"><label class="field-label" for="outcome-<?php echo e($followUp->id); ?>">Outcome</label><select class="select-control" id="outcome-<?php echo e($followUp->id); ?>" name="outcome" required><option value="">Choose outcome</option><?php $__currentLoopData = \App\Models\FollowUp::OUTCOMES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($key); ?>"><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                                    <div class="form-field"><label class="field-label" for="remark-<?php echo e($followUp->id); ?>">Remark</label><textarea class="textarea-control" name="remark" id="remark-<?php echo e($followUp->id); ?>" rows="2" placeholder="What happened on the call?"></textarea></div>
                                    <div class="form-field"><label class="field-label" for="next-<?php echo e($followUp->id); ?>">Next follow-up <span class="table-muted">· optional</span></label><input class="form-control" type="datetime-local" name="next_follow_up_at" id="next-<?php echo e($followUp->id); ?>"></div>
                                    <div class="form-grid"><div class="form-field"><label class="field-label" for="meeting-<?php echo e($followUp->id); ?>">Meeting date/time</label><input class="form-control" type="datetime-local" name="meeting_at" id="meeting-<?php echo e($followUp->id); ?>"></div><div class="form-field"><label class="field-label" for="meeting-with-<?php echo e($followUp->id); ?>">Meeting with</label><input class="form-control" name="meeting_with" id="meeting-with-<?php echo e($followUp->id); ?>" placeholder="Attendee"></div><div class="form-field"><label class="field-label" for="trial-<?php echo e($followUp->id); ?>">Trial date/time</label><input class="form-control" type="datetime-local" name="trial_at" id="trial-<?php echo e($followUp->id); ?>"></div><div class="form-field"><label class="field-label" for="trial-details-<?php echo e($followUp->id); ?>">Trial details</label><input class="form-control" name="trial_details" id="trial-details-<?php echo e($followUp->id); ?>" placeholder="Session details"></div></div>
                                    <div class="complete-actions"><button type="submit" class="btn btn-primary btn-sm"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check','size' => '14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','size' => '14']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Save outcome</button></div>
                                </form>
                            </details>
                            <form method="POST" action="<?php echo e(route('follow-ups.destroy', $followUp)); ?>" data-confirm="Remove this follow-up from the calendar?"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="row-action danger" type="submit" title="Remove follow-up"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?></button></form>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($followUps->hasPages()): ?><div class="table-footer"><span>Showing <?php echo e($followUps->firstItem()); ?>–<?php echo e($followUps->lastItem()); ?> of <?php echo e($followUps->total()); ?></span><div class="pagination-links"><?php echo e($followUps->onEachSide(1)->links('pagination.crm')); ?></div></div><?php endif; ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Laravel_Project\gxb_crm\resources\views/follow-ups/index.blade.php ENDPATH**/ ?>