<form method="GET" action="{{ route('admin.sellers') }}" class="filter-bar seller-filter-bar">
    <div class="search-box search-box--grow">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search sellers..." />
    </div>
    <label class="inline-select seller-inline-select">
        <i class="fa-solid fa-gear"></i>
        <select name="status" aria-label="Filter sellers by status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>
    <button class="action-button action-button--primary" type="submit">Filter</button>
    <a class="action-button action-button--light" href="{{ route('admin.sellers') }}">Reset</a>
</form>
