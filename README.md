# LocalLift PH

LocalLift PH is a web-based marketplace platform for local products. It provides a centralized online environment where buyers can browse and order local products, sellers can manage their shops and product listings, and administrators can monitor and moderate marketplace activities.

## About the Project

Many local sellers still rely on scattered selling channels such as personal social media pages, chat-based ordering, and manual transaction coordination. These processes can make product discovery, seller verification, communication, order monitoring, and customer trust difficult to manage.

LocalLift PH addresses these concerns by providing a Laravel-based marketplace system where local products, seller shops, buyer transactions, and administrator moderation are handled in one organized platform.

## User Roles

### Buyer

Buyers can register and log in, browse products and seller shops, view product details, add items to cart, manage delivery addresses, place orders, view order history, cancel eligible orders, confirm received orders, submit product reviews, and communicate with sellers through the messaging feature.

### Seller

Sellers can register, submit a seller application, manage shop information, add and update product listings, monitor product stock and status, view received orders, check earnings summaries, manage buyer conversations, reply to reviews, and receive notifications for important marketplace activities.

### Administrator

Administrators can log in through the admin access flow, view the admin dashboard, review seller applications, approve or reject seller accounts, approve or reject product listings, monitor orders, review payouts, view submitted reports, manage seller review records, and receive admin notifications.

## Main Features

- Buyer, seller, and administrator authentication
- Google sign-in support for supported buyer and seller accounts
- Seller application and approval workflow
- Product listing management
- Product approval and moderation
- Category-based product browsing
- Cart and checkout process
- Address management
- Order history and order tracking
- Order cancellation with cancellation reasons
- Product reviews with optional image and video attachments
- Seller replies to buyer reviews
- Buyer-seller messaging
- Real-time chat messages
- Typing indicators
- Read receipts
- Seller notifications
- Admin notifications
- Report monitoring
- Payout monitoring
- Laravel Reverb support for real-time broadcasting
- Queue worker support for background jobs

## Technology Stack

- Laravel
- PHP
- Blade
- MySQL
- Laravel migrations and models
- Laravel authentication guards
- Laravel events and broadcasting
- Laravel notifications
- Laravel queues
- Laravel Reverb
- Vite
- Tailwind CSS
- JavaScript
- Composer
- NPM

## Project Structure

### `app/`

Contains the main backend logic of the system, including models, controllers, events, notifications, policies, providers, and request validation classes.

### `app/Http/Controllers`

Handles buyer, seller, and administrator requests. Controllers process actions such as product management, checkout, order handling, seller review, product approval, reports, payouts, and notifications.

### `app/Http/Requests`

Contains custom validation classes for user-submitted forms such as product forms, seller applications, checkout forms, profile updates, and review submissions.

### `app/Models`

Contains database model classes used to manage records such as users, sellers, products, categories, carts, orders, order items, reviews, review media, conversations, messages, reports, and seller payouts.

### `app/Events`

Contains real-time event classes used for chat messages, typing indicators, read receipts, and other broadcasted system updates.

### `app/Notifications`

Contains notification classes used to alert buyers, sellers, and administrators about important marketplace activities.

### `app/Providers`

Contains service provider classes that register and prepare application services such as policies, shared view data, authentication behavior, and system-wide configuration.

### `bootstrap/`

Initializes the Laravel application and contains cached framework files.

### `config/`

Contains configuration files for the application, authentication, database, filesystems, broadcasting, queues, mail, sessions, cache, and services.

### `database/`

Contains migrations, factories, and seeders used to build and populate the database.

### `public/`

Contains files directly accessible by the browser, including `index.php`, compiled frontend assets, public images, `favicon.ico`, `robots.txt`, and the `storage` link.

### `resources/`

Contains Blade views, CSS source files, and JavaScript source files used for the user interface.

### `routes/`

Contains route definitions for web pages, authentication, buyer pages, seller pages, admin pages, and broadcasting channels.

### `storage/`

Contains application-generated files such as uploaded media, logs, cache files, sessions, and compiled views.

### `tests/`

Contains automated test files used to verify application features and system behavior.

### `vendor/`

Contains Composer-installed PHP dependencies, including the Laravel framework.

### `node_modules/`

Contains NPM-installed frontend dependencies used by Vite, Tailwind CSS, and JavaScript build tools.

## Uploaded Media Storage

Uploaded images and videos are not stored directly in the database. The actual files are stored in the Laravel storage directory, while the database stores only the file path.

Example storage locations:

```text
storage/app/public/products
storage/app/public/reviews/images
storage/app/public/reviews/videos
storage/app/public/messages
