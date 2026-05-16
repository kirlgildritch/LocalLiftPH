<div style="overflow-x:auto;">
    <table class="data-table">
        <thead>
            <tr>
                <th class="checkbox-cell">
                    <input class="selection-checkbox" id="select-all-products" type="checkbox"
                        aria-label="Select all products">
                </th>
                <th>Product</th>
                <th>Seller</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                @include('admin.product-approvals.table-row', [
                    'product' => $product,
                    'statusBadge' => $statusBadge,
                    'money' => $money,
                    'publicMediaUrl' => $publicMediaUrl,
                ])
            @empty
                <tr>
                    <td colspan="5" class="sub-line empty-table">{{ $statusMeta[$currentTab]['empty'] }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
