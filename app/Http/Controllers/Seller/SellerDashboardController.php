<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SellerDashboardController extends Controller
{
    public function show(Request $request): View
    {
        $user = Auth::guard('seller')->user();
        $seller = Seller::where('user_id', $user->id)->first();

        $dashboardState = $this->resolveDashboardState($request, $seller);

        $stats = [
            'total_sales' => 0,
            'orders_received' => 0,
            'products_listed' => 0,
            'pending_orders' => 0,
            'active_products' => 0,
            'open_conversations' => 0,
        ];

        $recentOrders = collect();
        $recentProducts = collect();

        if ($seller && $seller->application_status === Seller::STATUS_APPROVED) {
            $stats = $this->buildApprovedDashboardStats($user->id);
            $recentOrders = $this->recentOrders($user->id);
            $recentProducts = $this->recentProducts($user->id);
        }

        return view('seller.dashboard', compact(
            'seller',
            'dashboardState',
            'stats',
            'recentOrders',
            'recentProducts'
        ));
    }

    public function submitApplication(Request $request): RedirectResponse
    {
        $user = Auth::guard('seller')->user();
        $existingSeller = Seller::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'seller_type' => ['required', Rule::in(['individual', 'registered_business'])],
            'full_name' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:18', 'max:120'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'valid_id_type' => ['required', 'string', 'max:100'],
            'valid_id_number' => ['required', 'string', 'max:120'],
            'valid_id_document' => [
                Rule::requiredIf(! $existingSeller?->valid_id_path),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,webp',
                'max:4096',
            ],
            'business_permit' => [
                Rule::requiredIf(
                    $request->input('seller_type') === 'registered_business'
                    && ! $existingSeller?->business_permit_path
                ),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,webp',
                'max:4096',
            ],
        ]);

        DB::transaction(function () use ($request, $validated, $existingSeller, $user) {
            $seller = $existingSeller ?? new Seller(['user_id' => $user->id]);

            if ($request->hasFile('valid_id_document')) {
                $seller->valid_id_path = $request->file('valid_id_document')->store('seller_documents/ids', 'public');
            }

            if ($request->hasFile('business_permit')) {
                $seller->business_permit_path = $request->file('business_permit')->store('seller_documents/permits', 'public');
            }

            $seller->fill([
                'seller_type' => $validated['seller_type'],
                'full_name' => $validated['full_name'],
                'age' => $validated['age'],
                'email' => $validated['email'],
                'contact_number' => $validated['phone_number'],
                'address' => $validated['address'],
                'valid_id_type' => $validated['valid_id_type'],
                'valid_id_number' => $validated['valid_id_number'],
                'application_status' => Seller::STATUS_PENDING,
                'review_notes' => null,
                'submitted_at' => now(),
                'reviewed_at' => null,
                'store_name' => $seller->store_name ?: $validated['full_name'] . '\'s Shop',
                'store_description' => $seller->store_description ?: 'Seller application submitted and pending admin approval.',
            ]);
            $seller->save();

            $user->forceFill([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone_number'],
                'address' => $validated['address'],
                'is_seller' => true,
            ])->save();
        });

        return redirect()
            ->route('seller.dashboard')
            ->with('success', 'Application submitted. Your Seller Center access is pending admin review.');
    }

    private function resolveDashboardState(Request $request, ?Seller $seller): string
    {
        if (! $seller) {
            return $request->boolean('start_registration') || $request->has('register') || $request->has('resubmit') || $request->session()->getOldInput()
                ? 'filling_form'
                : 'not_started';
        }

        if ($seller->application_status === Seller::STATUS_APPROVED) {
            return 'approved';
        }

        if ($seller->application_status === Seller::STATUS_REJECTED) {
            return $request->boolean('resubmit') || $request->session()->getOldInput()
                ? 'filling_form'
                : 'rejected';
        }

        return 'pending';
    }

    private function buildApprovedDashboardStats(int $sellerId): array
    {
        $approvedProductIds = Product::where('user_id', $sellerId)
            ->where('status', Product::STATUS_APPROVED)
            ->where('is_active', 1)
            ->pluck('id');

        $orderItems = OrderItem::whereIn('product_id', $approvedProductIds);

        return [
            'total_sales' => (float) $orderItems->sum(\DB::raw('quantity * price')),
            'orders_received' => (clone $orderItems)->distinct('order_id')->count('order_id'),
            'products_listed' => (int) $approvedProductIds->count(),
            'pending_orders' => (clone $orderItems)->whereHas('order', function ($query) {
                $query->whereIn('status', ['pending', 'processing']);
            })->distinct('order_id')->count('order_id'),
            'active_products' => Product::where('user_id', $sellerId)
                ->where('status', Product::STATUS_APPROVED)
                ->where('is_active', 1)
                ->count(),
            'open_conversations' => Conversation::where('seller_id', $sellerId)->count(),
        ];
    }

    private function recentOrders(int $sellerId)
    {
        return OrderItem::with(['order', 'product'])
            ->whereHas('product', function ($query) use ($sellerId) {
                $query->where('user_id', $sellerId);
            })
            ->latest()
            ->take(4)
            ->get();
    }

    private function recentProducts(int $sellerId)
    {
        return Product::where('user_id', $sellerId)
            ->latest()
            ->take(4)
            ->get();
    }
}
