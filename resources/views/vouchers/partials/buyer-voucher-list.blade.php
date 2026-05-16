@php
    $vouchers = collect($vouchers ?? []);
    $title = $title ?? 'Available Vouchers';
    $emptyText = $emptyText ?? null;
@endphp

@if($vouchers->isNotEmpty())
    <div class="buyer-voucher-list">
        <div class="buyer-voucher-list__head">
            <span class="section-kicker">{{ $title }}</span>
        </div>

        <div class="buyer-voucher-grid">
            @foreach($vouchers as $voucher)
                <article class="buyer-voucher-card">
                    <div class="buyer-voucher-card__icon">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <div class="buyer-voucher-card__body">
                        <strong>{{ $voucher['code'] }}</strong>
                        <span>{{ $voucher['label'] }}</span>
                        <small>
                            Min spend PHP {{ number_format($voucher['minimum_subtotal'], 2) }}
                            @if($voucher['maximum_discount'])
                                | Cap PHP {{ number_format($voucher['maximum_discount'], 2) }}
                            @endif
                            @if($voucher['ends_at'])
                                | Until {{ $voucher['ends_at']->format('M d, Y') }}
                            @endif
                        </small>
                    </div>
                    @if(!empty($voucher['apply_url']))
                        <a href="{{ $voucher['apply_url'] }}" class="buyer-voucher-card__action">Use</a>
                    @else
                        <button
                            type="button"
                            class="buyer-voucher-card__action"
                            data-copy-voucher-code="{{ $voucher['code'] }}"
                        >
                            Use code
                        </button>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
@elseif($emptyText)
    <p class="buyer-voucher-empty">{{ $emptyText }}</p>
@endif
