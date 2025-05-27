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
        // Получаем текущий статус из базы
        $firstOrder = DB::table('orders')
            ->where('userid', $userId)
            ->where('ordernum', $orderNum)
            ->first();
            
        if (!$firstOrder) {
            return redirect()->back()->with('error', 'Заказ не найден');
        }
        
        $currentStatus = $firstOrder->status;
        $newStatus = '';
        $newIsVerified = 0;
        
        // Определяем новый статус в зависимости от текущего
        if ($currentStatus === 'в обработке') {
            $newStatus = 'Принят в работу';
            $newIsVerified = 0;
        } elseif ($currentStatus === 'Принят в работу') {
            $newStatus = 'в обработке';
            $newIsVerified = 0;
        } else {
            return redirect()->back()->with('error', 'Невозможно изменить статус для заказа со статусом: ' . $currentStatus);
        }
        
        // Обновляем статус, is_verified и очищаем verified_at для всех позиций данного заказа
        DB::table('orders')
            ->where('userid', $userId)
            ->where('ordernum', $orderNum)
            ->update([
                'status' => $newStatus, 
                'is_verified' => $newIsVerified,
                'verified_at' => null,
                'updated_at' => now()
            ]);
            
        return redirect()->back()->with('success', "Статус заказа #{$orderNum} изменён на '{$newStatus}'");
    }

    public function toggleVerification($userId, $orderNum)
    {
        // Получаем текущий статус из базы
        $firstOrder = DB::table('orders')
            ->where('userid', $userId)
            ->where('ordernum', $orderNum)
            ->first();
            
        if (!$firstOrder) {
            return redirect()->back()->with('error', 'Заказ не найден');
        }
        
        $currentStatus = $firstOrder->status;
        $newStatus = '';
        $newIsVerified = 0;
        $verifiedAt = null;
        
        // Определяем новый статус в зависимости от текущего
        if ($currentStatus === 'Принят в работу') {
            $newStatus = 'Готов к выдаче';
            $newIsVerified = 1;
            $verifiedAt = now();
        } elseif ($currentStatus === 'Готов к выдаче') {
            $newStatus = 'Принят в работу';
            $newIsVerified = 0;
            $verifiedAt = null;
        } else {
            return redirect()->back()->with('error', 'Невозможно изменить верификацию для заказа со статусом: ' . $currentStatus);
        }
        
        // Обновляем статус, is_verified и verified_at для всех позиций данного заказа
        DB::table('orders')
            ->where('userid', $userId)
            ->where('ordernum', $orderNum)
            ->update([
                'status' => $newStatus, 
                'is_verified' => $newIsVerified,
                'verified_at' => $verifiedAt,
                'updated_at' => now()
            ]);
            
        $actionText = $newStatus === 'Готов к выдаче' ? 'верифицирован' : 'снята верификация';
        return redirect()->back()->with('success', "Заказ #{$orderNum} {$actionText}");
    }

    public function index(Request $request)
    {
        // Get all orders with their associated book and user details
        $cartItems = Order::with(['book', 'user'])->get();
       
        // Group by ordernum
        $groupedCartItems = $cartItems->groupBy('ordernum')->map(function ($orders, $orderNum) {
            $firstItem = $orders->first();
            return [
                'ordernum' => $orderNum,
                'user' => $firstItem->user, // Пользователь для этого заказа
                'userid' => $firstItem->userid, // ID пользователя
                'items' => $orders // Все элементы заказа
            ];
        });

        // Apply sorting based on request parameter
        $sort = $request->get('sort', 'desc'); // Default to descending
        
        if ($sort === 'desc') {
            $groupedCartItems = $groupedCartItems->sortByDesc('ordernum');
        } else {
            $groupedCartItems = $groupedCartItems->sortBy('ordernum');
        }
       
        return view('command/command', compact('groupedCartItems'));
    }
}