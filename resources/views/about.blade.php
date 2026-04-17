@extends('layouts.app')
@section('title', 'LocalLift PH - About')
@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/about.css') }}">

    <section class="about-page">
        <div class="container">
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <span>About</span>
            </div>

            <div class="about-story-grid">
                <article class="about-card panel">
                    <span class="about-label">Mission</span>
                    <h2>Our Mission</h2>
                    <p>
                        We support local entrepreneurs by building an accessible online marketplace where sellers and buyers
                        can connect easily, discover value faster, and create long-term trust.
                    </p>
                </article>

                <article class="about-card panel">
                    <span class="about-label">Why It Matters</span>
                    <h2>Why LocalLift PH exists</h2>
                    <p>
                        Many small businesses depend on limited social media reach. LocalLift PH gives them a centralized
                        platform where products, shops, and identity are easier to discover and evaluate.
                    </p>
                </article>
            </div>

            <div class="about-grid">
                <article class="about-box panel">
                    <i class="fa-solid fa-store"></i>
                    <h3>For Sellers</h3>
                    <p>
                        Register, build a storefront, publish products, and manage your marketplace presence from one
                        focused platform.
                    </p>
                </article>

                <article class="about-box panel">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <h3>For Buyers</h3>
                    <p>
                        Browse shops, compare products, and support local sellers through a cleaner and more trustworthy
                        interface.
                    </p>
                </article>

                <article class="about-box panel">
                    <i class="fa-solid fa-people-group"></i>
                    <h3>For the Community</h3>
                    <p>
                        Better local discovery helps strengthen regional brands, create repeat buyers, and encourage
                        community support.
                    </p>
                </article>
            </div>

            <div class="about-offers panel">
                <div class="about-offers-copy">
                    <span class="about-label">Platform Capabilities</span>
                    <h2>What the platform offers</h2>
                    <p>
                        The system is designed to give both sellers and buyers the core marketplace tools they need without
                        making the experience feel cluttered or outdated.
                    </p>
                </div>

                <ul class="about-list">
                    <li>Seller registration and shop creation</li>
                    <li>Product posting and management</li>
                    <li>Buyer account registration</li>
                    <li>Shop and product browsing</li>
                    <li>Secure login and account management</li>
                    <li>Responsive layouts for every screen size</li>
                </ul>
            </div>
        </div>
    </section>
@endsection