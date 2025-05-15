<?php

namespace App\Http\Controllers;


use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;

class CartController extends Controller
{   
public function updateQuantity(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:books,id',
        'quantity' => 'required|integer|min:1',
    ]);

    // Найти товар в корзине и обновить его количество
    $cartItem = CartItem::where('user_id', auth()->id())
        ->where('product_id', $validated['product_id'])
        ->first();

    if ($cartItem) {
        $cartItem->quantity = $validated['quantity'];
        $cartItem->save();
    }

    // Возвращаем JSON-ответ для AJAX запроса
    return response()->json(['success' => true, 'message' => 'Количество товара обновлено']);
}
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cartItems = CartItem::with('book')
            ->where('user_id', auth()->id())
            ->get();

        return view('cart.index', compact('cartItems'));
    }

public function add(Request $request)
{
    $validated = $request->validate([
        'book_id' => 'required|exists:books,id',
        'quantity' => 'required|integer|min:1',
    ]);

    // Проверяем, есть ли уже этот товар в корзине
    $cartItem = CartItem::where('user_id', auth()->id())
        ->where('product_id', $validated['book_id'])
        ->first();

    if ($cartItem) {
        // Если товар уже есть в корзине, увеличиваем количество
        $cartItem->quantity += $validated['quantity'];
        $cartItem->save();
    } else {
        // Если товара нет в корзине, создаем новую запись
        CartItem::create([
            'user_id' => auth()->id(),
            'product_id' => $validated['book_id'],
            'quantity' => $validated['quantity']
        ]);
    }

    return redirect()->back()->with('success', 'Книга добавлена в корзину');
}


public function remove(Request $request)
{
    CartItem::where('user_id', auth()->id())
        ->where('product_id', $request->book_id)
        ->delete();

    // Возвращаем JSON-ответ для AJAX запроса
    return response()->json(['success' => true, 'message' => 'Книга удалена из корзины']);
}
	
    public function order(Request $request){
        //поместим заказ из корзины в заказы
        // Получите элементы корзины для текущего пользователя
		$cartItems = CartItem::with('book')
        ->where('user_id', auth()->id())
        ->get();

        $orderNum = $cartItems[0]->id;
        foreach ($cartItems as $cartItem) {
            // Create a new order
            Order::create([
                'userid' => auth()->id(), // Assuming you want to associate the order with the current user
                'productid' => $cartItem->book->id, // Assuming 'book' is the relationship and you want to use the book's ID
                'ordernum' => $orderNum,
                'quantity' => $cartItem->quantity, // Assuming you have a quantity field in the cart item
               
                'status' => 'в обработке', // Set the initial status of the order
            ]);
        }

        CartItem::where('user_id', auth()->id())->delete();//или можно менять их статус в корзине
        return redirect()->route('order.index')->with('success', 'Orders created successfully.');
    }

	public function download(Request $request)
	{
		// Получите элементы корзины для текущего пользователя
		$cartItems = CartItem::with('book')
			->where('user_id', auth()->id())
			->get();

		// Проверьте, есть ли элементы в корзине
		if ($cartItems->isEmpty()) {
			return redirect()->back()->with('error', 'Корзина пуста, нечего скачивать.');
		}

		// Создайте текстовый файл со списком покупок
		$content = "Список покупок:\n\n";
		foreach ($cartItems as $item) {
			$content .= "Название: " . $item->book->caption . "\n";
			$content .= "Автор: " . $item->book->author . "\n";
			$content .= "Количество: " . $item->quantity . "\n\n";
		}

		// Установите заголовки для скачивания файла
		$headers = [
			'Content-Type' => 'text/plain',
			'Content-Disposition' => 'attachment; filename="shopping_list.txt"',
		];

		// Верните файл как ответ
		return response()->make($content, 200, $headers);
	}
} 