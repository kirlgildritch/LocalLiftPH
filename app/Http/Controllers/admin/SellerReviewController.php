<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Seller;
use App\Models\SellerDocumentRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SellerReviewController extends Controller
{
    protected function documentRequestReasons(): array
    {
        return [
            'proof_of_address' => 'Proof of Address',
            'tax_identification_number' => 'Tax Identification Number',
            'bank_statement' => 'Bank Statement',
        ];
    }

    public function index(Request $request): View
    {
        $status = (string) $request->string('status', '');
        $search = trim((string) $request->string('search', ''));
        $perPage = 10;

        $baseQuery = Seller::query()
            ->with([
                'user',
                'user.products.category',
                'user.products.reports',
                'latestDocumentRequest',
            ]);

        $sellers = (clone $baseQuery)
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $sellerQuery) use ($search) {
                    $sellerQuery
                        ->where('store_name', 'like', '%' . $search . '%')
                        ->orWhere('full_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('contact_number', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%')
                                ->orWhere('address', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($status !== '', function (Builder $query) use ($status) {
                match ($status) {
                    'active' => $query->where('application_status', Seller::STATUS_APPROVED),
                    'pending' => $query->where('application_status', Seller::STATUS_PENDING),
                    'rejected' => $query->where('application_status', Seller::STATUS_REJECTED),
                    'flagged' => $query->whereHas(
                        'user.products.reports',
                        fn (Builder $reportQuery) => $reportQuery->where('status', Report::STATUS_PENDING),
                    ),
                    default => null,
                };
            })
            ->latest('submitted_at')
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $allSellers = Seller::query();

        $stats = [
            ['label' => 'Approved Sellers', 'value' => (clone $allSellers)->where('application_status', Seller::STATUS_APPROVED)->count(), 'tone' => 'green'],
            ['label' => 'Pending Review', 'value' => (clone $allSellers)->where('application_status', Seller::STATUS_PENDING)->count(), 'tone' => 'orange'],
            ['label' => 'Rejected', 'value' => (clone $allSellers)->where('application_status', Seller::STATUS_REJECTED)->count(), 'tone' => 'red'],
            ['label' => 'Total Applications', 'value' => (clone $allSellers)->count(), 'tone' => 'blue'],
        ];

        return view('admin.sellers', [
            'sellers' => $sellers,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
            'documentRequestReasons' => $this->documentRequestReasons(),
        ]);
    }

    public function updateStatus(Request $request, Seller $seller): RedirectResponse
    {
        $requestMoreDocuments = $request->boolean('request_more_documents');

        $validated = $request->validate([
            'application_status' => ['required', Rule::in([Seller::STATUS_PENDING, Seller::STATUS_APPROVED, Seller::STATUS_REJECTED])],
            'review_notes' => ['nullable', 'string', 'max:1000'],
            'document_request_reason' => [
                Rule::requiredIf($requestMoreDocuments),
                'nullable',
                Rule::in(array_keys($this->documentRequestReasons())),
            ],
        ]);

        if ($requestMoreDocuments) {
            DB::transaction(function () use ($seller, $validated) {
                $seller->documentRequests()
                    ->whereIn('status', [SellerDocumentRequest::STATUS_PENDING, SellerDocumentRequest::STATUS_RESUBMITTED])
                    ->update([
                        'status' => SellerDocumentRequest::STATUS_RESOLVED,
                        'resolved_at' => now(),
                    ]);

                $seller->documentRequests()->create([
                    'reason' => $validated['document_request_reason'],
                    'admin_notes' => $validated['review_notes'] ?: null,
                    'status' => SellerDocumentRequest::STATUS_PENDING,
                    'requested_at' => now(),
                ]);

                $seller->update([
                    'application_status' => Seller::STATUS_PENDING,
                    'review_notes' => $validated['review_notes'] ?: null,
                    'reviewed_at' => now(),
                ]);
            });

            return back()->with('success', 'Document request sent to seller.');
        }

        $newStatus = $validated['application_status'];
        $statusChanged = $seller->application_status !== $newStatus;

        DB::transaction(function () use ($seller, $validated, $newStatus, $statusChanged) {
            $seller->update([
                'application_status' => $newStatus,
                'review_notes' => $validated['review_notes'] ?: null,
                'reviewed_at' => $statusChanged ? now() : $seller->reviewed_at,
            ]);

            if (in_array($newStatus, [Seller::STATUS_APPROVED, Seller::STATUS_REJECTED], true)) {
                $seller->documentRequests()
                    ->whereIn('status', [SellerDocumentRequest::STATUS_PENDING, SellerDocumentRequest::STATUS_RESUBMITTED])
                    ->update([
                        'status' => SellerDocumentRequest::STATUS_RESOLVED,
                        'resolved_at' => now(),
                    ]);
            }
        });

        return back()->with('success', 'Seller application status updated.');
    }
}
