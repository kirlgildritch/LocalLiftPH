# Security and Database Evidence

This document is a presentation-ready guide for the project's security and database implementation.

## 1. Authentication and Guards

Primary evidence:
- `config/auth.php`
- `routes/buyer.php`
- `routes/seller.php`
- `routes/admin.php`

What to point out:
- The project uses separate session guards for `web`, `seller`, and `admin`.
- All three guards use the same `users` provider, but route access is separated by middleware and authorization.
- Password reset configuration uses Laravel's built-in broker and token table.

Key file:
- `config/auth.php`

Highlights:
- `defaults.guard` is `web`
- `guards.web` protects buyer flows
- `guards.seller` protects seller-center and seller dashboard flows
- `guards.admin` protects admin moderation and management flows

## 2. Route-Level Access Control

Primary evidence:
- `routes/frontend.php`
- `routes/buyer.php`
- `routes/seller.php`
- `routes/admin.php`

What to point out:
- Public pages are grouped under the `frontend` middleware.
- Buyer-only features use the `buyer` middleware.
- Seller-only features use the `seller` middleware.
- Admin-only features use the `admin` middleware.

Examples:
- `routes/buyer.php` protects cart, checkout, wishlist, orders, reports, and messages.
- `routes/seller.php` protects seller product management, seller orders, settings, notifications, and seller messages.
- `routes/admin.php` protects moderation for products, sellers, reports, payouts, and notifications.

## 3. CSRF Protection

Primary evidence:
- Blade forms across `resources/views/**`

What to point out:
- Forms use Laravel's `@csrf` directive.
- This protects state-changing POST, PATCH, PUT, and DELETE requests from cross-site request forgery.

Representative examples:
- `resources/views/checkout/index.blade.php`
- `resources/views/buyer/order-show.blade.php`
- `resources/views/buyer/wishlist.blade.php`
- `resources/views/admin/product-approvals/modals/product-approval.blade.php`
- `resources/views/admin/reports.blade.php`
- `resources/views/seller/products/edit.blade.php`

## 4. Validation via Form Requests

Primary evidence:
- `app/Http/Requests`

What to point out:
- Validation is no longer buried in controllers for the main flows.
- Laravel Form Requests now centralize validation rules and improve maintainability.
- Some requests also preserve special behavior like anchor redirects or custom error bags.

Key request classes:
- `app/Http/Requests/Checkout/CheckoutStoreRequest.php`
- `app/Http/Requests/Message/StoreMessageRequest.php`
- `app/Http/Requests/Report/StoreReportRequest.php`
- `app/Http/Requests/ProductReview/StoreProductReviewRequest.php`
- `app/Http/Requests/Seller/StoreProductRequest.php`
- `app/Http/Requests/Seller/UpdateProductRequest.php`
- `app/Http/Requests/Seller/UpdateSellerSettingsRequest.php`
- `app/Http/Requests/Seller/UpdateSellerPayoutRequest.php`
- `app/Http/Requests/Seller/UpdateSellerInventoryRequest.php`
- `app/Http/Requests/Seller/UpdateSellerStatusRequest.php`

What this proves:
- Required fields are enforced
- File uploads are size/type validated
- Enumerated values are restricted
- Invalid requests are rejected before controller business logic runs

## 5. Authorization via Policies

Primary evidence:
- `app/Policies`
- `app/Providers/AppServiceProvider.php`

What to point out:
- The project uses Laravel policies for consistent authorization.
- Authorization checks were standardized for orders, products, conversations, seller moderation, reports, and admin notifications.

Registered policies:
- `OrderPolicy`
- `ConversationPolicy`
- `ProductPolicy`
- `ReviewPolicy`
- `SellerPolicy`
- `ReportPolicy`
- `DatabaseNotificationPolicy`

Key registration file:
- `app/Providers/AppServiceProvider.php`

Example talking point:
- "Middleware protects the route, and policies protect the resource."

## 6. Core Database Relationships

Primary evidence:
- `app/Models`
- `database/migrations`

