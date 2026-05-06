@php
    $steps = \App\Models\Order::progressStatuses();
    $stepKeys = array_keys($steps);
    $statusIndex = array_flip($stepKeys);

    $currentStatus = $order->shippingStatus();
    $progressStatus = $order->isCancelled()
        ? ($order->cancellation?->status_before_cancellation ?: \App\Models\Order::SHIPPING_PENDING)
        : $currentStatus;

    $currentStepIndex = $statusIndex[$progressStatus] ?? 0;
@endphp

<div class="order-progress-shell panel">
    <div class="order-progress-header">
        <div>
            <span class="toolbar-label">Shipping Status</span>
            <h3>{{ $order->isCancelled() ? 'Order Cancelled' : $order->shippingStatusLabel() }}</h3>
        </div>

        <div class="order-status-chip {{ $order->shippingToneClass() }}">
            @if($order->isCancelled())
                <i class="fa-solid fa-ban"></i>
            @else
                <i class="fa-solid fa-sparkles"></i>
            @endif
            <span>{{ $order->shippingStatusLabel() }}</span>
        </div>
    </div>

    <div class="order-stepper {{ $order->isCancelled() ? 'is-cancelled' : '' }}">
        @foreach($steps as $key => $meta)
            @php
                $stepIndex = $statusIndex[$key] ?? 0;
                $isComplete = $stepIndex < $currentStepIndex || (!$order->isCancelled() && $stepIndex < ($statusIndex[$currentStatus] ?? 0));
                $isActive = !$order->isCancelled() && $currentStatus === $key;
            @endphp

            <div class="step-item {{ $isComplete ? 'is-complete' : '' }} {{ $isActive ? 'is-active' : '' }}">
                <div class="step-line"></div>
                <div class="step-icon">
                    @if($isComplete)
                        <i class="fa-solid fa-check"></i>
                    @else
                        <i class="fa-solid {{ $meta['icon'] }}"></i>
                    @endif
                </div>
                <div class="step-copy">
                    <strong>{{ $meta['label'] }}</strong>
                    <span>
                        @if($isActive)
                            Current step
                        @elseif($isComplete)
                            Completed
                        @else
                            Waiting
                        @endif
                    </span>
                </div>
            </div>
        @endforeach

        @if($order->isCancelled())
            <div class="step-item is-cancelled is-active">
                <div class="step-line"></div>
                <div class="step-icon">
                    <i class="fa-solid fa-ban"></i>
                </div>
                <div class="step-copy">
                    <strong>Cancelled</strong>
                    <span>Order stopped before completion</span>
                </div>
            </div>
        @endif
    </div>

    @if($order->isCancelled() && $order->cancellationReasonLines())
        <div class="cancellation-reason-box">
            <span class="toolbar-label">Cancellation Reason</span>
            <div class="reason-list">
                @foreach($order->cancellationReasonLines() as $reason)
                    <div class="reason-chip">
                        <i class="fa-solid fa-circle-dot"></i>
                        <span>{{ $reason }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
