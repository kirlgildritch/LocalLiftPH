<div class="empty-box panel">
    <h3>No saved addresses yet</h3>
    <p>Add your first delivery address to speed up checkout.</p>
    <a href="{{ route('buyer.addresses.create', array_filter(['return_to' => $returnTo])) }}"
        class="action-btn primary-btn">Create Address</a>
</div>
