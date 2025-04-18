<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;

class CommandController extends Controller
{
        
        public function index()
{
    // Get all orders with their associated book and user details
    $cartItems = Order::with(['book', 'user'])->get();
    
    // Group by user_id first, then by ordernum
    $groupedCartItems = $cartItems->groupBy('userid')->map(function ($orders) {
        // Instead of using first(), we can just access the user from the first order
        $user = $orders->first()->user; // Get the user from the first order
        return [
            'user' => $user, // Store the user
            'orders' => $orders->groupBy('ordernum') // Group by order number
        ];
    });
    
    
    return view('command/command', compact('groupedCartItems'));
}
}
