<section class="checkout-card checkout-card--payment panel">
    <div class="card-header">
        <div class="step-title">
            <span class="step-number">1</span>
            <div>
                <span class="toolbar-label">Step</span>
                <h3>Shipping Address</h3>
            </div>
        </div>
        <a href="{{ route('buyer.addresses', ['return_to' => request()->fullUrl()]) }}" class="action-link">Edit</a>
    </div>

    <div class="card-body">
        <div class="shipping-address-box">
            @if(isset($defaultAddress) && $defaultAddress)
                <p><strong>{{ $defaultAddress->full_name ?? auth()->user()->name }}</strong></p>
                <p>{{ $defaultAddress->phone ?? 'No phone number' }}</p>

                @if(!empty($defaultAddress->street_address))
                    <p>{{ $defaultAddress->street_address }}</p>
                @endif

                @if(!empty($defaultAddress->landmark))
                    <p>Landmark: {{ $defaultAddress->landmark }}</p>
                @endif

                <p>
                    {{ $defaultAddress->barangay ?? '' }}
                    @if(!empty($defaultAddress->barangay) && !empty($defaultAddress->city)), @endif
                    {{ $defaultAddress->city ?? '' }}
                    @if(!empty($defaultAddress->province)), {{ $defaultAddress->province }}@endif
                    @if(!empty($defaultAddress->region)), {{ $defaultAddress->region }}@endif
                    @if(!empty($defaultAddress->postal_code)), {{ $defaultAddress->postal_code }}@endif
                </p>
            @else
                <p>Please add a delivery address before placing an order.</p>
                <a href="{{ route('buyer.addresses', ['return_to' => request()->fullUrl()]) }}" class="action-btn secondary-btn">
                    Add Delivery Address
                </a>
            @endif
        </div>
    </div>
</section>
