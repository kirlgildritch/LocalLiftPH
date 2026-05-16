@extends('layouts.seller')

@section('title', 'Seller Vouchers')

@section('content')
    @php
        $voucher = $editingVoucher;
        $isEditing = $voucher !== null;
        $formAction = $isEditing ? route('seller.vouchers.update', $voucher) : route('seller.vouchers.store');
        $formMethod = $isEditing ? 'PATCH' : 'POST';
        $voucherTimezone = $voucherTimezone ?? 'Asia/Manila';
    @endphp

    <section class="dashboard-wrapper">
        <div class="container">
            <div class="dashboard-layout">
                @include('seller.partials.sidebar')

                <main class="dashboard-main">
                    <section class="seller-page-panel panel">
                        <div class="page-header">
                            <div>
                                <span class="section-kicker">Promotions</span>
                                <h2>Seller Vouchers</h2>
                            </div>
                        </div>

                        <form action="{{ $formAction }}" method="POST" class="seller-application-form">
                            @csrf
                            @if($isEditing)
                                @method($formMethod)
                            @endif

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="code">Voucher Code</label>
                                    <input type="text" id="code" name="code" value="{{ old('code', $voucher?->code) }}" placeholder="SHOP10">
                                    @error('code')<small class="error-text">{{ $message }}</small>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="name">Voucher Name</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $voucher?->name) }}" placeholder="Shop launch discount">
                                    @error('name')<small class="error-text">{{ $message }}</small>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="type">Discount Type</label>
                                    <select id="type" name="type">
                                        @foreach([\App\Models\Voucher::TYPE_FIXED => 'Fixed Amount', \App\Models\Voucher::TYPE_PERCENT => 'Percentage'] as $type => $label)
                                            <option value="{{ $type }}" {{ old('type', $voucher?->type ?? \App\Models\Voucher::TYPE_FIXED) === $type ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')<small class="error-text">{{ $message }}</small>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="value">Discount Value</label>
                                    <input type="number" id="value" name="value" value="{{ old('value', $voucher?->value) }}" min="0.01" step="0.01" placeholder="50">
                                    @error('value')<small class="error-text">{{ $message }}</small>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="minimum_subtotal">Minimum Subtotal</label>
                                    <input type="number" id="minimum_subtotal" name="minimum_subtotal" value="{{ old('minimum_subtotal', $voucher?->minimum_subtotal ?? 0) }}" min="0" step="0.01">
                                    @error('minimum_subtotal')<small class="error-text">{{ $message }}</small>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="maximum_discount">Max Discount</label>
                                    <input type="number" id="maximum_discount" name="maximum_discount" value="{{ old('maximum_discount', $voucher?->maximum_discount) }}" min="0.01" step="0.01" placeholder="Optional">
                                    @error('maximum_discount')<small class="error-text">{{ $message }}</small>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="usage_limit">Total Usage Limit</label>
                                    <input type="number" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $voucher?->usage_limit) }}" min="1" step="1" placeholder="Optional">
                                    @error('usage_limit')<small class="error-text">{{ $message }}</small>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="per_user_limit">Per Buyer Limit</label>
                                    <input type="number" id="per_user_limit" name="per_user_limit" value="{{ old('per_user_limit', $voucher?->per_user_limit) }}" min="1" step="1" placeholder="Optional">
                                    @error('per_user_limit')<small class="error-text">{{ $message }}</small>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="starts_at">Starts At</label>
                                    <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at', $voucher?->starts_at?->copy()->timezone($voucherTimezone)->format('Y-m-d\\TH:i')) }}">
                                    @error('starts_at')<small class="error-text">{{ $message }}</small>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="ends_at">Ends At</label>
                                    <input type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at', $voucher?->ends_at?->copy()->timezone($voucherTimezone)->format('Y-m-d\\TH:i')) }}">
                                    @error('ends_at')<small class="error-text">{{ $message }}</small>@enderror
                                </div>

                                <div class="form-group form-group-wide">
                                    <label class="voucher-toggle">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $voucher?->is_active ?? true) ? 'checked' : '' }}>
                                        <span>Active voucher</span>
                                    </label>
                                    @error('is_active')<small class="error-text">{{ $message }}</small>@enderror
                                </div>
                            </div>

                            <div class="form-actions">
                                @if($isEditing)
                                    <a href="{{ route('seller.vouchers.index') }}" class="table-action secondary">Cancel</a>
                                @endif
                                <button type="submit" class="page-action-btn">
                                    {{ $isEditing ? 'Update Voucher' : 'Create Voucher' }}
                                </button>
                            </div>
                        </form>
                    </section>

                    <section class="seller-page-panel panel">
                        <div class="panel-heading">
                            <div>
                                <span class="section-kicker">Existing Codes</span>
                                <h2>Your Vouchers</h2>
                            </div>
                        </div>

                        <div class="table-panel table-panel--scroll">
                            <table class="seller-table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Discount</th>
                                        <th>Minimum</th>
                                        <th>Usage</th>
                                        <th>Schedule</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vouchers as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->code }}</strong>
                                                <span class="muted-label">{{ $item->name ?: 'Seller voucher' }}</span>
                                            </td>
                                            <td>
                                                {{ $item->type === \App\Models\Voucher::TYPE_PERCENT ? rtrim(rtrim(number_format((float) $item->value, 2), '0'), '.') . '%' : 'PHP ' . number_format((float) $item->value, 2) }}
                                            </td>
                                            <td>PHP {{ number_format((float) $item->minimum_subtotal, 2) }}</td>
                                            <td>
                                                {{ $item->redemptions_count }} / {{ $item->usage_limit ?: 'Unlimited' }}
                                            </td>
                                            <td>
                                                    <span class="muted-label">
                                                    {{ $item->starts_at?->copy()->timezone($voucherTimezone)->format('M d, Y') ?? 'Now' }}
                                                    -
                                                    {{ $item->ends_at?->copy()->timezone($voucherTimezone)->format('M d, Y') ?? 'No end' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-chip {{ $item->is_active ? 'delivered' : 'cancelled' }}">
                                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('seller.vouchers.edit', $item) }}" class="table-action secondary">Edit</a>
                                                    <form action="{{ route('seller.vouchers.destroy', $item) }}" method="POST" data-enable-loading>
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="table-action danger">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="empty-text">No seller vouchers yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $vouchers->links() }}
                    </section>
                </main>
            </div>
        </div>
    </section>
@endsection
