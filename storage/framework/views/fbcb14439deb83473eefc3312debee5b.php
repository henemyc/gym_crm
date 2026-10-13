<div class="table-footer">
    <span>Showing <strong><?php echo e($leads->firstItem() ?? 0); ?>–<?php echo e($leads->lastItem() ?? 0); ?></strong> of <strong><?php echo e($leads->total()); ?></strong> leads</span>
    <?php if($leads->hasPages()): ?><div class="pagination-links"><?php echo e($leads->onEachSide(1)->links('pagination.crm')); ?></div><?php else: ?><span><?php echo e($leads->total() === 1 ? '1 lead in your workspace' : 'Up to 15 per page'); ?></span><?php endif; ?>
</div>
<?php /**PATH C:\Laravel_Project\gxb_crm\resources\views/leads/partials/footer.blade.php ENDPATH**/ ?>