
@extends('layouts.seller')


@section('content')
      <section class="dashboard-wrapper">
    <div class="container">
      <div class="dashboard-layout">

        <!-- SIDEBAR -->
       @include('seller.partials.sidebar')
        <!-- MAIN -->
        <main class="dashboard-main">
          <h2>Welcome to Your Seller Dashboard!</h2>
          <div class="divider"></div>

          <div class="stats-grid">
            <div class="stat-card">
              <div class="top"><i class="fa-solid fa-bag-shopping"></i> Total Sales</div>
              <div class="value">₱ 25,300</div>
            </div>

            <div class="stat-card">
              <div class="top"><i class="fa-solid fa-cart-shopping"></i> Orders Received</div>
              <div class="value">120</div>
            </div>

            <div class="stat-card">
              <div class="top"><i class="fa-solid fa-cube"></i> Products Listed</div>
              <div class="value">15</div>
            </div>

            <div class="stat-card">
              <div class="top"><i class="fa-regular fa-clock"></i> Pending Orders</div>
              <div class="value highlight">3</div>
            </div>
          </div>

          <div class="middle-grid">
            <!-- Sales Overview -->
            <div class="panel">
              <div class="panel-header">Sales Overview</div>
              <div class="panel-body">
                <div class="chart-legend">
                  <div class="legend-item"><span class="legend-line pink-line"></span> This Month</div>
                  <div class="legend-item"><span class="legend-line gray-line"></span> Last Month</div>
                </div>

                <div class="chart-box">
                  <div class="y-labels">
                    <span>300</span>
                    <span>200</span>
                    <span>100</span>
                    <span>0</span>
                  </div>

                  <svg viewBox="0 0 500 250" preserveAspectRatio="none">
                    <!-- gray line -->
                    <polyline
                      points="20,210 95,150 170,160 245,90 320,120 395,70"
                      fill="none"
                      stroke="#bbb1bc"
                      stroke-width="4"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <circle cx="20" cy="210" r="7" fill="#bbb1bc"/>
                    <circle cx="95" cy="150" r="7" fill="#bbb1bc"/>
                    <circle cx="170" cy="160" r="7" fill="#bbb1bc"/>
                    <circle cx="245" cy="90" r="7" fill="#bbb1bc"/>
                    <circle cx="320" cy="120" r="7" fill="#bbb1bc"/>
                    <circle cx="395" cy="70" r="7" fill="#bbb1bc"/>

                    <!-- pink line -->
                    <polyline
                      points="20,205 95,120 170,125 245,60 320,75 395,30"
                      fill="none"
                      stroke="#ff4f93"
                      stroke-width="5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <circle cx="20" cy="205" r="8" fill="#ff4f93"/>
                    <circle cx="95" cy="120" r="8" fill="#ff4f93"/>
                    <circle cx="170" cy="125" r="8" fill="#ff4f93"/>
                    <circle cx="245" cy="60" r="8" fill="#ff4f93"/>
                    <circle cx="320" cy="75" r="8" fill="#ff4f93"/>
                    <circle cx="395" cy="30" r="8" fill="#ff4f93"/>
                  </svg>
                </div>

                <div class="months">
                  <span>Jan</span>
                  <span>Feb</span>
                  <span>Mar</span>
                  <span>Apr</span>
                  <span>May</span>
                  <span>Jun</span>
                </div>
              </div>
            </div>

            <!-- Recent Orders -->
            <div class="panel">
              <div class="panel-header">Recent Orders</div>
              <div class="panel-body">
                <table class="orders-table">
                  <thead>
                    <tr>
                      <th>Order ID</th>
                      <th>Customer</th>
                      <th>Status</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>#1023</td>
                      <td>Anna Santos</td>
                      <td class="status-completed">Completed</td>
                      <td>₱850.00</td>
                    </tr>
                    <tr>
                      <td>#1022</td>
                      <td>Mark Reyes</td>
                      <td class="status-processing">Processing</td>
                      <td>₱1,200.00</td>
                    </tr>
                    <tr>
                      <td>#1021</td>
                      <td>Liza Delacruz</td>
                      <td class="status-shipped">Shipped</td>
                      <td>₱540.00</td>
                    </tr>
                    <tr>
                      <td>#1020</td>
                      <td>John Villanueva</td>
                      <td class="status-pending">Pending</td>
                      <td>₱320.00</td>
                    </tr>
                  </tbody>
                </table>

                <div class="view-orders">
                  <a href="#">View All Orders <i class="fa-solid fa-angle-right"></i></a>
                </div>
              </div>
            </div>
          </div>

          <!-- PRODUCTS -->
          <div class="product-section-title">Your Products</div>
          <div class="product-grid">
            <div class="product-card">
              <img src="https://images.unsplash.com/photo-1617038220319-276d3cfab638?q=80&w=800&auto=format&fit=crop" alt="Beaded Bracelet">
              <h4>Beaded Bracelet</h4>
              <div class="sub">10 Sales</div>
              <div class="product-card-bottom">
                <div class="product-price">₱ 180.00</div>
                <button class="edit-btn">Edit / View</button>
              </div>
            </div>

            <div class="product-card">
              <img src="https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?q=80&w=800&auto=format&fit=crop" alt="Herbal Soap Set">
              <h4>Herbal Soap Set</h4>
              <div class="sub">25 in Stock</div>
              <div class="product-card-bottom">
                <div class="product-price">₱ 250.00</div>
                <button class="edit-btn">Edit / View</button>
              </div>
            </div>

            <div class="product-card">
              <img src="https://images.unsplash.com/photo-1587300003388-59208cc962cb?q=80&w=800&auto=format&fit=crop" alt="Eco-Friendly Straws">
              <h4>Eco-Friendly Straws</h4>
              <div class="sub">5 Sales</div>
              <div class="product-card-bottom">
                <div class="product-price">₱ 150.00</div>
                <button class="edit-btn">Edit / View</button>
              </div>
            </div>

            <div class="product-card">
              <img src="https://images.unsplash.com/photo-1594223274512-ad4803739b7c?q=80&w=800&auto=format&fit=crop" alt="Woven Tote Bag">
              <h4>Woven Tote Bag</h4>
              <div class="sub">8 Sales</div>
              <div class="product-card-bottom">
                <div class="product-price">₱ 350.00</div>
                <button class="edit-btn">Edit / View</button>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </section>
@endsection