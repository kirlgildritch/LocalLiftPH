<div class="variant-row" data-variant-row>
    @if(($showVariantId ?? false) && !empty($variantRow['id']))
        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variantRow['id'] }}">
    @endif

    <div class="form-group">
        <label>Variant Name</label>
        <input type="text" name="variants[{{ $index }}][name]" value="{{ $variantRow['name'] ?? '' }}" placeholder="e.g. Small / Red">
        @error("variants.$index.name")
            <small class="error-text">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label>SKU</label>
        <input type="text" name="variants[{{ $index }}][sku]" value="{{ $variantRow['sku'] ?? '' }}" placeholder="Optional">
    </div>

    <div class="form-group">
        <label>Price</label>
        <input type="number" name="variants[{{ $index }}][price]" value="{{ $variantRow['price'] ?? '' }}" step="0.01" min="0" placeholder="0.00">
        @error("variants.$index.price")
            <small class="error-text">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label>Stock</label>
        <input type="number" name="variants[{{ $index }}][stock]" value="{{ $variantRow['stock'] ?? '' }}" min="0" placeholder="0">
        @error("variants.$index.stock")
            <small class="error-text">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label>Image</label>
        <input type="file" name="variants[{{ $index }}][image]" accept="image/*">
        @if(($showExistingImageNote ?? false) && !empty($variantRow['image']))
            <small class="product-media-note">Current image saved.</small>
        @endif
    </div>

    <div class="variant-row-actions">
        <input type="hidden" name="variants[{{ $index }}][is_active]" value="0">
        <label class="variant-active-toggle">
            <input type="checkbox" name="variants[{{ $index }}][is_active]" value="1" {{ (bool) ($variantRow['is_active'] ?? true) ? 'checked' : '' }}>
            Active
        </label>
        <button type="button" class="table-action danger" data-remove-variant>Remove</button>
    </div>
</div>
