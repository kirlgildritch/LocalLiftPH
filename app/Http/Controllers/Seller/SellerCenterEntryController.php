<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerCenterEntryController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if (Auth::guard('seller')->check()) {
            return redirect()->route('seller.dashboard');
        }

        return redirect()
            ->route('seller.login')
            ->with('seller_center_notice', 'Buyer session closed. Continue in Seller Center.');
    }
}
