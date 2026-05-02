@extends('layouts.seller')

@section('content')
    @php
        $requestStatusLabel = match ($latestDocumentRequest?->status) {
            \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED => 'Resubmitted',
            \App\Models\SellerDocumentRequest::STATUS_RESOLVED => 'Resolved',
            default => 'Pending',
        };

        $requestReasonLabel = match ($latestDocumentRequest?->reason) {
            'proof_of_address' => 'Proof of Address',
            'tax_identification_number' => 'Tax Identification Number',
            'bank_statement' => 'Bank Statement',
            default => $latestDocumentRequest?->reason ? ucfirst(str_replace('_', ' ', $latestDocumentRequest->reason)) : null,
        };
    @endphp
    <section class="dashboard-wrapper">
        <div class="container">
            <div class="dashboard-layout">
                @include('seller.partials.sidebar')

                <main class="dashboard-main panel">
                    @if (session('success'))
                        <p class="seller-feedback success-message">{{ session('success') }}</p>
                    @endif

                    @if (session('error'))
                        <p class="seller-feedback error-message">{{ session('error') }}</p>
                    @endif

                    @if ($moderationNotifications->isNotEmpty())
                        <section class="dashboard-status-state panel">
                            <span class="section-kicker">Moderation Updates</span>
                            <div class="status-card-grid">
                                @foreach ($moderationNotifications as $notification)
                                    @php($payload = $notification->data)
                                    <article class="status-card panel">
                                        <strong>{{ $payload['title'] ?? 'Update' }}</strong>
                                        <p>{{ $payload['message'] ?? '' }}</p>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($dashboardState === 'not_started')
                        <section class="dashboard-empty-state panel">
                            <span class="section-kicker">Seller Dashboard</span>
                            <h1>Start your seller registration inside Seller Center.</h1>
                            <p>Your dashboard analytics and shop tools will appear here after you submit your application and
                                get approved by admin.</p>

                            <div class="dashboard-empty-actions">
                                <a href="{{ route('seller.dashboard', ['register' => 1]) }}" class="page-action-btn">Start
                                    Registration</a>
                            </div>
                        </section>
                    @elseif ($dashboardState === 'filling_form')
                        <section class="content-panel panel seller-application-panel">
                            <div class="panel-heading">
                                <div>
                                    <span class="section-kicker">Seller Registration</span>
                                    <h2>Complete your seller application</h2>
                                </div>
                            </div>

                            @if ($latestDocumentRequest && $latestDocumentRequest->status === \App\Models\SellerDocumentRequest::STATUS_PENDING)
                                <div class="status-card-grid">
                                    <article class="status-card panel">
                                        <strong>{{ $requestReasonLabel }}</strong>
                                        <p>{{ $latestDocumentRequest->admin_notes ?: 'Additional verification document requested.' }}</p>
                                    </article>
                                    <article class="status-card panel">
                                        <strong>Requested</strong>
                                        <p>{{ optional($latestDocumentRequest->requested_at)->format('M d, Y h:i A') ?: 'N/A' }}</p>
                                    </article>
                                </div>
                            @endif

                            <form action="{{ route('seller.dashboard.application.store') }}" method="POST"
                                enctype="multipart/form-data" class="seller-application-form">
                                @csrf

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="seller_type">Seller Type</label>
                                        <select name="seller_type" id="seller_type" required>
                                            <option value="">Select seller type</option>
                                            <option value="individual" {{ old('seller_type', $seller?->seller_type) === 'individual' ? 'selected' : '' }}>Small Seller /
                                                Individual Seller</option>
                                            <option value="registered_business" {{ old('seller_type', $seller?->seller_type) === 'registered_business' ? 'selected' : '' }}>Registered
                                                Business / Enterprise</option>
                                        </select>
                                        @error('seller_type')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="full_name">Full Name</label>
                                        <input type="text" id="full_name" name="full_name"
                                            value="{{ old('full_name', $seller?->full_name ?? auth('seller')->user()?->name) }}"
                                            required>
                                        @error('full_name')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="age">Age</label>
                                        <input type="number" id="age" name="age" min="18"
                                            value="{{ old('age', $seller?->age) }}" required>
                                        @error('age')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_number">Phone Number</label>
                                        <input type="text" id="phone_number" name="phone_number"
                                            value="{{ old('phone_number', $seller?->contact_number ?? auth('seller')->user()?->phone) }}"
                                            required>
                                        @error('phone_number')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email"
                                            value="{{ old('email', $seller?->email ?? auth('seller')->user()?->email) }}"
                                            required>
                                        @error('email')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group form-group-wide">
                                        <label for="address">Address</label>
                                        <textarea id="address" name="address" rows="3"
                                            required>{{ old('address', $seller?->address ?? auth('seller')->user()?->address) }}</textarea>
                                        @error('address')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="valid_id_type">Valid ID Type</label>
                                        <select name="valid_id_type" id="valid_id_type" required>
                                            <option value="">Select valid ID</option>
                                            @foreach (['Passport', 'National ID', 'Driver\'s License', 'UMID', 'PhilHealth ID', 'Postal ID'] as $idType)
                                                <option value="{{ $idType }}" {{ old('valid_id_type', $seller?->valid_id_type) === $idType ? 'selected' : '' }}>{{ $idType }}</option>
                                            @endforeach
                                        </select>
                                        @error('valid_id_type')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="valid_id_number">Valid ID Number</label>
                                        <input type="text" id="valid_id_number" name="valid_id_number"
                                            value="{{ old('valid_id_number', $seller?->valid_id_number) }}" required>
                                        @error('valid_id_number')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="valid_id_document">Upload Valid ID / Passport</label>
                                        <input type="file" id="valid_id_document" name="valid_id_document"
                                            accept=".jpg,.jpeg,.png,.pdf,.webp">
                                        @if ($seller?->valid_id_path)
                                            <small class="muted-label">Existing file uploaded. Upload a new one only if you want to
                                                replace it.</small>
                                        @endif
                                        @error('valid_id_document')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="business_permit">Business Permit</label>
                                        <input type="file" id="business_permit" name="business_permit"
                                            accept=".jpg,.jpeg,.png,.pdf,.webp">
                                        <small class="muted-label">Optional for individual sellers. Required for registered
                                            businesses.</small>
                                        @if ($seller?->business_permit_path)
                                            <small class="muted-label">Existing permit uploaded. Upload a new one only if you want
                                                to replace it.</small>
                                        @endif
                                        @error('business_permit')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    @if ($latestDocumentRequest && $latestDocumentRequest->status === \App\Models\SellerDocumentRequest::STATUS_PENDING)
                                        <div class="form-group form-group-wide">
                                            <label for="requested_document">Requested Document</label>
                                            <input type="file" id="requested_document" name="requested_document"
                                                accept=".jpg,.jpeg,.png,.pdf,.webp">
                                            <small class="muted-label">{{ $requestReasonLabel }}</small>
                                            @error('requested_document')<small class="error-text">{{ $message }}</small>@enderror
                                        </div>
                                    @endif
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="page-action-btn">
                                        {{ $latestDocumentRequest && $latestDocumentRequest->status === \App\Models\SellerDocumentRequest::STATUS_PENDING ? 'Upload and Resubmit' : 'Submit Application' }}
                                    </button>
                                </div>
                            </form>
                        </section>
                    @elseif ($dashboardState === 'documents_requested')
                        <section class="dashboard-status-state panel">
                            <span class="section-kicker">Documents Required</span>
                            <h1>Additional document requested</h1>

                            <div class="status-card-grid">
                                <article class="status-card panel">
                                    <strong>{{ $requestReasonLabel }}</strong>
                                    <p>{{ $latestDocumentRequest?->admin_notes ?: 'Upload the requested document to continue review.' }}</p>
                                </article>
                                <article class="status-card panel">
                                    <strong>Requested</strong>
                                    <p>{{ optional($latestDocumentRequest?->requested_at)->format('M d, Y h:i A') ?: 'N/A' }}</p>
                                </article>
                                <article class="status-card panel">
                                    <strong>Status</strong>
                                    <p>{{ $requestStatusLabel }}</p>
                                </article>
                            </div>

                            <div class="dashboard-empty-actions">
                                <a href="{{ route('seller.dashboard', ['resubmit' => 1]) }}" class="page-action-btn">Upload
                                    Documents</a>
                            </div>
                        </section>
                    @elseif ($dashboardState === 'suspended')
                        <section class="dashboard-status-state panel">
                            <span class="section-kicker">Account Status</span>
                            <h1>Seller account suspended</h1>
                            <div class="status-card-grid">
                                <article class="status-card panel">
                                    <strong>Status</strong>
                                    <p>Suspended</p>
                                </article>
                                <article class="status-card panel">
                                    <strong>Reason</strong>
                                    <p>{{ $seller?->suspension_reason ?: 'Account under moderation review.' }}</p>
                                </article>
                            </div>
                        </section>
                    @elseif ($dashboardState === 'pending')
                        <section class="dashboard-status-state panel">
                            <span class="section-kicker">Pending Review</span>
                            <h1>{{ $latestDocumentRequest?->status === \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED ? 'Resubmitted for Review' : 'Application Submitted' }}</h1>
                            <div class="status-card-grid">
                                <article class="status-card panel">
                                    <strong>{{ $latestDocumentRequest?->status === \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED ? 'Latest Request' : 'Application Submitted' }}</strong>
                                    <p>
                                        @if ($latestDocumentRequest?->status === \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED)
                                            {{ $requestReasonLabel }}. {{ $latestDocumentRequest->admin_notes ?: 'Document resubmitted for review.' }}
                                        @else
                                            Your seller details and uploaded documents are now in the admin review queue.
                                        @endif
                                    </p>
                                </article>
                                <article class="status-card panel">
                                    <strong>{{ $latestDocumentRequest?->status === \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED ? 'Requested' : 'Pending Admin Review' }}</strong>
                                    <p>
                                        @if ($latestDocumentRequest?->status === \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED)
                                            {{ optional($latestDocumentRequest->requested_at)->format('M d, Y h:i A') ?: 'N/A' }}
                                        @else
                                            Your dashboard analytics and shop controls will unlock after approval.
                                        @endif
                                    </p>
                                </article>
                                <article class="status-card panel">
                                    <strong>Status</strong>
                                    <p>{{ $latestDocumentRequest?->status === \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED ? $requestStatusLabel : 'Under Review' }}</p>
                                </article>
                            </div>
                        </section>
                    @elseif ($dashboardState === 'rejected')
                        <section class="dashboard-status-state panel">
                            <span class="section-kicker">Application Update</span>
                            <h1>Seller application rejected</h1>
                            <p>{{ $seller?->review_notes ?: 'Your application needs changes before it can be approved.' }}</p>

                            <div class="status-card-grid">
                                <article class="status-card panel">
                                    <strong>Current Status</strong>
                                    <p>Rejected. Review the feedback and resubmit your registration from this dashboard.</p>
                                </article>
                                <article class="status-card panel">
                                    <strong>Next Step</strong>
                                    <p>Update your information or documents, then submit again for admin review.</p>
                                </article>
                            </div>

                            <div class="dashboard-empty-actions">
                                <a href="{{ route('seller.dashboard', ['resubmit' => 1]) }}" class="page-action-btn">Update and
                                    Resubmit</a>
                            </div>
                        </section>
                    @else

                        <div class="stats-grid">
                            <article class="stat-card panel">
                                <div class="stat-top">
                                    <i class="fa-solid fa-coins"></i>
                                    <span>Total Sales</span>
                                </div>
                                <strong>&#8369; {{ number_format($stats['total_sales'], 2) }}</strong>
                                <p>Current revenue snapshot across completed order items.</p>
                            </article>

                            <article class="stat-card panel">
                                <div class="stat-top">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                    <span>Orders Received</span>
                                </div>
                                <strong>{{ $stats['orders_received'] }}</strong>
                                <p>Orders connected to your approved live product listings.</p>
                            </article>

                            <article class="stat-card panel">
                                <div class="stat-top">
                                    <i class="fa-solid fa-cube"></i>
                                    <span>Products Listed</span>
                                </div>
                                <strong>{{ $stats['products_listed'] }}</strong>
                                <p>Approved products currently visible to buyers.</p>
                            </article>

                            <article class="stat-card panel">
                                <div class="stat-top">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>Pending Orders</span>
                                </div>
                                <strong class="highlight">{{ $stats['pending_orders'] }}</strong>
                                <p>Orders that still need confirmation or processing.</p>
                            </article>
                        </div>

                        <div class="dashboard-grid">
                            <section class="content-panel panel orders-panel">
                                <div class="panel-heading">
                                    <div>
                                        <span class="section-kicker">Orders</span>
                                        <h2>Recent Orders</h2>
                                    </div>
                                    <a href="{{ route('seller.orders') }}" class="inline-link">View All</a>
                                </div>

                                <div class="order-list">
                                    @forelse ($recentOrders as $item)
                                        <article class="order-item">
                                            <div>
                                                <strong>#{{ $item->order?->id ?? $item->id }}</strong>
                                                <span>{{ $item->order?->customer_name ?? 'Buyer Order' }}</span>
                                            </div>
                                            <div class="order-meta">
                                                <span
                                                    class="status-chip {{ in_array($item->order?->status, ['completed', 'delivered']) ? 'completed' : (in_array($item->order?->status, ['processing']) ? 'processing' : (in_array($item->order?->status, ['shipped']) ? 'shipped' : 'pending')) }}">
                                                    {{ ucfirst($item->order?->status ?? 'pending') }}
                                                </span>
                                                <strong>&#8369; {{ number_format(($item->price ?? 0) * ($item->quantity ?? 1), 2) }}</strong>
                                            </div>
                                        </article>
                                    @empty
                                        <p class="empty-text">No orders yet for your live products.</p>
                                    @endforelse
                                </div>
                            </section>

                            <section class="content-panel panel conversations-panel">
                                <div class="panel-heading">
                                    <div>
                                        <span class="section-kicker">Inbox</span>
                                        <h2>Buyer Conversations</h2>
                                    </div>
                                </div>

                                <div class="dashboard-mini-metrics">
                                    <article class="hero-stat-card">
                                        <strong>{{ $stats['open_conversations'] }}</strong>
                                        <span>Inbox</span>
                                    </article>
                                    <article class="hero-stat-card">
                                        <strong>{{ $stats['active_products'] }}</strong>
                                        <span>Live listings</span>
                                    </article>
                                </div>
                            </section>
                        </div>

                        <section class="content-panel panel">
                            <div class="panel-heading">
                                <div>
                                    <span class="section-kicker">Products</span>
                                    <h2>Your Products</h2>
                                </div>
                                <a href="{{ route('seller.products.index') }}" class="inline-link">Manage Products</a>
                            </div>

                            <div class="product-grid">
                                @forelse ($recentProducts as $product)
                                    <article class="product-card panel">
                                        <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?q=80&w=900&auto=format&fit=crop' }}"
                                            alt="{{ $product->name }}">
                                        <div class="product-copy">
                                            <h3>{{ $product->name }}</h3>
                                            <p>{{ ucfirst($product->status) }} {{ $product->is_active ? 'listing' : 'draft' }}</p>
                                            <div class="product-card-bottom">
                                                <strong>&#8369; {{ number_format($product->price, 2) }}</strong>
                                                <a href="{{ route('seller.products.index') }}" class="mini-action">View</a>
                                            </div>
                                        </div>
                                    </article>
                                @empty
                                    <p class="empty-text">You do not have products yet.</p>
                                @endforelse
                            </div>
                        </section>
                    @endif
                </main>
            </div>
        </div>
    </section>
@endsection
