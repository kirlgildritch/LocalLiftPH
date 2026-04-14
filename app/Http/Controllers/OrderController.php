<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
      public function index()
    {
        $orders = []; // temporary
        return view('seller.orders', compact('orders'));
    }
}
