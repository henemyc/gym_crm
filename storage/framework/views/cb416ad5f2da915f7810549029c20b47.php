<?php ($modal = $modal ?? false); ?>
<form method="POST" action="<?php echo e($mode === 'create' ? route('leads.store') : route('leads.update', $lead)); ?>" class="<?php echo e($formClass ?? 'surface-card form-card'); ?>" <?php if($modal): ?> data-ajax-lead-form <?php endif; ?> <?php if(!empty($placeProspectId)): ?> data-place-prospect-id="<?php echo e($placeProspectId); ?>" <?php endif; ?> <?php if($mode === 'edit'): ?> data-lead-profile-url="<?php echo e(route('leads.show', $lead)); ?>" <?php endif; ?>>
    <?php echo csrf_field(); ?>
    <?php if($mode === 'edit'): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
    <div class="lead-form-alert" data-form-errors hidden role="alert"></div>
    <?php if(!empty($placeProspectId)): ?>
        <input type="hidden" name="place_prospect_id" value="<?php echo e($placeProspectId); ?>">
        <div class="google-verification-note"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'shield','size' => '15']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield','size' => '15']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><span>Enter details you independently verified from a first-party source or direct contact. Google-provided business fields are intentionally not prefilled or saved.</span></div>
        <div class="form-field google-verification-attestation">
            <label for="lead-independent-contact-verified"><input id="lead-independent-contact-verified" type="checkbox" name="independent_contact_verified" value="1" required><span>I confirm the contact name, gym name, and phone number above were independently verified, not copied solely from Google Places.</span></label>
            <?php $__errorArgs = ['independent_contact_verified'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="field-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    <?php endif; ?>

    <div class="form-section lead-form-section-contact">
        <h2 class="form-section-title">Contact & gym details</h2>
        <div class="form-grid">
            <div class="form-field"><label class="field-label" for="lead-name">Contact name <span class="required">*</span></label><input class="form-control" id="lead-name" name="name" value="<?php echo e(old('name', $lead->name)); ?>" placeholder="e.g. Ananya Sharma" maxlength="120" required><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="field-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
            <div class="form-field"><label class="field-label" for="lead-gym-name">Gym name <span class="required">*</span></label><input class="form-control" id="lead-gym-name" name="gym_name" value="<?php echo e(old('gym_name', $lead->gym_name)); ?>" placeholder="e.g. PulseFit Studio" maxlength="160" required><?php $__errorArgs = ['gym_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="field-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
            <div class="form-field"><label class="field-label" for="lead-phone">Phone number <span class="required">*</span></label><input class="form-control" id="lead-phone" name="phone" type="tel" value="<?php echo e(old('phone', $lead->phone)); ?>" placeholder="+91 98765 43210" maxlength="32" required><?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="field-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
            <div class="form-field"><label class="field-label" for="lead-location">Location</label><input class="form-control" id="lead-location" name="location" value="<?php echo e(old('location', $lead->location)); ?>" placeholder="City or area" maxlength="120"><?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="field-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
        </div>
    </div>

    <div class="form-section lead-form-section-qualification">
        <h2 class="form-section-title">Qualification & ownership</h2>
        <div class="form-grid form-grid-three">
            <div class="form-field"><label class="field-label" for="lead-source">Lead source <span class="required">*</span></label><select class="select-control" id="lead-source" name="source" required><?php $__currentLoopData = \App\Models\Lead::SOURCES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($source); ?>" <?php if(old('source', $lead->source) === $source): echo 'selected'; endif; ?>><?php echo e($source); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select><?php $__errorArgs = ['source'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="field-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
            <div class="form-field"><label class="field-label" for="lead-priority">Priority <span class="required">*</span></label><select class="select-control" id="lead-priority" name="priority" required><?php $__currentLoopData = \App\Models\Lead::PRIORITIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($key); ?>" <?php if(old('priority', $lead->priority) === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select><?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="field-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
            <div class="form-field"><label class="field-label" for="lead-status">Status <span class="required">*</span></label><select class="select-control" id="lead-status" name="status" required><?php $__currentLoopData = \App\Models\Lead::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($key); ?>" <?php if(old('status', $lead->status) === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select><?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="field-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
            <?php if(auth()->user()->canManageTeam()): ?>
                <div class="form-field"><label class="field-label" for="lead-assigned-to">Assigned to</label><select class="select-control" id="lead-assigned-to" name="assigned_to"><option value="">Unassigned</option><?php $__currentLoopData = $team; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($member->id); ?>" <?php if((string) old('assigned_to', $lead->assigned_to) === (string) $member->id): echo 'selected'; endif; ?>><?php echo e($member->name); ?> · <?php echo e(\App\Models\User::ROLES[$member->role] ?? 'Team'); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select><?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="field-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-section lead-form-section-notes">
        <h2 class="form-section-title">Context for your next conversation</h2>
        <div class="form-field"><label class="field-label" for="lead-notes">Notes <span class="field-optional">· optional</span></label><textarea class="textarea-control" id="lead-notes" name="notes" maxlength="10000" placeholder="Timing, membership size, current tools, or anything that helps your team follow through."><?php echo e(old('notes', $lead->notes)); ?></textarea><span class="field-hint">Keep the context specific and easy for a teammate to act on.</span><?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="field-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
    </div>

    <div class="form-actions">
        <?php if($modal): ?>
            <button class="btn btn-secondary" type="button" data-lead-dialog-close>Cancel</button>
        <?php else: ?>
            <a class="btn btn-secondary" href="<?php echo e($mode === 'edit' ? route('leads.show', $lead) : route('leads.index')); ?>">Cancel</a>
        <?php endif; ?>
        <button class="btn btn-primary" type="submit"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','size' => '16']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> <?php echo e($mode === 'create' ? 'Create lead' : 'Save changes'); ?></button>
    </div>
</form>
<?php /**PATH C:\Laravel_Project\gxb_crm\resources\views/leads/partials/fields.blade.php ENDPATH**/ ?>