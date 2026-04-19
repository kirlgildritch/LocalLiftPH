<aside class="sidebar">
    <button class="sidebar-close" type="button" data-close-seller-sidebar aria-label="Close seller navigation">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <div class="sidebar-menu">
        <a href="{{ url('/seller-dashboard') }}" class="{{ request()->is('seller-dashboard') ? 'active' : '' }}">
            <div class="left">
                <i class="fa-solid fa-house"></i> Dashboard
            </div>
        </a>

        <a href="{{ url('/manage-products') }}" class="{{ request()->is('manage-products') ? 'active' : '' }}">
            <div class="left">
                <i class="fa-solid fa-circle-check"></i> My Products
            </div>
        </a>

        <a href="{{ route('seller.orders') }}" class="{{ request()->is('seller-orders') ? 'active' : '' }}">
            <div class="left">
                <i class="fa-solid fa-bag-shopping"></i> Orders
            </div>
        </a>

        <a href="{{ route('seller.earnings') }}" class="{{ request()->is('seller-earnings') ? 'active' : '' }}">
            <div class="left">
                <i class="fa-solid fa-dollar-sign"></i> Earnings
            </div>
        </a>
        <a href="{{ route('seller.shop.preview') }}" class="{{ request()->is('seller-shop-preview') ? 'active' : '' }}">
            <div class="left">
                <i class="fa-solid fa-store"></i> View Shop
            </div>
        </a>


    </div>
</aside>