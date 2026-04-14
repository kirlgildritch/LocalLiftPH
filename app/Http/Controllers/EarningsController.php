<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    public function index()
    {
        $totalEarnings = 0;
        $monthlyEarnings = 0;
        $pendingPayout = 0;

        return view('seller.earnings', compact('totalEarnings', 'monthlyEarnings', 'pendingPayout'));
    }
}
