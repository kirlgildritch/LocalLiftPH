<article class="address-card panel">
    <div class="address-card-top">
        <div>
            <h3>{{ $address->full_name }}</h3>
            <p class="address-phone">{{ $address->phone }}</p>
        </div>

        <div class="address-tags">
            @if($address->is_default)
                <span class="tag default-tag">Default</span>
            @endif

            @if($address->label)
                <span class="tag">{{ $address->label }}</span>
            @endif
        </div>
    </div>

    <div class="address-content">
        <p>{{ $address->street_address }}</p>
        @if($address->landmark)
            <p>Landmark: {{ $address->landmark }}</p>
        @endif
        <p>
            {{ $address->barangay }},
            {{ $address->city }},
            {{ $address->province }},
            {{ $address->region }}
            @if($address->postal_code), {{ $address->postal_code }} @endif
        </p>
    </div>

    <div class="address-actions">
        @if(!$address->is_default)
            <form action="{{ route('buyer.addresses.default', $address) }}" method="POST">
                @csrf
                @method('PATCH')
                @if($returnTo)
                    <input type="hidden" name="return_to" value="{{ $returnTo }}">
                @endif
                <button type="submit" class="action-btn secondary-btn">Set Default</button>
            </form>
        @endif

        <button type="button" class="action-btn secondary-btn open-edit-address"
            data-id="{{ $address->id }}" data-full_name="{{ $address->full_name }}"
            data-phone="{{ $address->phone }}" data-region="{{ $address->region }}"
            data-province="{{ $address->province }}" data-city="{{ $address->city }}"
            data-barangay="{{ $address->barangay }}" data-postal_code="{{ $address->postal_code }}"
            data-landmark="{{ $address->landmark }}" data-label="{{ $address->label }}"
            data-street_address="{{ $address->street_address }}"
            data-is_default="{{ $address->is_default ? 1 : 0 }}">
            Edit
        </button>

        <form action="{{ route('buyer.addresses.destroy', $address) }}" method="POST"
            onsubmit="return confirm('Delete this address?')">
            @csrf
            @method('DELETE')
            @if($returnTo)
                <input type="hidden" name="return_to" value="{{ $returnTo }}">
            @endif
            <button type="submit" class="action-btn danger-btn">Delete</button>
        </form>
    </div>
</article>
