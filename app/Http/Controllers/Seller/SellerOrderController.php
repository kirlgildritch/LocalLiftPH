<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SellerOrderController extends Controller
{
         public function index()
{
    $orders = collect();

    return view('seller.orders', compact('orders'));
}
}
