<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    public function index(): View
    {
        $reports = Report::with(['user', 'product.user.sellerProfile', 'seller.sellerProfile'])
            ->latest()
            ->get();

        return view('admin.reports', compact('reports'));
    }

    public function resolve(Report $report): RedirectResponse
    {
        $report->update([
            'status' => Report::STATUS_RESOLVED,
        ]);

        return back()->with('success', 'Report marked as resolved.');
    }
}
