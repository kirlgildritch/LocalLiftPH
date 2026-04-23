<footer>
    <div class="container footer-top">
        <div class="footer-brand">
            <div class="footer-logo">
                <div class="logo-icon"><i class="fa-solid fa-location-dot"></i></div>
                <h3>LocalLift <span>PH</span></h3>
            </div>

            <p>
                A modern marketplace for discovering local products, regional sellers, and communities worth supporting.
            </p>

            <div class="footer-badges">
                <span>Trust-first shopping</span>
                <span>Seller growth tools</span>
                <span>Responsive storefronts</span>
            </div>
        </div>

        <div class="footer-col">
            <h4>Marketplace</h4>
            <ul>
                <li><a href="{{ route('home') }}">Overview</a></li>
                <li><a href="{{ route('shops.index') }}">Featured Shops</a></li>
                <li><a href="{{ route('products.index') }}">Products</a></li>
                <li><a href="{{ route('about') }}">About LocalLift</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>For Sellers</h4>
            <ul>
                <li><a href="{{ route('seller.register') }}">Create a Shop</a></li>
                <li><a href="{{ route('seller.center') }}">Seller Access</a></li>
                <li><a href="{{ route('products.index') }}">Product Discovery</a></li>

            </ul>
        </div>

        <div class="footer-col">
            <h4>Connect</h4>
            <ul>
                <li><i class="fa-brands fa-facebook-f"></i> Facebook</li>
                <li><i class="fa-brands fa-instagram"></i> Instagram</li>
                <li><i class="fa-brands fa-tiktok"></i> TikTok</li>
                <li><i class="fa-solid fa-envelope"></i> support@locallift.ph</li>
            </ul>
        </div>
    </div>

    <div class="container footer-bottom">
        <div>Copyright 2026 LocalLift PH. Built for local-first commerce.</div>
        <div class="right-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms and Conditions</a>
        </div>
    </div>
</footer>