Important relationship examples:
- `User` has many `Product`
- `User` has one `Seller`
- `User` has many `Address`
- `Product` belongs to `User` and `Category`
- `Product` has many `Review`, `OrderItem`, `ProductMedia`, `ProductVariant`, and `Report`
- `Order` has many `OrderItem`
- `Conversation` belongs to both buyer and seller users
- `Message` belongs to a `Conversation` and a sender `User`
- `Report` belongs to a reporting user, an optional product, and an optional seller

Key model files:
- `app/Models/User.php`
- `app/Models/Product.php`
- `app/Models/Order.php`
- `app/Models/Conversation.php`
- `app/Models/Report.php`
- `app/Models/Seller.php`

## 7. Foreign Keys and Referential Integrity

Primary evidence:
- `database/migrations/2026_04_12_074443_create_products_table.php`
- `database/migrations/2026_04_15_145701_create_order_items_table.php`
- `database/migrations/2026_04_24_110000_create_reports_table.php`
- `database/migrations/2026_05_12_000004_create_product_variants_table.php`

What to point out:
- The schema uses `foreignId()->constrained()` and explicit delete behavior.
- This enforces valid relationships at the database level, not just in PHP.

Examples:
- `products.user_id` -> `users.id` with cascade delete
- `order_items.order_id` -> `orders.id` with cascade delete
- `order_items.product_id` -> `products.id`
- `reports.user_id` -> `users.id`
- `reports.product_id` -> `products.id` with `nullOnDelete()`
- `reports.seller_id` -> `users.id` with `nullOnDelete()`
- `product_variants.product_id` -> `products.id` with cascade delete

Why this matters:
- Prevents orphaned records
- Improves data integrity
- Makes relationships safer and more consistent

## 8. Indexes and Performance Evidence

Primary evidence:
- `database/migrations/2026_05_14_120000_add_performance_indexes_for_marketplace_queries.php`

What to point out:
- The project includes explicit indexes for marketplace browsing and messaging performance.
- This is strong evidence that the database design considered scale and query efficiency.

Indexes added:
- `products_visibility_sort_index`
- `products_seller_visibility_index`
- `sellers_marketplace_visibility_index`
- `conversations_buyer_updated_at_index`
- `conversations_seller_updated_at_index`
- `messages_conversation_created_at_index`
- `messages_unread_lookup_index`
- `notifications_notifiable_read_at_created_at_index`

Why this matters:
- Faster product browse queries
- Faster seller product lists
- Faster message thread lookups
- Faster unread notification counts

## 9. Security-Sensitive Business Rules

Primary evidence:
- `app/Http/Controllers/CheckoutController.php`
- `app/Http/Controllers/ReportController.php`
- `app/Http/Controllers/ProductReviewController.php`
- `app/Http/Controllers/Seller/ProductController.php`
- `app/Http/Controllers/MessageController.php`

Examples to explain:
- Buyers cannot order their own products
- Review submission is limited to completed purchases and one review per order item
- Reports validate seller/product consistency
- Seller product editing is limited to owned products
- Conversations are policy-protected so only participants can access them

## 10. Suggested Presentation Script

Short version:

1. "We separated buyer, seller, and admin access using Laravel guards and middleware."
2. "We use CSRF tokens on forms and Form Requests for server-side validation."
3. "We use Laravel policies to protect orders, products, messages, reports, and admin notifications."
4. "Our database uses foreign keys, cascades, nullable foreign relations where needed, and performance indexes."
5. "We also added feature tests for critical flows like checkout, reports, wishlist, admin approvals, and review uploads."

## 11. Best Files to Show Live

If you only show a few files during the defense, use:
- `config/auth.php`
- `app/Providers/AppServiceProvider.php`
- `app/Http/Requests/Seller/StoreProductRequest.php`
- `app/Http/Requests/Report/StoreReportRequest.php`
- `app/Policies/ProductPolicy.php`
- `database/migrations/2026_04_24_110000_create_reports_table.php`
- `database/migrations/2026_05_14_120000_add_performance_indexes_for_marketplace_queries.php`
- `tests/Feature/CheckoutFlowTest.php`
- `tests/Feature/ReportSubmissionTest.php`
