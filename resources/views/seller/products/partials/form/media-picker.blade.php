<div class="form-group form-group-wide">
    <label for="media">{{ $label }}</label>
    <input type="file" id="media" name="media[]" accept="image/*,video/*" multiple data-product-media-input>
    <small class="product-media-note">{{ $note }}</small>
    @error('media')
        <span class="error-text">{{ $message }}</span>
    @enderror
    @error('media.*')
        <span class="error-text">{{ $message }}</span>
    @enderror
    @error('image')
        <span class="error-text">{{ $message }}</span>
    @enderror
    <div class="product-media-preview" data-product-media-preview hidden></div>
</div>
