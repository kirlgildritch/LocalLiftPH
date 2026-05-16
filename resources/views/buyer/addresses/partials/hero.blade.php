<div class="address-hero panel">
    <div class="hero-copy">
        <span class="section-kicker">Address Book</span>
        <h1>Manage Delivery Addresses</h1>
    </div>

    <div class="hero-actions">
        @if($returnTo)
            <a href="{{ $returnTo }}" class="action-btn secondary-btn">
                <i class="fa-solid fa-arrow-left"></i>
                Back
            </a>
        @endif

        <a href="{{ route('buyer.addresses.create', array_filter(['return_to' => $returnTo])) }}"
            class="action-btn primary-btn">
            <i class="fa-solid fa-plus"></i>
            Add New Address
        </a>
    </div>
</div>
