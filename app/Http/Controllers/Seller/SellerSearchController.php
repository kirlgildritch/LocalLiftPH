<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SellerSearchController extends Controller
{
    public function index(Request $request): View
    {
        $sellerUser = Auth::guard('seller')->user();
        $query = trim((string) $request->get('q', ''));
        $tools = $this->sellerTools();

        $toolResults = collect();
        $products = collect();
        $orders = collect();
        $conversations = collect();

        if ($query !== '') {
            $toolResults = $this->matchTools($tools, $query);

            $products = Product::with('category')
                ->where('user_id', $sellerUser->id)
                ->where(function ($builder) use ($query) {
                    $builder->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($query) {
                            $categoryQuery->where('name', 'like', "%{$query}%");
                        });
                })
                ->latest()
                ->limit(8)
                ->get();

            $orders = Order::with(['user', 'items.product'])
                ->where('seller_id', $sellerUser->id)
                ->where(function ($builder) use ($query) {
                    $builder->where('id', 'like', "%{$query}%")
                        ->orWhere('shipping_status', 'like', "%{$query}%")
                        ->orWhereHas('user', function ($userQuery) use ($query) {
                            $userQuery->where('name', 'like', "%{$query}%")
                                ->orWhere('email', 'like', "%{$query}%");
                        })
                        ->orWhereHas('items.product', function ($productQuery) use ($query) {
                            $productQuery->where('name', 'like', "%{$query}%");
                        });
                })
                ->latest()
                ->limit(8)
                ->get();

            $conversations = Conversation::with(['buyer', 'latestMessage'])
                ->where('seller_id', $sellerUser->id)
                ->where(function ($builder) use ($query) {
                    $builder->whereHas('buyer', function ($buyerQuery) use ($query) {
                        $buyerQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%");
                    })->orWhereHas('latestMessage', function ($messageQuery) use ($query) {
                        $messageQuery->where('message', 'like', "%{$query}%");
                    });
                })
                ->latest('updated_at')
                ->limit(8)
                ->get();
        }

        return view('seller.search-results', compact(
            'query',
            'toolResults',
            'products',
            'orders',
            'conversations'
        ));
    }

    public function suggestions(Request $request): JsonResponse
    {
        $sellerUser = Auth::guard('seller')->user();
        $query = trim((string) $request->get('q', ''));

        if (mb_strlen($query) < 1) {
            return response()->json([]);
        }

        $toolSuggestions = $this->matchTools($this->sellerTools(), $query)
            ->take(4)
            ->map(fn (array $tool) => $tool['label']);

        $productSuggestions = Product::query()
            ->where('user_id', $sellerUser->id)
            ->where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(3)
            ->pluck('name');

        $orderSuggestions = Order::query()
            ->with('user')
            ->where('seller_id', $sellerUser->id)
            ->where(function ($builder) use ($query) {
                $builder->where('id', 'like', "%{$query}%")
                    ->orWhereHas('user', function ($userQuery) use ($query) {
                        $userQuery->where('name', 'like', "%{$query}%");
                    });
            })
            ->latest()
            ->limit(2)
            ->get()
            ->map(fn (Order $order) => 'Order #' . $order->id);

        $messageSuggestions = Conversation::query()
            ->with('buyer')
            ->where('seller_id', $sellerUser->id)
            ->whereHas('buyer', function ($buyerQuery) use ($query) {
                $buyerQuery->where('name', 'like', "%{$query}%");
            })
            ->latest('updated_at')
            ->limit(2)
            ->get()
            ->map(fn (Conversation $conversation) => 'Message: ' . ($conversation->buyer?->name ?? 'Buyer'));

        $suggestions = $toolSuggestions
            ->concat($productSuggestions)
            ->concat($orderSuggestions)
            ->concat($messageSuggestions)
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values()
            ->take(8)
            ->map(fn (string $label) => [
                'label' => $label,
                'selectable' => true,
            ])
            ->values();

        if ($suggestions->isEmpty()) {
            return response()->json([
                [
                    'label' => 'No seller results found.',
                    'selectable' => false,
                ],
            ]);
        }

        return response()->json($suggestions);
    }

    private function sellerTools(): Collection
    {
        return collect([
            [
                'label' => 'Dashboard',
                'description' => 'Overview, moderation status, and seller stats.',
                'keywords' => 'overview home dashboard stats seller center',
                'url' => route('seller.dashboard'),
            ],
            [
                'label' => 'My Products',
                'description' => 'Manage live, sold out, reviewing, and delisted products.',
                'keywords' => 'products catalog inventory listings live sold out reviewing delisted',
                'url' => route('seller.products.index'),
            ],
            [
                'label' => 'Add Product',
                'description' => 'Create a new product listing for approval.',
                'keywords' => 'add create new product listing',
                'url' => route('seller.products.create'),
            ],
            [
                'label' => 'Orders',
                'description' => 'Review seller orders and update shipping status.',
                'keywords' => 'orders shipping customer fulfilment fulfillment',
                'url' => route('seller.orders'),
            ],
            [
                'label' => 'Earnings',
                'description' => 'Track seller revenue and completed orders.',
                'keywords' => 'earnings revenue income payout sales',
                'url' => route('seller.earnings'),
            ],
            [
                'label' => 'Messages',
                'description' => 'Reply to buyers from the seller inbox.',
                'keywords' => 'messages inbox chat buyer conversations',
                'url' => route('seller.messages'),
            ],
            [
                'label' => 'Settings',
                'description' => 'Update shop details, policies, and seller preferences.',
                'keywords' => 'settings shop preferences policies general payout inventory status',
                'url' => route('seller.settings'),
            ],
            [
                'label' => 'My Profile',
                'description' => 'Edit seller profile, email, password, and profile image.',
                'keywords' => 'profile account password email image',
                'url' => route('seller.profile'),
            ],
            [
                'label' => 'View Shop',
                'description' => 'Preview the public storefront for the seller account.',
                'keywords' => 'shop storefront preview public store',
                'url' => route('seller.shop.preview'),
            ],
        ]);
    }

    private function matchTools(Collection $tools, string $query): Collection
    {
        $needle = mb_strtolower($query);

        return $tools->filter(function (array $tool) use ($needle) {
            $haystack = mb_strtolower(implode(' ', [
                $tool['label'] ?? '',
                $tool['description'] ?? '',
                $tool['keywords'] ?? '',
            ]));

            return str_contains($haystack, $needle);
        })->values();
    }
}
