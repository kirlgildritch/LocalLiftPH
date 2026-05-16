@php($wrapperClass = trim($wrapperClass ?? ''))
<div{{ $wrapperClass !== '' ? ' class=' . '"' . e($wrapperClass) . '"' : '' }}>
    <div class="form-group">
        <label for="price">{{ $priceLabel ?? 'Price' }}</label>
        <input type="number" id="price" name="price" value="{{ $priceValue }}"
            placeholder="{{ $pricePlaceholder ?? '0.00' }}" step="0.01" min="0">
        @error('price')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="discount_type">Discount Type</label>
        <select id="discount_type" name="discount_type">
            <option value="">No discount</option>
            <option value="percent" {{ ($discountTypeValue ?? '') === 'percent' ? 'selected' : '' }}>Percentage</option>
            <option value="fixed" {{ ($discountTypeValue ?? '') === 'fixed' ? 'selected' : '' }}>Fixed amount</option>
        </select>
        @error('discount_type')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="discount_value">Discount Value</label>
        <input type="number" id="discount_value" name="discount_value"
            value="{{ $discountValue ?? '' }}" placeholder="Optional" step="0.01" min="0">
        @error('discount_value')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="stock">{{ $stockLabel ?? 'Stock' }}</label>
        <input type="number" id="stock" name="stock" value="{{ $stockValue }}"
            placeholder="{{ $stockPlaceholder ?? '0' }}" min="0">
        @error('stock')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>
</div>
