@php($wrapperClass = trim($wrapperClass ?? ''))
<div{{ $wrapperClass !== '' ? ' class=' . '"' . e($wrapperClass) . '"' : '' }}>
    <div class="form-group form-group-wide">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" value="{{ $nameValue }}" placeholder="{{ $namePlaceholder ?? 'Enter product name' }}">
        @error('name')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="category_id">Category</label>
        <select id="category_id" name="category_id">
            <option value="">Select category</option>
            @foreach (($categories ?? collect()) as $category)
                <option value="{{ $category->id }}" {{ (string) $categoryValue === (string) $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="condition">Condition</label>
        <select id="condition" name="condition">
            @if(($includeConditionPlaceholder ?? true))
                <option value="">Select condition</option>
            @endif
            <option value="new" {{ $conditionValue === 'new' ? 'selected' : '' }}>New</option>
            <option value="used" {{ $conditionValue === 'used' ? 'selected' : '' }}>Used</option>
        </select>
        @error('condition')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group form-group-wide">
        <label for="description">{{ $descriptionLabel ?? 'Description' }}</label>
        @if(($descriptionMode ?? 'textarea') === 'quill')
            <div id="editor" style="height: {{ $editorHeight ?? '220px' }};">{!! $descriptionValue !!}</div>
            <input type="hidden" name="description" id="description">
        @else
            <textarea id="description" name="description" rows="{{ $descriptionRows ?? 7 }}"
                placeholder="{{ $descriptionPlaceholder ?? 'Describe your product' }}">{{ $descriptionValue }}</textarea>
        @endif
        @error('description')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>
</div>
