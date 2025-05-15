<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Корзина') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 w-full xl:max-w-screen-2xl">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">
                    @if($cartItems->count() > 0)
                        <!-- Обертка для горизонтальной прокрутки -->
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Автор</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Год</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Количество</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                    </tr>
                                </thead>
                               <tbody class="bg-white divide-y divide-gray-200">
    @foreach($cartItems as $item)
        <tr id="cart-item-{{ $item->product_id }}">
            <td class="px-4 py-3">
                <div class="text-sm font-medium text-gray-900 break-words">
                    {{ $item->book->caption }}
                </div>
            </td>
            <td class="px-4 py-3">
                <div class="text-sm text-gray-900 break-words">
                    {{ $item->book->author }}
                </div>
            </td>
            <td class="px-4 py-3">
                <div class="text-sm text-gray-900">
                    {{ $item->book->year }}
                </div>
            </td>
            <td class="px-4 py-3 text-sm text-gray-500">
                <input
                    style="width: 8ch;" 
                    type="number" 
                    name="quantity" 
                    value="{{ $item->quantity }}"
                    class="quantity-input border border-gray-400 rounded mx-1 w-16 text-center"
                    data-product-id="{{ $item->product_id }}" 
                    min="0"
                >
            </td>
            <td class="px-4 py-3 text-sm font-medium">
                <button 
                    type="button" 
                    class="remove-item text-red-600 hover:text-red-900" 
                    data-product-id="{{ $item->product_id }}"
                >
                    Удалить
                </button>
            </td>
        </tr>
    @endforeach
</tbody>
                            </table>
                        </div>
                        <!-- Кнопка для оформления заказа -->
                        <form action="{{ route('cart.order') }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Оформить заказ</button>
                        </form>
                    @else
                        <p>Корзина пуста</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработка изменения количества
    const quantityInputs = document.querySelectorAll('.quantity-input');
    
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantity = parseInt(this.value);
            
            if (quantity <= 0) {
                // Если количество 0 или меньше, удаляем товар
                removeCartItem(productId);
            } else {
                // Иначе обновляем количество
                updateCartItemQuantity(productId, quantity);
            }
        });
    });
    
    // Обработка кнопки удаления
    const removeButtons = document.querySelectorAll('.remove-item');
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            removeCartItem(productId);
        });
    });
    
    // Функция для обновления количества товара
    function updateCartItemQuantity(productId, quantity) {
        // Создаем форму для отправки
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        formData.append('_token', '{{ csrf_token() }}');
        
        // Отправляем AJAX запрос
        fetch('{{ route('cart.updateQuantity') }}', {
            method: 'POST',
            body: formData
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Функция для удаления товара
    function removeCartItem(productId) {
        // Создаем форму для отправки
        const formData = new FormData();
        formData.append('book_id', productId);
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');
        
        // Отправляем AJAX запрос
        fetch('{{ route('cart.remove') }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Удаляем строку из таблицы
            const row = document.getElementById('cart-item-' + productId);
            row.remove();
            
            // Если корзина пуста, можно перезагрузить страницу или показать сообщение
            if (document.querySelectorAll('tbody tr').length === 0) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при удалении товара');
        });
    }
});
</script>
</x-app-layout> 