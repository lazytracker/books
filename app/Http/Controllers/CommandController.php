<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;

class CommandController extends Controller
{
     public function index()
    {
        // Get the authenticated user's cart items with corresponding book details
        $cartItems = Order::with(['book', 'user'])->get();
		$groupedCartItems = $cartItems->groupBy('user_id');
		
        return view('command/command', compact('groupedCartItems'));
    }
}
