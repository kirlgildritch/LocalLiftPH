@extends('layouts.app')

 
    
@section('content')

  <link rel="stylesheet" href="{{ asset('assets/css/productsStyle.css') }}">

   
  <section class="products-page">



    <div class="container">
      <div class="checkout-breadcrumb">
        <a href="#">Home</a>
        <span>&gt;</span>
        <span>Products</span>
    </div>

      <div class="products-wrapper">
        <aside class="sidebar">
          <div class="filter-card">
            <h3>CATEGORY</h3>

            <div class="category-list">
              <div class="category-item active">
                <div class="category-left">
                  <span class="radio-dot active-dot"></span>
                  <span>All</span>
                </div>
                <div class="category-right">
                  <span class="count">128</span>
                  <i class="fa-solid fa-chevron-down"></i>
                </div>
              </div>

              <div class="category-item">
                <div class="category-left">
                  <span class="radio-dot"></span>
                  <span>Food & Drinks</span>
                </div>
                <span class="count">42</span>
              </div>

              <div class="category-item">
                <div class="category-left">
                  <span class="radio-dot"></span>
                  <span>Clothing & Fashion</span>
                </div>
                <span class="count">33</span>
              </div>

              <div class="category-item">
                <div class="category-left">
                  <span class="radio-dot"></span>
                  <span>Handmade Crafts</span>
                </div>
                <span class="count">13</span>
              </div>

              <div class="category-item">
                <div class="category-left">
                  <span class="radio-dot"></span>
                  <span>Accessories</span>
                </div>
                <span class="count">24</span>
              </div>

              <div class="category-item">
                <div class="category-left">
                  <span class="radio-dot"></span>
                  <span>Souvenirs & Gifts</span>
                </div>
                <span class="count">12</span>
              </div>
            </div>
          </div>

          <div class="filter-card">
            <h3>FILTER BY PRICE</h3>

            <div class="price-labels">
              <span>PHP 50</span>
              <span>PHP 500</span>
            </div>

            <div class="fake-range">
              <div class="range-line"></div>
              <div class="range-fill"></div>
              <span class="range-thumb thumb-left"></span>
              <span class="range-thumb thumb-right"></span>
            </div>

            <button class="filter-btn">FILTER</button>
          </div>

          <div class="filter-simple">
            <h3>SORT BY</h3>
            <select>
              <option>Newest</option>
              <option>Price Low-High</option>
              <option>Price High-Low</option>
            </select>
          </div>
        </aside>

        <main class="products-content">
          <div class="products-top">
            <h1>Products</h1>

            <div class="products-toolbar">
              <div class="sort-inline">
                <label>Sort By:</label>
                <select>
                  <option>Newest</option>
                  <option>Price Low-High</option>
                  <option>Price High-Low</option>
                </select>
              </div>

              <div class="right-tools">
                <select class="second-sort">
                  <option>Newest</option>
                  <option>Popular</option>
                </select>

                <div class="view-icons">
                  <i class="fa-solid fa-table-cells-large"></i>
                  <i class="fa-solid fa-bars"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="product-grid">
            <div class="product-card">
              <div class="product-image"><img src="assets/images/product1.png" alt="Banana Chips"></div>
              <div class="product-info">
                <h4>Banana Chips</h4>
                <p>Brew & Reans Cafe</p>
                <div class="price">P 120.00</div>
                <div class="product-actions">
                  <a href="{{ route('products.show') }}" class="btn-view">VIEW</a>
                  <a href="cart.php" class="btn-cart">ADDT CART</a>
                </div>
              </div>
            </div>

            <div class="product-card">
              <div class="product-image"><img src="assets/images/product2.png" alt="Handwoven Bag"></div>
              <div class="product-info">
                <h4>Handwoven Bag</h4>
                <p>Throads & Style PH</p>
                <div class="price">P 950.00</div>
                <div class="product-actions">
                  <a href="product_details.php" class="btn-view">VIEW</a>
                  <a href="cart.php" class="btn-cart">ADDT CART</a>
                </div>
              </div>
            </div>

            <div class="product-card">
              <div class="product-image"><img src="assets/images/product3.png" alt="Herbal Soap"></div>
              <div class="product-info">
                <h4>Herbal Soap</h4>
                <p>Lithong Ramay Crafts</p>
                <div class="price">P 95.00</div>
                <div class="product-actions">
                  <a href="product_details.php" class="btn-view">VIEW</a>
                  <a href="cart.php" class="btn-cart">ADDT CART</a>
                </div>
              </div>
            </div>

            <div class="product-card">
              <div class="product-image"><img src="assets/images/product4.png" alt="Tote Bag"></div>
              <div class="product-info">
                <h4>Tote Bag</h4>
                <p>Throads & Style PH</p>
                <div class="price">P 190.00</div>
                <div class="product-actions">
                  <a href="product_details.php" class="btn-view">VIEW</a>
                  <a href="cart.php" class="btn-cart">ADDT CART</a>
                </div>
              </div>
            </div>

            <div class="product-card">
              <div class="product-image"><img src="assets/images/product5.png" alt="Wooden Cooking Set"></div>
              <div class="product-info">
                <h4>Wooden Cooking Set</h4>
                <p>Brew & Beans Cafe</p>
                <div class="price">P 250.00</div>
                <div class="product-actions">
                  <a href="product_details.php" class="btn-view">VIEW</a>
                  <a href="cart.php" class="btn-cart">ADDT CART</a>
                </div>
              </div>
            </div>

            <div class="product-card">
              <div class="product-image"><img src="assets/images/product6.png" alt="Handmade Necklace"></div>
              <div class="product-info">
                <h4>Handmade Necklace</h4>
                <p>Lithong Ramay Crafts</p>
                <div class="price">P 220.00</div>
                <div class="product-actions">
                  <a href="product_details.php" class="btn-view">VIEW</a>
                  <a href="cart.php" class="btn-cart">ADDT CART</a>
                </div>
              </div>
            </div>

            <div class="product-card">
              <div class="product-image"><img src="assets/images/product7.png" alt="Tote Bag"></div>
              <div class="product-info">
                <h4>Tote Bag</h4>
                <p>Throads & Style PH</p>
                <div class="price">P 190.00</div>
                <div class="product-actions">
                  <a href="product_details.php" class="btn-view">VIEW</a>
                  <a href="cart.php" class="btn-cart">ADDT CART</a>
                </div>
              </div>
            </div>

            <div class="product-card">
              <div class="product-image"><img src="assets/images/product8.png" alt="Organic Honey"></div>
              <div class="product-info">
                <h4>Organic Honey</h4>
                <p>Brew & Beans Cafe</p>
                <div class="price">P 460.00</div>
                <div class="product-actions">
                  <a href="product_details.php" class="btn-view">VIEW</a>
                  <a href="cart.php" class="btn-cart">ADDT CART</a>
                </div>
              </div>
            </div>

            <div class="product-card">
              <div class="product-image"><img src="assets/images/product9.png" alt="Eco-Friendly Straw Set"></div>
              <div class="product-info">
                <h4>Eco-Friendly Straw Set</h4>
                <p>Lithong Ramay Crafts</p>
                <div class="price">P 150.00</div>
                <div class="product-actions">
                  <a href="product_details.php" class="btn-view">VIEW</a>
                  <a href="cart.php" class="btn-cart">ADDT CART</a>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </section>

    
@endsection