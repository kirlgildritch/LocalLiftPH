<?php
use App\Http\Controllers\Buyer\AddressController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\EarningsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Seller\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductBrowseController;
use App\Http\Controllers\ShopController;
use App\Models\Product;
Route::get('/', function () {
    $categoryIcons = [
        'food' => 'fa-utensils',
        'food and drinks' => 'fa-utensils',
        'clothing' => 'fa-shirt',
        'clothing and fashion' => 'fa-shirt',
        'fashion' => 'fa-shirt',
        'handmade crafts' => 'fa-palette',
        'crafts' => 'fa-palette',
        'accessories' => 'fa-bag-shopping',
        'souvenirs' => 'fa-gift',
        'souvenirs and gifts' => 'fa-gift',
        'beauty' => 'fa-heart',
        'electronics' => 'fa-mobile-screen',
        'home & living' => 'fa-couch',
        'home and living' => 'fa-couch',
        'bags' => 'fa-bag-shopping',
        'shoes' => 'fa-shoe-prints',
        'books' => 'fa-book',
        'toys' => 'fa-puzzle-piece',
        'pets' => 'fa-paw',
    ];

    $featuredProducts = Product::with('user')
        ->where('is_active', 1)
        ->latest()
        ->take(4)
        ->get();

    $featuredCategories = Product::query()
        ->where('is_active', 1)
        ->selectRaw('category, COUNT(*) as product_count')
        ->whereNotNull('category')
        ->where('category', '!=', '')
        ->groupBy('category')
        ->orderByDesc('product_count')
        ->take(5)
        ->get()
        ->map(function ($category) use ($categoryIcons) {
            $key = strtolower(trim($category->category));

            return (object) [
                'name' => $category->category,
                'count' => (int) $category->product_count,
                'icon' => $categoryIcons[$key] ?? 'fa-grid-2',
            ];
        });

    return view('home', compact('featuredProducts', 'featuredCategories'));
})->name('home');

Route::get('/shops', function () {
    return view('shops.index');
})->name('shops.index');

Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
Route::get('/shops/{user}', [ShopController::class, 'show'])->name('shops.show');

Route::get('/products/suggestions', [ProductBrowseController::class, 'suggestions'])->name('products.suggestions');
Route::get('/products', [ProductBrowseController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductBrowseController::class, 'show'])->name('products.show');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'seller') {
        return redirect()->route('seller.dashboard');
    }

    if ($user->role === 'buyer') {
        return redirect()->route('home');
    }

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('/about', 'about')->name('about');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
 
Route::middleware(['auth', 'seller'])->group(function () {
    Route::get('/seller-dashboard', function () {
        return view('seller.dashboard');
    })->name('seller.dashboard');

    Route::get('/add-product', [ProductController::class, 'create'])->name('seller.products.create');
    Route::post('/add-product', [ProductController::class, 'store'])->name('seller.products.store');
    Route::get('/manage-products', [ProductController::class, 'index'])->name('seller.products.index');

    Route::get('/seller-orders', [SellerOrderController::class, 'index'])->name('seller.orders');
    Route::get('/seller-earnings', [EarningsController::class, 'index'])->name('seller.earnings');
    Route::get('/seller-messages', [MessageController::class, 'index'])->name('seller.messages');

    Route::get('/seller-settings', [SettingsController::class, 'index'])->name('seller.settings');
    Route::patch('/seller-settings', [SettingsController::class, 'update'])->name('seller.settings.update');
    Route::patch('/seller-settings/notifications', [SettingsController::class, 'updateNotifications'])->name('seller.settings.notifications');
    Route::patch('/seller-settings/policies', [SettingsController::class, 'updatePolicies'])->name('seller.settings.policies');
    Route::patch('/seller-settings/payout', [SettingsController::class, 'updatePayout'])->name('seller.settings.payout');
    Route::patch('/seller-settings/inventory', [SettingsController::class, 'updateInventory'])->name('seller.settings.inventory');
    Route::patch('/seller-settings/status', [SettingsController::class, 'updateStatus'])->name('seller.settings.status');

    Route::get('/seller-profile', [ProfileController::class, 'edit'])->name('seller.profile');
    Route::patch('/seller-profile', [ProfileController::class, 'update'])->name('seller.profile.update');
    Route::delete('/seller-profile', [ProfileController::class, 'destroy'])->name('seller.profile.destroy');

    Route::get('/seller-shop-preview', [SettingsController::class, 'preview'])->name('seller.shop.preview');
});


Route::middleware(['auth', 'buyer'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{productId}', [CartController::class, 'store'])->name('cart.add');
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'destroy'])->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/become-seller', [SellerController::class, 'create'])->name('seller.setup');
    Route::post('/become-seller', [SellerController::class, 'store'])->name('seller.store');

    Route::get('/buyer-profile', [ProfileController::class, 'buyerEdit'])->name('buyer.profile');
    Route::patch('/buyer-profile', [ProfileController::class, 'buyerUpdate'])->name('buyer.profile.update');

    Route::get('/my-addresses', [AddressController::class, 'index'])->name('buyer.addresses');
    Route::get('/my-addresses/create', [AddressController::class, 'create'])->name('buyer.addresses.create');
    Route::post('/my-addresses', [AddressController::class, 'store'])->name('buyer.addresses.store');
    Route::patch('/my-addresses/{address}', [AddressController::class, 'update'])->name('buyer.addresses.update');
    Route::delete('/my-addresses/{address}', [AddressController::class, 'destroy'])->name('buyer.addresses.destroy');
    Route::patch('/my-addresses/{address}/default', [AddressController::class, 'setDefault'])->name('buyer.addresses.default');

    Route::get('/my-orders', [OrderController::class, 'index'])->name('buyer.orders');


});

require __DIR__.'/auth.php';
