<div class="place-queue-pagination" data-place-pagination>
    <span>Page {{ $prospects->currentPage() }} of {{ max(1, $prospects->lastPage()) }}</span>
    <div class="place-queue-pagination-actions">
        <button class="btn btn-secondary btn-sm" type="button" data-place-queue-page="{{ max(1, $prospects->currentPage() - 1) }}" @disabled($prospects->onFirstPage())>Previous</button>
        <button class="btn btn-secondary btn-sm" type="button" data-place-queue-page="{{ min($prospects->lastPage(), $prospects->currentPage() + 1) }}" @disabled(! $prospects->hasMorePages())>Next</button>
    </div>
</div>
