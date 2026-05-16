<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ShopFollow;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $wishlists = Wishlist::query()
            ->with([
                'product' => function ($query) {
                    $query
                        ->with(['user.sellerProfile', 'category'])
                        ->withAvg('reviews', 'rating')
                        ->withCount('reviews');
                },
            ])
            ->where('user_id', Auth::id())
            ->whereHas('product', function ($query) {
                $query->visibleToBuyers();
            })
            ->latest()
            ->paginate(12);

        $followedShops = ShopFollow::query()
            ->with([
                'seller' => function ($query) {
                    $query
                        ->with('sellerProfile')
                        ->withCount([
                            'products' => function ($productQuery) {
                                $productQuery->visibleToBuyers();
                            },
                        ]);
                },
            ])
            ->where('user_id', Auth::id())
            ->whereHas('seller', function ($query) {
                $query->visibleSellerShops();
            })
            ->latest()
            ->get();

        return view('buyer.wishlist', compact('wishlists', 'followedShops'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $product->loadMissing('user.sellerProfile');

        abort_if(! Product::query()->whereKey($product->id)->visibleToBuyers()->exists(), 404);

        if ((int) $product->user_id === (int) Auth::id()) {
            return back()->with('error', 'You cannot add your own product to your wishlist.');
        }

        Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'Product added to your wishlist.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        Wishlist::query()
            ->where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', 'Product removed from your wishlist.');
    }
}
