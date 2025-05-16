<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CommandController extends Controller
{       
        public function toggleStatus($userId, $orderNum)
{
    // Получаем текущий статус из базы — например, берём первый найденный заказ пользователя с этим номером заказа
    $firstOrder = DB::table('orders')
        ->where('userid', $userId)
        ->where('ordernum', $orderNum)
        ->first();

    if (!$firstOrder) {
        return redirect()->back()->with('error', 'Заказ не найден');
    }

    // Определяем новый статус
    $newStatus = $firstOrder->status === 'Принят в работу' ? 'в обработке' : 'Принят в работу';

    // Обновляем статус для всех позиций данного заказа
    DB::table('orders')
        ->where('userid', $userId)
        ->where('ordernum', $orderNum)
        ->update(['status' => $newStatus, 'updated_at' => now()]);

    return redirect()->back()->with('success', "Статус заказа #{$orderNum} изменён на '{$newStatus}'");
}
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
