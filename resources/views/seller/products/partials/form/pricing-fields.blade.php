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
        <label for="stock">{{ $stockLabel ?? 'Stock' }}</label>
        <input type="number" id="stock" name="stock" value="{{ $stockValue }}"
            placeholder="{{ $stockPlaceholder ?? '0' }}" min="0">
        @error('stock')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>
</div>
