<div class="place-queue-pagination" data-place-pagination>
    <span>Page <?php echo e($prospects->currentPage()); ?> of <?php echo e(max(1, $prospects->lastPage())); ?></span>
    <div class="place-queue-pagination-actions">
        <button class="btn btn-secondary btn-sm" type="button" data-place-queue-page="<?php echo e(max(1, $prospects->currentPage() - 1)); ?>" <?php if($prospects->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
        <button class="btn btn-secondary btn-sm" type="button" data-place-queue-page="<?php echo e(min($prospects->lastPage(), $prospects->currentPage() + 1)); ?>" <?php if(! $prospects->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
    </div>
</div>
<?php /**PATH C:\Laravel_Project\gxb_crm\resources\views/prospecting/partials/prospect-pagination.blade.php ENDPATH**/ ?>