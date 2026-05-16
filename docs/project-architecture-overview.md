# Project Architecture Overview

This document is a presentation-ready overview of how LocalLift PH is structured and why the codebase is organized the way it is.

## 1. System Purpose

LocalLift PH is a Laravel marketplace platform with three main roles:
- Buyer
- Seller
- Administrator

The system centralizes product discovery, seller verification, orders, messaging, moderation, reports, and payouts into one application.

## 2. Main Architecture Style

The project follows Laravel's MVC structure:
- Models represent marketplace entities and reusable domain logic.
- Controllers handle request flow and coordinate application behavior.
- Blade views render buyer, seller, and admin interfaces.
- Form Requests centralize validation.
- Policies centralize authorization.
- Services handle shared business processes such as admin activity notifications.

This gives the project a clean server-rendered Laravel architecture without mixing too much business logic directly into the UI.

## 3. Role-Based Application Areas

### Buyer Area

Primary responsibilities:
- Browse products and shops
- Manage cart and checkout
- Save delivery addresses
- View and manage orders
- Submit product reviews
- Message sellers
- Report products or sellers

Main route file:
- `routes/buyer.php`

Representative controllers:
- `app/Http/Controllers/ProductBrowseController.php`
- `app/Http/Controllers/CheckoutController.php`
- `app/Http/Controllers/OrderController.php`
- `app/Http/Controllers/MessageController.php`
- `app/Http/Controllers/ProductReviewController.php`

### Seller Area

Primary responsibilities:
- Manage seller profile and shop settings
- Create and edit products
- Handle orders and inventory
- Monitor earnings and payouts
- Review buyer feedback
- Receive notifications

Main route file:
- `routes/seller.php`

Representative controllers:
- `app/Http/Controllers/Seller/ProductController.php`
- `app/Http/Controllers/SettingsController.php`
- `app/Http/Controllers/Seller/SellerDashboardController.php`

### Admin Area

Primary responsibilities:
- Review seller applications
- Moderate products
- Monitor reports
- Monitor orders and payouts
- Review notifications

Main route file:
- `routes/admin.php`

Representative controllers:
- `app/Http/Controllers/Admin/ProductApprovalController.php`
- `app/Http/Controllers/Admin/SellerReviewController.php`
- `app/Http/Controllers/Admin/AdminReportController.php`
- `app/Http/Controllers/Admin/AdminPayoutController.php`

## 4. Route Organization

Routes were split by domain to improve maintainability:
- `routes/frontend.php`
- `routes/buyer.php`
- `routes/seller.php`
- `routes/admin.php`

This makes it easier to explain which flows belong to which role and gives the project a more professional Laravel structure.

## 5. Validation Strategy

Validation is centralized in Laravel Form Requests under:
- `app/Http/Requests`

Examples:
- `app/Http/Requests/Checkout/CheckoutStoreRequest.php`
- `app/Http/Requests/Message/StoreMessageRequest.php`
- `app/Http/Requests/Report/StoreReportRequest.php`
- `app/Http/Requests/Seller/StoreProductRequest.php`
- `app/Http/Requests/Seller/UpdateProductRequest.php`

Why this matters:
- Controllers stay shorter and easier to maintain.
- Validation rules are reusable and easier to audit.
- The project follows Laravel best practices more consistently.

## 6. Authorization Strategy

Authorization is handled by both middleware and policies.

Middleware protects route groups:
- buyer routes
- seller routes
- admin routes

Policies protect individual resources:
- `app/Policies/OrderPolicy.php`
- `app/Policies/ConversationPolicy.php`
- `app/Policies/ProductPolicy.php`
- `app/Policies/ReviewPolicy.php`
- `app/Policies/SellerPolicy.php`
- `app/Policies/ReportPolicy.php`
- `app/Policies/DatabaseNotificationPolicy.php`

Short explanation for defense:
- middleware controls who may enter an area
- policies control who may act on a specific record

## 7. Shared Business Logic

The project now uses a service for repeated admin-activity notification logic:
- `app/Services/AdminActivityService.php`

This service is used by multiple controllers to avoid duplicating notification behavior when:
- seller profiles change
- seller applications are submitted or updated
- products are submitted, updated, or deleted
- reports are created

## 8. Model Responsibilities

Models are used for both relationships and reusable domain logic.

Examples:
- `app/Models/Product.php` now owns reusable product-detail display logic
- `app/Models/Order.php` contains order status helpers and flow rules
- `app/Models/Seller.php` contains seller visibility and shop-state helpers

This reduces the amount of logic left in Blade views and makes the code easier to test.

## 9. View Structure

The UI is built with Blade, but several large pages were refactored into partials for readability.

Examples:
- product detail page partials under `resources/views/products/partials/show`
- inbox partials under `resources/views/messages/partials/inbox`
- admin product approval partials under `resources/views/admin/product-approvals`
- buyer order partials under `resources/views/buyer/orders/partials`
- buyer address partials under `resources/views/buyer/addresses/partials`
- admin report and seller review partials under `resources/views/admin/reports/partials` and `resources/views/admin/sellers/partials`

Why this matters:
- better code organization
- easier maintenance
- clearer presentation of UI sections

## 10. Frontend Behavior

The project uses:
- Blade-rendered pages
- JavaScript modules for interactivity
- Laravel Echo and Reverb for real-time features
- Vite for modern asset handling on supported frontend modules

Examples of extracted JS assets:
- header behavior
- floating chat
- product detail behavior
- admin seller and report modal behavior

This keeps large inline scripts out of Blade and improves browser caching.

## 11. Performance Improvements Already Applied

Key optimization work completed:
- heavy inline JS moved to cacheable assets
- fake full-page skeleton loading removed
- AJAX product pagination added
- shared page-load work reduced
- chat loading deferred
- performance indexes added for key marketplace and messaging queries
- several large Blade files split into partials

Key evidence:
- `database/migrations/2026_05_14_120000_add_performance_indexes_for_marketplace_queries.php`
- `public/assets/js`
- `app/Providers/AppServiceProvider.php`

## 12. Testing Strategy

The project includes feature tests for critical flows under:
- `tests/Feature`

Examples already covered:
- checkout flow
- message inbox access
- product pagination
- product review upload
- product show rendering
- profile update flow
- report submission
- seller product editing
- wishlist flow
- admin product approval

This helps prove that important features are not only implemented, but also verified.

## 13. Suggested Defense Script

Short version:

1. "We structured the project by role: buyer, seller, and admin."
2. "We split routes by domain so each area is easier to manage and defend."
3. "We use Form Requests for validation and policies for authorization."
4. "We moved repeated logic into services and reusable model methods."
5. "We refactored large Blade pages into partials to improve maintainability."
6. "We added feature tests and performance optimizations to support reliability and usability."

## 14. Best Files To Show During Presentation

If you want a strong live walkthrough, show:
- `routes/buyer.php`
- `routes/seller.php`
- `routes/admin.php`
- `app/Http/Requests/Seller/StoreProductRequest.php`
- `app/Policies/ProductPolicy.php`
- `app/Services/AdminActivityService.php`
- `app/Models/Product.php`
- `resources/views/products/show.blade.php`
- `resources/views/admin/reports.blade.php`
- `tests/Feature/CheckoutFlowTest.php`
- `tests/Feature/AdminProductApprovalTest.php`
- `docs/security-and-database-evidence.md`

