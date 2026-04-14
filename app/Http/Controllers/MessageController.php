<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
        public function index()
    {
        $messages = []; // temporary
        return view('seller.messages', compact('messages'));
    }

}
