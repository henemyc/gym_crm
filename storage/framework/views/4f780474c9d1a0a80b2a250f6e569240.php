<?php $__env->startSection('title', 'Calendar'); ?>
<?php $__env->startSection('page-name', 'Calendar'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading"><div><div class="eyebrow"><span class="eyebrow-dot"></span> PLAN THE NEXT CONVERSATION</div><h1>Calendar</h1><p class="page-description">Follow-ups, meetings, and trials in one shared view.</p></div><a class="btn btn-primary" href="<?php echo e(route('follow-ups.create')); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Schedule touchpoint</a></div>
    <section class="surface-card">
        <div class="calendar-toolbar">
            <div class="calendar-nav"><a class="icon-button" href="<?php echo e(route('calendar.index', ['view' => $viewMode, 'date' => $previous])); ?>" aria-label="Previous <?php echo e($viewMode); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-left','size' => '18']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-left','size' => '18']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></a><h2><?php echo e($title); ?></h2><a class="icon-button" href="<?php echo e(route('calendar.index', ['view' => $viewMode, 'date' => $next])); ?>" aria-label="Next <?php echo e($viewMode); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-right','size' => '18']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','size' => '18']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></a><a class="btn btn-secondary btn-sm" href="<?php echo e(route('calendar.index', ['view' => $viewMode, 'date' => $today])); ?>">Today</a></div>
            <div class="calendar-controls"><div class="calendar-legend"><span><i class="legend-follow"></i> Follow-up</span><span><i class="legend-meeting"></i> Meeting</span><span><i class="legend-trial"></i> Trial</span></div><div class="tab-bar" aria-label="Calendar view"><?php $__currentLoopData = ['month'=>'Month','week'=>'Week','day'=>'Day']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="tab-link <?php echo e($viewMode === $key ? 'active' : ''); ?>" href="<?php echo e(route('calendar.index', ['view'=>$key, 'date'=>$focusDate->toDateString()])); ?>"><?php echo e($label); ?></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div>
        </div>
        <?php if($viewMode === 'day'): ?>
            <div class="agenda-list">
                <?php $__empty_1 = true; $__currentLoopData = ($eventsByDate->get($focusDate->toDateString()) ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="agenda-day"><h3><?php echo e($event['time']); ?> · <?php echo e($event['kind_label']); ?></h3><a class="agenda-event" href="<?php echo e(route('leads.show', $event['follow_up']->lead)); ?>"><span class="calendar-event event-<?php echo e($event['type']); ?>"><?php echo e($event['title']); ?></span><strong><?php echo e($event['gym']); ?></strong><small><?php echo e($event['follow_up']->assignee?->name ?? 'Unassigned'); ?></small></a></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state"><span class="empty-icon"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'calendar','size' => '23']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar','size' => '23']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span><strong>A little breathing room</strong><p>No touchpoints are scheduled for this day. Add one whenever you’re ready.</p><a class="btn btn-secondary btn-sm" href="<?php echo e(route('follow-ups.create')); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','size' => '14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','size' => '14']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Schedule touchpoint</a></div>
                <?php endif; ?>
            </div>
        <?php elseif($viewMode === 'week'): ?>
            <div class="calendar-grid calendar-week-view">
                <?php $__currentLoopData = $cells; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="calendar-week-day"><div class="calendar-week-head <?php echo e($date->toDateString() === $today ? 'is-today' : ''); ?>"><small><?php echo e($date->translatedFormat('D')); ?></small><strong><?php echo e($date->format('j')); ?></strong></div><div class="calendar-day-events"><?php $__empty_1 = true; $__currentLoopData = ($eventsByDate->get($date->toDateString()) ?? collect())->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><a class="calendar-event event-<?php echo e($event['type']); ?>" href="<?php echo e(route('leads.show', $event['follow_up']->lead)); ?>" title="<?php echo e($event['time']); ?> · <?php echo e($event['title']); ?>"><?php echo e($event['time']); ?> <?php echo e($event['title']); ?></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><span class="calendar-empty-cell">No plans</span><?php endif; ?></div></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="calendar-grid month-grid">
                <?php $__currentLoopData = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weekday): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="calendar-weekday"><?php echo e($weekday); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $cells; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $dayEvents = $eventsByDate->get($date->toDateString(), collect()); ?>
                    <div class="calendar-day <?php echo e($date->month !== $focusDate->month ? 'is-outside' : ''); ?> <?php echo e($date->toDateString() === $today ? 'is-today' : ''); ?>"><div class="calendar-day-top"><span class="calendar-day-number"><?php echo e($date->format('j')); ?></span><?php if($dayEvents->count() > 3): ?><span class="table-muted"><?php echo e($dayEvents->count()); ?> events</span><?php endif; ?></div><div class="calendar-day-events"><?php $__currentLoopData = $dayEvents->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="calendar-event event-<?php echo e($event['type']); ?>" href="<?php echo e(route('leads.show', $event['follow_up']->lead)); ?>" title="<?php echo e($event['time']); ?> · <?php echo e($event['title']); ?>"><?php echo e($event['time']); ?> <?php echo e($event['title']); ?></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php if($dayEvents->count() > 3): ?><a class="calendar-more" href="<?php echo e(route('calendar.index', ['view'=>'day','date'=>$date->toDateString()])); ?>">+<?php echo e($dayEvents->count() - 3); ?> more</a><?php endif; ?></div></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
        <div class="calendar-footer"><span>All times in <?php echo e(auth()->user()->timezone ?: 'Asia/Kolkata'); ?></span><span class="calendar-legend"><i class="legend-follow"></i> Follow-up <i class="legend-meeting"></i> Meeting <i class="legend-trial"></i> Trial</span></div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Laravel_Project\gxb_crm\resources\views/calendar/index.blade.php ENDPATH**/ ?>