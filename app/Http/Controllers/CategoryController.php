<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = collect([
            (object) ['name' => 'Fashion', 'icon' => 'fa-shirt', 'count' => 24],
            (object) ['name' => 'Beauty', 'icon' => 'fa-heart', 'count' => 18],
            (object) ['name' => 'Electronics', 'icon' => 'fa-mobile-screen', 'count' => 32],
            (object) ['name' => 'Home & Living', 'icon' => 'fa-couch', 'count' => 15],
            (object) ['name' => 'Food', 'icon' => 'fa-utensils', 'count' => 20],
            (object) ['name' => 'Bags', 'icon' => 'fa-bag-shopping', 'count' => 11],
            (object) ['name' => 'Shoes', 'icon' => 'fa-shoe-prints', 'count' => 17],
            (object) ['name' => 'Accessories', 'icon' => 'fa-gem', 'count' => 13],
            (object) ['name' => 'Souvenirs', 'icon' => 'fa-gift', 'count' => 9],
            (object) ['name' => 'Books', 'icon' => 'fa-book', 'count' => 7],
            (object) ['name' => 'Toys', 'icon' => 'fa-puzzle-piece', 'count' => 6],
            (object) ['name' => 'Pets', 'icon' => 'fa-paw', 'count' => 10],
        ]);

        return view('categories.index', compact('categories'));
    }
}