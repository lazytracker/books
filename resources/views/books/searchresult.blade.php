<x-app-layout>
    
<x-slot name="header">
    <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Учебники') }}
        </h2>
        <div class="flex items-center w-full sm:w-auto">
            <form action="{{ route('books.search') }}" method="GET" class="flex w-full sm:w-auto">
                <input type="text" name="query" placeholder="Искать..." class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full" />
                <button class="bg-blue-500 text-white rounded-lg p-2 ml-2 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50" style="width:45px;">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
    </x-slot>
    <div class="py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 w-full xl:max-w-screen-2xl">
            <!-- Панель с классами сверху -->
            <div class="mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    
                </div>
            </div>

            <div class="flex flex-col lg:flex-row">
                <!-- Боковое меню -->
                <div class="w-full lg:w-64 mb-6 lg:mb-0 lg:mr-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 text-gray-900">
                            <a href="{{ route('home') }}" class="inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition duration-300">
                                Вернуться на главную
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Таблица с книгами -->
                <div class="flex-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 text-gray-900">
                            @if($results->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3"></th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Год</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Кол-во</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Автор</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Класс</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">№</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($results as $book)
                                                <tr>
                                                    <td class="px-4 py-3 text-sm text-gray-500" style="min-width: 70px; width: 70px;">
                                                        @if(!is_null($book->url_id))
                                                            <div class="relative">
                                                                <!-- Картинка обложки -->
                                                                <a href="https://books.rusneb.ru/book/ru/nbr?book={{ $book->url_id }}" target="_blank">
                                                                <img src="{{ asset('images/thumbs/thumbs_' . $book->url_id . '.jpg') }}" 
                                                                    style="width:70px;" alt="обложка" 
                                                                    data-book-id="{{ $book->url_id }}" 
                                                                    onmouseover="showPreview({{ $book->url_id }})" onmouseleave="hidePreview({{ $book->url_id }})">

                                                                <!-- Контейнер для превью -->
                                                                <div id="preview-{{ $book->url_id }}" 
                                                                    class="absolute hidden z-10 bg-white shadow-lg p-2 rounded-lg">
                                                                    <img src="{{ asset('images/preview/preview_' . $book->url_id . '.jpg') }}" 
                                                                        style="max-width: 500px; max-height: 500px; object-fit: contain;" 
                                                                        alt="Превью">
                                                                </div>
                                                            </div>
                                                        @else
                                                            <img src="{{ asset('images/bookcover.png') }}" class="w-16 h-16 object-cover" alt="обложка">
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="text-sm font-medium text-gray-900">{{ $book->year }}</div>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm font-medium">
<form action="{{ route('cart.add') }}" method="POST" class="inline-flex items-center">
    @csrf
    <input type="hidden" name="book_id" value="{{ $book->id }}">
        <input 
        type="number" 
        name="quantity" 
        value="{{ isset($cartItems[$book->id]) && $cartItems[$book->id] > 0 ? $cartItems[$book->id] : 1 }}" 
        min="0" 
        class="quantity-input w-16 mr-2 border rounded px-2 py-1"
        data-product-id="{{ $book->id }}"
    >

<div id="cart-button-{{ $book->id }}">
    @if(isset($cartItems[$book->id]))
        <!-- Зеленая кнопка -->
        <button 
            type="button" 
            onclick="window.location.href='{{ route('cart.index') }}'" 
            class="text-white py-2 px-4 rounded flex flex-col items-center justify-center"
            style="min-width: 80px; min-height: 60px; background-color:#22c55e;"
        >
            <span class="font-bold select-none">В корзине</span>
            <span class="text-sm select-none">Перейти</span>
        </button>
    @else
        <!-- Синяя кнопка -->
        <button 
            type="submit" 
            class="text-white py-2 px-4 rounded flex flex-col items-center justify-center"
            style="min-width: 80px; min-height: 60px; background-color:#3b82f6;"
        >
            <span class="font-bold select-none">В корзину</span>
        </button>
    @endif
</div>
</form>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-900 break-words">
                                                        {{ $book->caption }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-900 break-words">
                                                        {{ $book->author }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-500 break-words">
                                                        {{ $book->class }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-500">
                                                        {{ $book->seqNum }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>Учебники не найдены</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    window.showPreview = function(id) {
        const preview = document.getElementById('preview-' + id);
        const image = document.querySelector('img[data-book-id="' + id + '"]'); // получаем картинку по id
        if (preview && image) {
            preview.classList.remove('hidden');
            preview.style.display = 'block';

            // Добавляем бледно-серую рамку
            preview.style.border = '1px solid #D1D5DB';  // Цвет бледно-серый (цвет из палитры Tailwind)

            document.addEventListener('mousemove', movePreview);
        }

        function movePreview(e) {
            const preview = document.getElementById('preview-' + id);
            const image = document.querySelector('img[data-book-id="' + id + '"]');
            if (!preview || !image) return;

            const imageRect = image.getBoundingClientRect();

            // Позиционируем по горизонтали относительно картинки
            const fixedLeft = imageRect.left + imageRect.width + 10; // 10px отступ справа от картинки
            let y = e.clientY + 20;

            const previewRect = preview.getBoundingClientRect();
            const windowHeight = window.innerHeight;

            // если выходит вниз — поднимаем вверх
            if (y + previewRect.height > windowHeight) {
                y = e.clientY - previewRect.height - 20;
            }

            preview.style.position = 'fixed';
            preview.style.left = fixedLeft + 'px';
            preview.style.top = y + 'px';
        }

        preview._moveHandler = movePreview;
    };

    window.hidePreview = function(id) {
        const preview = document.getElementById('preview-' + id);
        if (preview) {
            preview.classList.add('hidden');
            preview.style.display = 'none';

            if (preview._moveHandler) {
                document.removeEventListener('mousemove', preview._moveHandler);
                preview._moveHandler = null;
            }
        }
    };
});
</script>
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
    const formData = new FormData();
    formData.append('book_id', productId);
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'DELETE');

    fetch('{{ route('cart.remove') }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Удаляем строку (если есть таблица корзины)
        const row = document.getElementById('cart-item-' + productId);
        if (row) row.remove();

        // Проверка: если на главной, меняем кнопку обратно на синюю
        const cartButtonContainer = document.getElementById('cart-button-' + productId);
        if (cartButtonContainer) {
            cartButtonContainer.innerHTML = `
                <button 
                    type="submit" 
                    class="text-white py-2 px-4 rounded flex flex-col items-center justify-center"
                    style="min-width: 80px; min-height: 60px; background-color:#3b82f6;"
                >
                    <span class="font-bold select-none">В корзину</span>
                </button>
            `;
        }

        // Если корзина пуста — обновляем страницу
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