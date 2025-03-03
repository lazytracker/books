<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
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
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $validated['book_id'])
            ->first();

        if ($cartItem) {
            $cartItem->update(['quantity' => $validated['quantity']]);
        } else {
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

        return redirect()->back()->with('success', 'Книга удалена из корзины');
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