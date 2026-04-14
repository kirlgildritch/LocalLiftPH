
  <!-- HEADER -->
  <header class="main-header">
    <div class="container">
      <div class="logo">
        <i class="fa-solid fa-bag-shopping"></i>
        <div class="logo-text">
          <h1>LocalLift</h1>
          <span>PH</span>
        </div>
      </div>

      <div class="search-box">
        <input type="text" placeholder="Search for prdd, hiogs, and more..." />
        <i class="fa-solid fa-magnifying-glass"></i>
      </div>

      <div class="header-right">
          <div class="notification-dropdown">
        <button class="notification-btn" id="notificationToggle" type="button">
            <i class="fa-regular fa-bell"></i>
            <span class="notif-badge">3</span>
        </button>

        <div class="notification-menu" id="notificationMenu">
            <div class="notification-header">
                <h4>Notifications</h4>
            </div>

            <a href="#" class="notification-item unread">
                <div class="notif-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                <div class="notif-content">
                    <p><strong>New Order</strong></p>
                    <span>You received a new order from Anna Santos.</span>
                    <small>2 mins ago</small>
                </div>
            </a>

            <a href="#" class="notification-item unread">
                <div class="notif-icon"><i class="fa-regular fa-envelope"></i></div>
                <div class="notif-content">
                    <p><strong>New Message</strong></p>
                    <span>Mark Reyes sent you a message.</span>
                    <small>10 mins ago</small>
                </div>
            </a>

            <a href="#" class="notification-item">
                <div class="notif-icon"><i class="fa-solid fa-box"></i></div>
                <div class="notif-content">
                    <p><strong>Order Shipped</strong></p>
                    <span>Your order #1021 has been marked as shipped.</span>
                    <small>1 hour ago</small>
                </div>
            </a>

            <div class="notification-footer">
                <a href="#">View All Notifications</a>
            </div>
        </div>
    </div>

      

        <div class="user-box">
          <div class="profile-dropdown">
              <div class="profile-btn" id="profileToggle">
                    @if(auth()->user()->profile_image)
                        <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile" class="header-profile-img">
                    @else
                        <i class="fa-regular fa-circle-user profile-icon"></i>
                    @endif

                    <span>Hi, {{ auth()->user()->name }}!</span>
                </div>
              <div class="profile-menu" id="profileMenu">
                  <a href="{{ route('seller.profile') }}">My Profile</a>
                 
                  <a href="{{ route('seller.settings') }}">
                      Settings
                  </a>

                  <form action="{{ route('logout') }}" method="POST">
                      @csrf
                      <button type="submit" class="logout">Logout</button>
                  </form>
              </div>
          </div>
      </div>


    </div>
  </header>

  <!-- NAV -->
  <nav class="seller-nav">
    <div class="container">
      <a href="{{ url('/seller-dashboard') }}" class="active"><i class="fa-solid fa-house"></i> Dashboard</a>
      <a href="#"><i class="fa-solid fa-location-dot"></i> AI Contacts</a>
      <a href="#"><i class="fa-regular fa-envelope"></i> Shop</a>
      <a href="#"><i class="fa-solid fa-gear"></i> Info</a>
      <a href="#"><i class="fa-regular fa-bag-shopping"></i> Orders</a>
      <a href="#">More <i class="fa-solid fa-chevron-down" style="font-size:12px;"></i></a>
    </div>
  </nav>

