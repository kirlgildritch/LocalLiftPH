@extends(auth('seller')->check() ? 'layouts.seller' : 'layouts.app')

@section('content')
    @if(auth('seller')->check())
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
                                    <p class="floating-chat-page-note">Your floating chat widget is open for this conversation. Minimize it any time and keep browsing Seller Center.</p>
                                </div>
                            </div>
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
                        <p class="floating-chat-page-note">The floating chat panel is open for this conversation. You can minimize it and continue shopping without leaving the page.</p>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
