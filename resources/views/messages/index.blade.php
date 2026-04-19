@extends(auth()->user()->isSeller() ? 'layouts.seller' : 'layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/messages.css') }}">

    @if(auth()->user()->isSeller())
        <section class="dashboard-wrapper">
            <div class="container">
                <div class="dashboard-layout">
                    @include('seller.partials.sidebar')

                    <main class="dashboard-main">
                        <section class="seller-page-panel panel">
                            <div class="page-header">
                                <div>
                                    <span class="section-kicker">Messages</span>
                                    <h2>Seller inbox</h2>
                                </div>
                            </div>

                            @include('messages.partials.chat-layout')
                        </section>
                    </main>
                </div>
            </div>
        </section>
    @else
        <section class="orders-page buyer-messages-page">
            <div class="container">
                <div class="buyer-messages-heading">
                    <div class="message-breadcrumb">
                        <a href="{{ route('home') }}">Home</a>
                        <span>&gt;</span>
                        <span>Messages</span>
                    </div>

                    <div class="buyer-messages-intro">

                        <h2>Messages</h2>

                    </div>
                </div>

                <div class="panel buyer-messages-shell">
                    @include('messages.partials.chat-layout')
                </div>
            </div>
        </section>
    @endif
@endsection