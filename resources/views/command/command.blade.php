{{-- resources/views/command.blade.php --}}

<x-app-layout>
<x-slot name="header">
    <div class="flex flex-col">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Заказы') }}
        </h2>
        <input
            type="text"
            placeholder="Данные заказчика, дата, номер заказа..."
            class="mt-2 max-w-md rounded-md border border-gray-300 shadow-sm
                   focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
        >
    </div>
</x-slot>

<div class="py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">

        {{-- Верхняя панель с сортировкой и фильтрами --}}
        <div class="flex justify-between items-center mb-4">
            {{-- Левая часть: сортировка --}}
            <div class="flex items-center space-x-2">
                <label for="sort" class="font-semibold text-gray-700">Сортировка:</label>
                <select id="sort" name="sort" class="rounded border border-gray-300 px-2 py-1 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="handleSortChange()">
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>По номеру заказа (убывание)</option>    
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>По номеру заказа (возрастание)</option>
                </select>
            </div>

            {{-- Правая часть: фильтры в два столбца --}}
            <div class="flex space-x-8">
                {{-- Первый столбец фильтров --}}
                <div>
                    <div class="font-semibold text-gray-700 mb-2">Заказы</div>
                    <form>
                        <div class="flex flex-col space-y-3">
                            <label class="inline-flex items-center">
                                <input type="radio" name="filter1" class="form-radio" value="option1" />
                                <span class="ml-2">Все</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="filter1" class="form-radio" value="option2" />
                                <span class="ml-2">Выполненные</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="filter1" class="form-radio" value="option3" />
                                <span class="ml-2">Не выполненные</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="filter1" class="form-radio" value="option4" />
                                <span class="ml-2">Отклонённые</span>
                            </label>
                        </div>
                    </form>
                </div>

                {{-- Второй столбец фильтров --}}
                <div>
                    <div class="font-semibold text-gray-700 mb-2">Клиенты</div>
                    <form>
                        <div class="flex flex-col space-y-3">
                            <label class="inline-flex items-center">
                                <input type="radio" name="filter2" class="form-radio" value="optionA" />
                                <span class="ml-2">Активные клиенты</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="filter2" class="form-radio" value="optionB" />
                                <span class="ml-2">Все</span>
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-center mb-6">Все заказы</h1>

            @if($groupedCartItems->isEmpty())
                <p class="text-center">No orders found.</p>
            @else
                <div class="flex flex-col items-center">
                    @foreach($groupedCartItems as $orderData)
                        <div class="customCard w-full max-w-4xl m-3 p-4 border rounded shadow">
                            @php
                                $cartItems = $orderData['items'];
                                $firstItem = $cartItems->first();
                                $status = $firstItem->status ?? 'нет статуса';
                                $isCancelled = mb_strtolower($status) === 'отменён';
                                $createdAt = $firstItem->created_at ? $firstItem->created_at->format('d.m.Y H:i') : 'нет даты';
                                
                                // Определяем состояния кнопок на основе статуса
                                $isInProcessing = mb_strtolower($status) === 'в обработке';
                                $isInWork = mb_strtolower($status) === 'принят в работу';
                                $isReady = mb_strtolower($status) === 'готов к выдаче';
                            @endphp
                            
                            <h3 class="font-semibold flex justify-between items-center">
                                <span>
                                    Заказ № {{ $orderData['ordernum'] }} от <span class="mx-2">{{ $createdAt }}</span>
                                    <span class="ml-4 text-blue-600">Пользователь: {{ $orderData['user']->name }} (ID: {{ $orderData['userid'] }})</span>
                                </span>
                                <span class="w-78 text-right whitespace-nowrap">Статус: {{ $status }}</span>
                            </h3>                            
                            
                            <style>
                                .approved-row {
                                    background-color:rgb(44, 235, 136); /* светло-зелёный фон */
                                }
                            </style>

                            <table class="w-full mt-4">
                                <thead>
                                    <tr>
                                        <th class="text-left">Учебник</th>
                                        <th class="text-left">Количество</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $cartItem)
                                        @php
                                            $isApproved = $cartItem->book->approved == 1;
                                        @endphp
                                        <tr class="{{ $isApproved ? 'approved-row' : '' }}">
                                            <td>{{ $cartItem->book->caption }}</td>
                                            <td>{{ $cartItem->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            <div class="mt-4 mb-4 flex items-center gap-4 justify-between order-block" data-cancelled="{{ $isCancelled ? '1' : '0' }}">
                                <div class="flex items-center gap-4 order-action-group">
                                    <a href="{{ route('order.download', [$orderData['userid'], $orderData['ordernum']]) }}"
                                    class="m-3 bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow hover:bg-blue-700">
                                        Скачать
                                    </a>
                                    <a href="{{ url('/download-csv?ordernum=' . $orderData['ordernum']) }}"
                                    class="m-3 bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow hover:bg-blue-700">
                                        Скачать CSV
                                    </a>

                                    {{-- Кнопка "Принять в работу" / "Убрать из работы" / "Заказ готов" --}}
                                    @if ($isReady)
                                        <div class="m-3 px-4 py-2 font-semibold rounded shadow bg-green-500 text-white w-[190px] text-center select-none cursor-not-allowed opacity-70">
                                            Заказ готов
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('admin.order.toggleStatus', [$orderData['userid'], $orderData['ordernum']]) }}">
                                            @csrf
                                            <button type="submit"
                                                class="px-4 py-2 font-semibold rounded shadow text-white w-[190px] hover:opacity-90 transition-opacity"
                                                style="background-color: {{ $isInWork ? '#dc2626' : '#2563eb' }}"
                                            >
                                                {{ $isInWork ? 'Убрать из работы' : 'Принять в работу' }}
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Кнопка "Верифицировать" / "Снять верификацию" --}}
                                    @if ($isInProcessing)
                                        <div class="px-4 py-2 font-semibold rounded shadow bg-gray-400 text-white w-[190px] text-center cursor-not-allowed opacity-70">
                                            Верифицировать
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('admin.order.toggleVerification', [$orderData['userid'], $orderData['ordernum']]) }}">
                                            @csrf
                                            <button type="submit"
                                                class="px-4 py-2 font-semibold rounded shadow text-white w-[190px] hover:opacity-90 transition-opacity"
                                                style="background-color: {{ $isReady ? '#dc2626' : '#16a34a' }}"
                                            >
                                                {{ $isReady ? 'Снять верификацию' : 'Верифицировать' }}
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <button
                                        type="button"
                                        class="m-3 bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow hover:bg-blue-700"
                                    >
                                        Передать в ИРБИС
                                    </button>
                                </div>

                                {{-- Кнопка "Отменить заказ" справа --}}
                                <form method="POST" action="{{ route('admin.order.cancel', [$orderData['userid'], $orderData['ordernum']]) }}" class="cancel-form">
                                    @csrf
                                    <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow cancel-btn"
                                        onclick="disableOrderActions(event, this)"
                                    >
                                        Отменить заказ
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.order-block').forEach(block => {
                if (block.dataset.cancelled === '1') {
                    disableButtonsInBlock(block);
                }
            });
        });

        function disableButtonsInBlock(orderBlock) {
            const buttons = orderBlock.querySelectorAll('button, a');
            buttons.forEach(btn => {
                btn.disabled = true;
                btn.style.pointerEvents = 'none';
                btn.style.opacity = '0.5';
            });
        }

        function disableOrderActions(event, button) {
            event.preventDefault(); // чтобы форма не отправлялась сразу

            // Находим родительский блок заказа (order-block)
            const orderBlock = button.closest('.order-block');

            // Отключаем кнопки в блоке
            disableButtonsInBlock(orderBlock);

            // Отправляем форму вручную через JS (после отключения кнопок)
            button.closest('form').submit();
        }

        function handleSortChange() {
            const sortSelect = document.getElementById('sort');
            const currentUrl = new URL(window.location.href);
            
            // Обновляем параметр sort в URL
            currentUrl.searchParams.set('sort', sortSelect.value);
            
            // Перенаправляем на новый URL
            window.location.href = currentUrl.toString();
        }
    </script>
</x-app-layout>