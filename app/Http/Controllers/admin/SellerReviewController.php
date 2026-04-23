<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SellerReviewController extends Controller
{
    public function index(): View
    {
        $sellers = Seller::with(['user', 'user.products'])
            ->latest('submitted_at')
            ->get();

        $stats = [
            ['label' => 'Approved Sellers', 'value' => $sellers->where('application_status', 'approved')->count(), 'tone' => 'green'],
            ['label' => 'Pending Review', 'value' => $sellers->where('application_status', 'pending')->count(), 'tone' => 'orange'],
            ['label' => 'Rejected', 'value' => $sellers->where('application_status', 'rejected')->count(), 'tone' => 'red'],
            ['label' => 'Total Applications', 'value' => $sellers->count(), 'tone' => 'blue'],
        ];

        return view('admin.sellers', compact('sellers', 'stats'));
    }

    public function updateStatus(Request $request, Seller $seller): RedirectResponse
    {
        $validated = $request->validate([
            'application_status' => ['required', Rule::in([Seller::STATUS_PENDING, Seller::STATUS_APPROVED, Seller::STATUS_REJECTED])],
            'review_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $newStatus = $validated['application_status'];
        $statusChanged = $seller->application_status !== $newStatus;

        $seller->update([
            'application_status' => $newStatus,
            'review_notes' => $validated['review_notes'] ?: null,
            'reviewed_at' => $statusChanged ? now() : $seller->reviewed_at,
        ]);

        return back()->with('success', 'Seller application status updated.');
    }
}
