<div class="table-footer">
    <span>Showing <strong>{{ $leads->firstItem() ?? 0 }}–{{ $leads->lastItem() ?? 0 }}</strong> of <strong>{{ $leads->total() }}</strong> leads</span>
    @if ($leads->hasPages())<div class="pagination-links">{{ $leads->onEachSide(1)->links('pagination.crm') }}</div>@else<span>{{ $leads->total() === 1 ? '1 lead in your workspace' : 'Up to 15 per page' }}</span>@endif
</div>
