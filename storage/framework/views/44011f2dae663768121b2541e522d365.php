<?php
    $viewer = auth()->user();
    $palette = ['orange', 'blue', 'green', 'purple', 'rose'];
    $canEditMember = $viewer->isAdmin() || ($member->role === 'sales' && $member->id !== $viewer->id);
    $canDeleteMember = $member->id !== $viewer->id && ($viewer->isAdmin() || $member->role === 'sales');
?>
<tr data-team-presence-row="<?php echo e($member->id); ?>">
    <td>
        <div class="team-member">
            <span class="avatar avatar-soft avatar-<?php echo e($palette[$member->id % count($palette)]); ?>"><?php echo e($member->initials()); ?></span>
            <span>
                <strong><?php echo e($member->name); ?> <?php if($member->id === $viewer->id): ?><span class="table-muted">(you)</span><?php endif; ?></strong>
                <small><?php echo e($member->email); ?><?php echo e($member->phone ? ' · '.$member->phone : ''); ?></small>
            </span>
        </div>
    </td>
    <td><span class="role-badge <?php echo e($member->role); ?>"><?php echo e(\App\Models\User::ROLES[$member->role] ?? 'Member'); ?></span></td>
    <td><span class="staff-lead-count"><?php echo e(number_format($member->assigned_leads_count)); ?></span></td>
    <td>
        <?php if($member->is_active && $member->isOnlineNow()): ?>
            <span class="presence-state presence-online" data-team-presence-state><i class="presence-dot"></i><span data-team-presence-label>Online now</span></span>
            <small class="staff-presence-detail" data-team-presence-detail>Active <?php echo e($member->last_activity_at->diffForHumans()); ?></small>
        <?php elseif($member->last_activity_at): ?>
            <span class="presence-state presence-offline" data-team-presence-state><i class="presence-dot"></i><span data-team-presence-label><?php echo e($member->is_active ? 'Offline' : 'Inactive'); ?></span></span>
            <small class="staff-presence-detail" data-team-presence-detail>Last seen <?php echo e($member->last_activity_at->diffForHumans()); ?></small>
        <?php else: ?>
            <span class="presence-state presence-unknown" data-team-presence-state><i class="presence-dot"></i><span data-team-presence-label><?php echo e($member->is_active ? 'Not seen yet' : 'Inactive'); ?></span></span>
            <small class="staff-presence-detail" data-team-presence-detail>No activity recorded</small>
        <?php endif; ?>
    </td>
    <td data-team-login-cell>
        <?php if($member->last_login_at): ?>
            <span class="staff-login-date" data-team-login-date><?php echo e($member->last_login_at->timezone($viewerTimezone)->format('d M Y')); ?></span>
            <small class="staff-presence-detail" data-team-login-time><?php echo e($member->last_login_at->timezone($viewerTimezone)->format('g:i A')); ?></small>
        <?php else: ?>
            <span class="staff-login-date staff-login-never" data-team-login-date>Never signed in</span>
            <small class="staff-presence-detail" data-team-login-time>First sign-in pending</small>
        <?php endif; ?>
    </td>
    <td>
        <?php if($canEditMember || $canDeleteMember): ?>
            <div class="staff-row-actions">
                <?php if($canEditMember): ?>
                    <button class="btn btn-secondary btn-sm" type="button" data-team-edit
                        data-update-url="<?php echo e(route('settings.users.update', $member)); ?>"
                        data-member-name="<?php echo e($member->name); ?>"
                        data-member-email="<?php echo e($member->email); ?>"
                        data-member-phone="<?php echo e($member->phone); ?>"
                        data-member-role="<?php echo e($member->role); ?>"
                        data-member-active="<?php echo e($member->is_active ? '1' : '0'); ?>"
                        data-member-self="<?php echo e($member->id === $viewer->id ? 'true' : 'false'); ?>">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'edit','size' => '14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit','size' => '14']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Edit
                    </button>
                <?php endif; ?>
                <?php if($canDeleteMember): ?>
                    <button class="btn btn-secondary btn-sm staff-remove-btn" type="button" data-team-delete
                        data-delete-url="<?php echo e(route('settings.users.destroy', $member)); ?>"
                        data-member-name="<?php echo e($member->name); ?>">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Remove
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <span class="table-muted">Managed by admin</span>
        <?php endif; ?>
    </td>
</tr>
<?php /**PATH C:\Laravel_Project\gxb_crm\resources\views/settings/partials/member-row.blade.php ENDPATH**/ ?>