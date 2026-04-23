<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            ['label' => 'Pending Products', 'value' => Product::where('status', 'pending')->count(), 'note' => 'Awaiting Approval', 'tone' => 'primary'],
            ['label' => 'Pending Sellers', 'value' => Seller::where('application_status', 'pending')->count(), 'note' => 'Verification Needed', 'tone' => 'warning'],
            ['label' => 'Approved Sellers', 'value' => Seller::where('application_status', 'approved')->count(), 'note' => 'Live storefronts', 'tone' => 'success'],
            ['label' => 'Total Buyers', 'value' => User::where('is_admin', false)->where('is_seller', false)->count(), 'note' => 'Registered buyers', 'tone' => 'danger'],
        ];

        $pendingProducts = Product::with(['user', 'category'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $pendingSellers = Seller::with('user')
            ->where('application_status', 'pending')
            ->latest('submitted_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingProducts', 'pendingSellers'));
    }
}
