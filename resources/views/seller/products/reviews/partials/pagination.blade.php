                    @if($reviews->hasPages())
                    <div class="seller-review-pagination">
                        @if($reviews->onFirstPage())
                        <span class="table-action secondary is-disabled">Previous</span>
                        @else
                        <a href="{{ $reviews->previousPageUrl() }}" class="table-action secondary">Previous</a>
                        @endif

                        <span class="seller-review-pagination-meta">
                            Page {{ $reviews->currentPage() }} of {{ $reviews->lastPage() }}
                        </span>

                        @if($reviews->hasMorePages())
                        <a href="{{ $reviews->nextPageUrl() }}" class="table-action secondary">Next</a>
                        @else
                        <span class="table-action secondary is-disabled">Next</span>
                        @endif
                    </div>
                    @endif
