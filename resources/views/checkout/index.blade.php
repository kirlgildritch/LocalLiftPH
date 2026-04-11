@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}">
    <section class="checkout-page">
    <div class="checkout-breadcrumb">
        <a href="#">Home</a>
        <span>&gt;</span>
        <span>Checkout</span>
    </div>

    <div class="checkout-wrapper">
        <h1 class="checkout-title">Checkout</h1>

        <div class="checkout-grid">
            <!-- LEFT SIDE -->
            <div class="checkout-left">
                <!-- Shipping Address -->
                <div class="checkout-card">
                    <div class="card-header">
                        <div class="step-title">
                            <span class="step-number">1</span>
                            <h3>Shipping Address</h3>
                        </div>
                        <a href="#" class="edit-link">Edit</a>
                    </div>

                    <div class="card-body">
                        <div class="customer-name">John Doe</div>

                        <div class="form-group">
                            <input type="email" value="johndoe@example.com" readonly>
                        </div>

                        <label class="form-label">Address: Line 1</label>
                        <div class="form-group">
                            <textarea rows="4" readonly>123 Main Street
Los Angeles, 90001
United States</textarea>
                        </div>
                    </div>
                </div>

                <!-- Shipping Method -->
                <div class="checkout-card">
                    <div class="card-header">
                        <div class="step-title">
                            <span class="step-number">2</span>
                            <h3>Shipping Method</h3>
                        </div>
                        <span class="arrow">&gt;</span>
                    </div>

                    <div class="card-body">
                        <div class="shipping-free">
                            <span class="step-number small">1</span>
                            <strong>Free Shipping</strong>
                        </div>

                        <label class="radio-option">
                            <input type="radio" name="shipping_method">
                            <span>Standard Shipping (₱50.00)</span>
                        </label>

                        <label class="radio-option">
                            <input type="radio" name="shipping_method">
                            <span>Express Shipping (₱150.00)</span>
                        </label>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="checkout-card">
                    <div class="card-header">
                        <div class="step-title">
                            <span class="step-number">3</span>
                            <h3>Payment Information</h3>
                        </div>
                        <span class="arrow">&gt;</span>
                    </div>

                    <div class="card-body payment-body">
                        <div class="payment-option active">
                            <div class="payment-option-title">
                                <span class="step-number small">4</span>
                                <strong>Credit / Debit Card</strong>
                            </div>

                            <div class="card-input-wrap">
                                <input type="text" value="4242-4242-4242-4242" readonly>
                                <div class="card-icons">
                                    <span class="dot mastercard-red"></span>
                                    <span class="dot mastercard-yellow"></span>
                                    <span class="dot visa-blue"></span>
                                    <span class="dot gcash-light"></span>
                                </div>
                            </div>

                            <div class="payment-fields">
                                <div class="form-group">
                                    <label>Card Holder</label>
                                    <input type="text" value="John Doe" readonly>
                                </div>

                                <div class="form-group small-field">
                                    <label>Exp</label>
                                    <select>
                                        <option>12 / 24</option>
                                    </select>
                                </div>

                                <div class="form-group small-field">
                                    <label>CVV</label>
                                    <input type="text" value="123" readonly>
                                </div>
                            </div>
                        </div>

                        <label class="paypal-option">
                            <input type="radio" name="payment_method">
                            <span>PayPal</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="checkout-right">
                <div class="summary-card">
                    <h3>Order Summary</h3>

                    <div class="summary-items">
                        <div class="summary-item">
                            <div class="summary-product">
                                <img src="https://via.placeholder.com/52x52.png?text=Item" alt="Beaded Bracelet">
                                <div>
                                    <h4>Beaded Bracelet</h4>
                                    <p>Likhang Kamay Crafts</p>
                                </div>
                            </div>
                            <div class="summary-price">
                                <strong>₱180.00</strong>
                                <span>x1</span>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="summary-product">
                                <img src="https://via.placeholder.com/52x52.png?text=Item" alt="Herbal Soap">
                                <div>
                                    <h4>Herbal Soap</h4>
                                    <p>Likhang Kamay Crafts</p>
                                </div>
                            </div>
                            <div class="summary-price">
                                <strong>₱95.00</strong>
                                <span>x2</span>
                            </div>
                        </div>
                    </div>

                    <div class="summary-line">
                        <span>Subtotal:</span>
                        <strong>₱ 370.00</strong>
                    </div>

                    <div class="summary-line">
                        <span>Shipping Fee:</span>
                        <strong>₱0.00</strong>
                    </div>

                    <div class="summary-total">
                        <span>Total:</span>
                        <strong>₱370.00</strong>
                    </div>

                    <button class="place-order-btn">PLACE ORDER</button>

                    <div class="coupon-box">
                        <input type="text" placeholder="Enter coupon code">
                        <button type="button">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    
</section>
@endsection