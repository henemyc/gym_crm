<?php $__env->startSection('title', 'Schedule follow-up'); ?>
<?php $__env->startSection('page-name', 'Follow-ups'); ?>

<?php $__env->startSection('content'); ?>
    <div class="breadcrumb"><a href="<?php echo e(route('follow-ups.index')); ?>">Follow-ups</a><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-right','size' => '13']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','size' => '13']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><span>Schedule</span></div>
    <div class="page-heading"><div><div class="eyebrow"><span class="eyebrow-dot"></span> KEEP THE MOMENTUM</div><h1>Schedule a touchpoint</h1><p class="page-description">Give the next conversation a clear owner, time, and purpose.</p></div><a class="btn btn-secondary" href="<?php echo e($selectedLead ? route('leads.show', $selectedLead) : route('follow-ups.index')); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-left','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-left','size' => '16']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Back</a></div>
    <?php if($errors->any()): ?><div class="form-error-summary">Please review the form. <?php echo e($errors->first()); ?></div><?php endif; ?>
    <form method="POST" action="<?php echo e(route('follow-ups.store')); ?>" class="surface-card form-card">
        <?php echo csrf_field(); ?>
        <div class="form-section"><h2 class="form-section-title">Who and when</h2><div class="form-grid">
            <div class="form-field"><label class="field-label" for="lead_id">Lead <span class="required">*</span></label><select class="select-control" name="lead_id" id="lead_id" required><option value="">Select a lead</option><?php $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($lead->id); ?>" <?php if((string) old('lead_id', $selectedLead?->id) === (string) $lead->id): echo 'selected'; endif; ?>><?php echo e($lead->name); ?> · <?php echo e($lead->gym_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select><?php $__errorArgs = ['lead_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="field-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
            <div class="form-field"><label class="field-label" for="kind">Touchpoint type <span class="required">*</span></label><select class="select-control" name="kind" id="kind" required><?php $__currentLoopData = \App\Models\FollowUp::KINDS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($key); ?>" <?php if(old('kind', 'follow_up') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div class="form-field"><label class="field-label" for="due_at">Date & time <span class="required">*</span></label><input class="form-control" type="datetime-local" name="due_at" id="due_at" value="<?php echo e(old('due_at', now(auth()->user()->timezone)->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'))); ?>" required><?php $__errorArgs = ['due_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="field-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
            <div class="form-field"><label class="field-label" for="assigned_to">Owner</label><select class="select-control" name="assigned_to" id="assigned_to"><option value=""><?php echo e(auth()->user()->canManageTeam() ? 'Lead owner' : 'Me'); ?></option><?php $__currentLoopData = $team; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($member->id); ?>" <?php if((string) old('assigned_to', $selectedLead?->assigned_to) === (string) $member->id): echo 'selected'; endif; ?>><?php echo e($member->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        </div></div>
        <div class="form-section"><h2 class="form-section-title">Meeting details <span class="table-muted">· optional</span></h2><div class="form-grid"><div class="form-field"><label class="field-label" for="meeting_at">Meeting date & time</label><input class="form-control" type="datetime-local" name="meeting_at" id="meeting_at" value="<?php echo e(old('meeting_at')); ?>"><span class="field-hint">Add a separate meeting slot if it differs from the touchpoint time.</span></div><div class="form-field"><label class="field-label" for="meeting_with">Meeting with</label><input class="form-control" name="meeting_with" id="meeting_with" value="<?php echo e(old('meeting_with', $selectedLead?->name)); ?>" placeholder="Gym owner or attendee"></div></div></div>
        <div class="form-section"><h2 class="form-section-title">Trial details <span class="table-muted">· optional</span></h2><div class="form-grid"><div class="form-field"><label class="field-label" for="trial_at">Trial date & time</label><input class="form-control" type="datetime-local" name="trial_at" id="trial_at" value="<?php echo e(old('trial_at')); ?>"></div><div class="form-field"><label class="field-label" for="trial_details">Trial details</label><input class="form-control" name="trial_details" id="trial_details" value="<?php echo e(old('trial_details')); ?>" placeholder="Session focus, account setup, or attendees"></div></div></div>
        <div class="form-section"><div class="form-field"><label class="field-label" for="remark">Preparation note</label><textarea class="textarea-control" name="remark" id="remark" maxlength="5000" placeholder="What should the owner know before this conversation?"></textarea></div></div>
        <div class="form-actions"><a class="btn btn-secondary" href="<?php echo e($selectedLead ? route('leads.show', $selectedLead) : route('follow-ups.index')); ?>">Cancel</a><button class="btn btn-primary" type="submit"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'calendar-plus','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar-plus','size' => '16']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Schedule touchpoint</button></div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Laravel_Project\gxb_crm\resources\views/follow-ups/create.blade.php ENDPATH**/ ?>