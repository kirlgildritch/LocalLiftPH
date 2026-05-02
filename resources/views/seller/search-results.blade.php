@extends('layouts.seller')

@section('content')
    <section class="dashboard-wrapper">
        <div class="container">
            <div class="dashboard-layout">
                @include('seller.partials.sidebar')

                <main class="dashboard-main">
                    <section class="seller-page-panel panel seller-search-page">
                        <div class="page-header seller-search-page__header">
                            <div>
                                <span class="section-kicker">Seller Search</span>
                                <h2>Search Results</h2>
                                @if ($query !== '')
                                    <p>Results for "<strong>{{ $query }}</strong>" across your seller tools, products, orders, and messages.</p>
                                @else
                                    <p>Search your seller dashboard for products, orders, messages, and tools.</p>
                                @endif
                            </div>
                        </div>

                        @php
                            $hasResults = $query !== '' && (
                                $toolResults->isNotEmpty()
                                || $products->isNotEmpty()
                                || $orders->isNotEmpty()
                                || $conversations->isNotEmpty()
                            );
                        @endphp

                        @if ($query === '')
                            <div class="seller-search-empty panel">
                                <h3>Start typing in the seller search bar.</h3>
                                <p>Try product names, order numbers, buyer names, or tool names like Settings, Messages, or Earnings.</p>
                            </div>
                        @elseif (! $hasResults)
                            <div class="seller-search-empty panel">
                                <h3>No seller results found.</h3>
                                <p>Try a product name, buyer name, order number, or tool label like My Products or Settings.</p>
                            </div>
                        @else
                            @if ($toolResults->isNotEmpty())
                                <section class="seller-search-section">
                                    <div class="seller-search-section__title">
                                        <h3>Seller Tools</h3>
                                        <span>{{ $toolResults->count() }} result{{ $toolResults->count() !== 1 ? 's' : '' }}</span>
                                    </div>

                                    <div class="seller-search-tool-grid">
                                        @foreach ($toolResults as $tool)
                                            <a href="{{ $tool['url'] }}" class="seller-search-tool-card panel">
                                                <strong>{{ $tool['label'] }}</strong>
                                                <p>{{ $tool['description'] }}</p>
                                            </a>
                                        @endforeach
                                    </div>
                                </section>
                            @endif

                            @if ($products->isNotEmpty())
                                <section class="seller-search-section">
                                    <div class="seller-search-section__title">
                                        <h3>Products</h3>
                                        <span>{{ $products->count() }} result{{ $products->count() !== 1 ? 's' : '' }}</span>
                                    </div>

                                    <div class="seller-search-list">
                                        @foreach ($products as $product)
                                            <a href="{{ route('seller.products.edit', $product) }}" class="seller-search-result-card panel">
                                                <div class="seller-search-result-card__main">
                                                    <strong>{{ $product->name }}</strong>
                                                    <p>{{ \Illuminate\Support\Str::limit($product->description, 120) }}</p>
                                                    <small>{{ $product->category?->name ?? 'Uncategorized' }}</small>
                                                </div>
                                                <span class="seller-search-result-card__meta">&#8369;{{ number_format((float) $product->price, 2) }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </section>
                            @endif

                            @if ($orders->isNotEmpty())
                                <section class="seller-search-section">
                                    <div class="seller-search-section__title">
                                        <h3>Orders</h3>
                                        <span>{{ $orders->count() }} result{{ $orders->count() !== 1 ? 's' : '' }}</span>
                                    </div>

                                    <div class="seller-search-list">
                                        @foreach ($orders as $order)
                                            <a href="{{ route('seller.orders') }}" class="seller-search-result-card panel">
                                                <div class="seller-search-result-card__main">
                                                    <strong>Order #{{ $order->id }}</strong>
                                                    <p>{{ $order->user->name ?? 'Customer' }} · {{ $order->shippingStatusLabel() }}</p>
                                                    <small>{{ $order->items->pluck('product.name')->filter()->take(2)->implode(', ') ?: 'No product details' }}</small>
                                                </div>
                                                <span class="seller-search-result-card__meta">&#8369;{{ number_format((float) $order->total_price, 2) }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </section>
                            @endif

                            @if ($conversations->isNotEmpty())
                                <section class="seller-search-section">
                                    <div class="seller-search-section__title">
                                        <h3>Messages</h3>
                                        <span>{{ $conversations->count() }} result{{ $conversations->count() !== 1 ? 's' : '' }}</span>
                                    </div>

                                    <div class="seller-search-list">
                                        @foreach ($conversations as $conversation)
                                            <a href="{{ route('seller.messages.show', $conversation) }}" class="seller-search-result-card panel">
                                                <div class="seller-search-result-card__main">
                                                    <strong>{{ $conversation->buyer?->name ?? 'Buyer' }}</strong>
                                                    <p>{{ \Illuminate\Support\Str::limit($conversation->latestMessage?->message ?: 'Open conversation', 110) }}</p>
                                                    <small>Updated {{ $conversation->updated_at?->diffForHumans() }}</small>
                                                </div>
                                                <span class="seller-search-result-card__meta">Open chat</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </section>
                            @endif
                        @endif
                    </section>
                </main>
            </div>
        </div>
    </section>

    <style>
        .seller-search-page {
            display: grid;
            gap: 22px;
        }

        .seller-search-page__header p {
            margin: 10px 0 0;
            color: #8fa7c4;
            line-height: 1.7;
        }

        .seller-search-empty {
            padding: 26px;
            display: grid;
            gap: 8px;
        }

        .seller-search-empty h3,
        .seller-search-section__title h3 {
            margin: 0;
        }

        .seller-search-empty p {
            margin: 0;
            color: #8fa7c4;
        }

        .seller-search-section {
            display: grid;
            gap: 14px;
        }

        .seller-search-section__title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .seller-search-section__title span {
            color: #8fa7c4;
            font-size: 13px;
        }

        .seller-search-tool-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }

        .seller-search-tool-card,
        .seller-search-result-card {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 20px;
            transition: transform 0.18s ease, border-color 0.18s ease, background 0.18s ease;
        }

        .seller-search-tool-card:hover,
        .seller-search-result-card:hover {
            transform: translateY(-1px);
            border-color: rgba(66, 165, 245, 0.24);
            background: rgba(255, 255, 255, 0.05);
        }

        .seller-search-tool-card {
            display: grid;
            gap: 8px;
        }

        .seller-search-tool-card strong,
        .seller-search-result-card strong {
            color: #f5f9ff;
        }

        .seller-search-tool-card p,
        .seller-search-result-card p {
            margin: 0;
            color: #8fa7c4;
            line-height: 1.65;
        }

        .seller-search-list {
            display: grid;
            gap: 12px;
        }

        .seller-search-result-card__main {
            display: grid;
            gap: 6px;
            min-width: 0;
        }

        .seller-search-result-card__main small {
            color: #6fbef5;
        }

        .seller-search-result-card__meta {
            flex-shrink: 0;
            color: #bbdefb;
            font-weight: 700;
            white-space: nowrap;
        }

        @media (max-width: 720px) {
            .seller-search-result-card {
                flex-direction: column;
            }

            .seller-search-result-card__meta {
                white-space: normal;
            }
        }
    </style>
@endsection
