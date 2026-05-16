<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\SubmitSellerApplicationRequest;
use App\Models\Conversation;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerDocumentRequest;
use App\Notifications\SellerModerationNotification;
use App\Services\AdminActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SellerDashboardController extends Controller
{
    private function readableAddress(array $validated): string
    {
        return collect([
            $validated['street_address'] ?? null,
            filled($validated['landmark'] ?? null) ? 'Landmark: ' . $validated['landmark'] : null,
            $validated['barangay'] ?? null,
            $validated['city'] ?? null,
            $validated['province'] ?? null,
            $validated['region'] ?? null,
            $validated['postal_code'] ?? null,
        ])->filter()->implode(', ');
    }

    public function show(Request $request): View
    {
        $user = Auth::guard('seller')->user();
        $seller = Seller::with('latestDocumentRequest')->where('user_id', $user->id)->first();
        $latestDocumentRequest = $seller?->latestDocumentRequest;
        $moderationNotifications = $user->notifications()
            ->where(function ($query) {
                $query->where('type', SellerModerationNotification::class)
                    ->orWhere('data->type', 'admin');
            })
            ->latest()
            ->take(5)
            ->get();

        $dashboardState = $this->resolveDashboardState($request, $seller, $latestDocumentRequest);

        $stats = [
            'total_sales' => 0,
            'orders_received' => 0,
            'products_listed' => 0,
            'pending_orders' => 0,
            'active_products' => 0,
            'open_conversations' => 0,
        ];

        $recentOrders = collect();

        if ($seller && $seller->application_status === Seller::STATUS_APPROVED) {
            $stats = $this->buildApprovedDashboardStats($user->id);
            $recentOrders = $this->recentOrders($user->id);
        }

        return view('seller.dashboard', compact(
            'seller',
            'latestDocumentRequest',
            'moderationNotifications',
            'dashboardState',
            'stats',
            'recentOrders'
        ));
    }

    public function submitApplication(SubmitSellerApplicationRequest $request, AdminActivityService $adminActivity): RedirectResponse
    {
        $user = Auth::guard('seller')->user();
        $existingSeller = Seller::with('latestDocumentRequest')->where('user_id', $user->id)->first();
        $latestDocumentRequest = $existingSeller?->latestDocumentRequest;
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated, $existingSeller, $user, $latestDocumentRequest) {
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
                'address' => $this->readableAddress($validated),
                'street_address' => $validated['street_address'],
                'barangay' => $validated['barangay'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'region' => $validated['region'],
                'postal_code' => $validated['postal_code'],
                'landmark' => $validated['landmark'],
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

            if ($latestDocumentRequest && $latestDocumentRequest->status === SellerDocumentRequest::STATUS_PENDING) {
                $latestDocumentRequest->update([
                    'response_document_path' => $request->file('requested_document')
                        ? $request->file('requested_document')->store('seller_documents/requests', 'public')
                        : $latestDocumentRequest->response_document_path,
                    'status' => SellerDocumentRequest::STATUS_RESUBMITTED,
                    'responded_at' => now(),
                ]);
            }

            $user->forceFill([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone_number'],
                'address' => $this->readableAddress($validated),
                'is_seller' => true,
            ])->save();
        });

        $seller = Seller::with('latestDocumentRequest')->where('user_id', $user->id)->first();
        $isResubmission = $latestDocumentRequest?->status === SellerDocumentRequest::STATUS_PENDING;

        $adminActivity->sellerApplicationSubmitted($seller, $validated['full_name'], $isResubmission);

        return redirect()
            ->route('seller.dashboard')
            ->with('success', 'Application submitted. Your Seller Center access is pending admin review.');
    }
    private function resolveDashboardState(Request $request, ?Seller $seller, ?SellerDocumentRequest $latestDocumentRequest): string
    {
        if (! $seller) {
            return $request->boolean('start_registration') || $request->has('register') || $request->has('resubmit') || $request->session()->getOldInput()
                ? 'filling_form'
                : 'not_started';
        }

        if ($seller->isSuspended()) {
            return 'suspended';
        }

        if ($seller->application_status === Seller::STATUS_APPROVED) {
            return 'approved';
        }

        if ($seller->application_status === Seller::STATUS_REJECTED) {
            return $request->boolean('resubmit') || $request->session()->getOldInput()
                ? 'filling_form'
                : 'rejected';
        }

        if ($latestDocumentRequest?->status === SellerDocumentRequest::STATUS_PENDING) {
            return $request->boolean('resubmit') || $request->session()->getOldInput()
                ? 'filling_form'
                : 'documents_requested';
        }

        return 'pending';
    }

    private function buildApprovedDashboardStats(int $sellerId): array
    {
        $approvedProducts = Product::query()
            ->where('user_id', $sellerId)
            ->where('status', Product::STATUS_APPROVED)
            ->where('is_active', 1);

        $productsListed = (clone $approvedProducts)->count();
        $approvedProductIds = (clone $approvedProducts)->select('id');
        $orderItems = OrderItem::query()->whereIn('product_id', $approvedProductIds);

        return [
            'total_sales' => (float) $orderItems->sum(\DB::raw('quantity * price')),
            'orders_received' => (clone $orderItems)->distinct('order_id')->count('order_id'),
            'products_listed' => $productsListed,
            'pending_orders' => (clone $orderItems)->whereHas('order', function ($query) {
                $query->whereIn('status', ['pending', 'processing']);
            })->distinct('order_id')->count('order_id'),
            'active_products' => $productsListed,
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

}
