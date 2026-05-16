<div class="variant-builder {{ $builderClass ?? '' }}" data-variant-builder data-next-index="{{ $variantRows->count() }}">
    <div class="variant-builder-head">
        <div>
            <label class="variant-toggle-label" for="has_variants">
                <input type="checkbox" id="has_variants" name="has_variants" value="1" data-variant-toggle {{ $variantsEnabled ? 'checked' : '' }}>
                <span>This product has variants</span>
            </label>
            <small class="product-media-note">{{ $variantHelpText }}</small>
        </div>
        <button type="button" class="table-action secondary" data-add-variant {{ $variantsEnabled ? '' : 'hidden' }}>
            Add Variant
        </button>
    </div>

    @error('variants')
        <small class="error-text">{{ $message }}</small>
    @enderror

    <div class="variant-list" data-variant-list {{ $variantsEnabled ? '' : 'hidden' }}>
        @foreach($variantRows as $index => $variantRow)
            @include('seller.products.partials.form.variant-row', [
                'index' => $index,
                'showExistingImageNote' => $showExistingImageNote ?? false,
                'showVariantId' => $showVariantId ?? false,
                'variantRow' => $variantRow,
            ])
        @endforeach
    </div>
</div>
