<article class="table-card seller-table-card">
    <div class="seller-table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Seller</th>
                    <th>Top Category</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Date Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sellers as $index => $seller)
                    @include('admin.sellers.partials.table-row', [
                        'seller' => $seller,
                        'index' => $index,
                        'sellers' => $sellers,
                        'avatarClasses' => $avatarClasses,
                        'publicMediaUrl' => $publicMediaUrl,
                    ])
                @empty
                    <tr>
                        <td colspan="6" class="sub-line">No seller applications found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($sellers->hasPages())
        @include('admin.sellers.partials.pagination', ['sellers' => $sellers])
    @endif
</article>
