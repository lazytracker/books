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
                <select id="sort" name="sort" class="rounded border border-gray-300 px-2 py-1 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="newest">Сначала новые</option>
                    <option value="oldest">Сначала старые</option>
                    <option value="asc">По возрастанию даты</option>
                    <option value="desc">По убыванию даты</option>
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
                    $isVerified = $firstItem->is_verified;
                    $canVerify = mb_strtolower($status) === 'принят в работу';
                    $createdAt = $firstItem->created_at ? $firstItem->created_at->format('d.m.Y H:i') : 'нет даты';
                @endphp
                
                <h3 class="font-semibold flex justify-between items-center">
                    <span>
                        Заказ № {{ $orderData['ordernum'] }} от <span class="mx-2">{{ $createdAt }}</span>
                        <span class="ml-4 text-blue-600">Пользователь: {{ $orderData['user']->name }} (ID: {{ $orderData['userid'] }})</span>
                    </span>
                    <span class="w-78 text-right whitespace-nowrap">Статус: {{ $status }}</span>
                </h3>
                
                <table class="w-full mt-4">
                    <thead>
                        <tr>
                            <th class="text-left">Учебник</th>
                            <th class="text-left">Количество</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $cartItem)
                            <tr>
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

                        @if ($cartItems->first()->is_verified)
                            <div class="m-3 px-4 py-2 font-semibold rounded shadow text-green-600 w-[190px] text-center select-none">
                                Заказ готов
                            </div>
                        @else
                            <form method="POST" action="{{ route('admin.order.toggleStatus', [$orderData['userid'], $orderData['ordernum']]) }}">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2 font-semibold rounded shadow text-white w-[190px]"
                                    style="background-color: {{ $cartItems->first()->status === 'Принят в работу' ? '#dc2626' : '#2563eb' }}"
                                    onmouseover="this.style.backgroundColor='{{ $cartItems->first()->status === 'Принят в работу' ? '#b91c1c' : '#1d4ed8' }}'"
                                    onmouseout="this.style.backgroundColor='{{ $cartItems->first()->status === 'Принят в работу' ? '#dc2626' : '#2563eb' }}'"
                                >
                                    {{ $cartItems->first()->status === 'Принят в работу' ? 'Убрать из работы' : 'Принять в работу' }}
                                </button>
                            </form>
                        @endif

                        @php
                            $firstItem = $cartItems->first();
                            $isVerified = $firstItem->is_verified;
                            $status = mb_strtolower($firstItem->status);
                            $canVerify = $status === 'принят в работу';
                        @endphp

                        <form method="POST" action="{{ route('admin.order.toggleVerification', [$orderData['userid'], $orderData['ordernum']]) }}">
                            @csrf
                            <button type="submit"
                                {{ (!$isVerified && !$canVerify) ? 'disabled' : '' }}
                                class="px-4 py-2 font-semibold rounded shadow text-white w-[190px]"
                                style="
                                    cursor: {{ (!$isVerified && !$canVerify) ? 'not-allowed' : 'pointer' }};
                                    background-color: {{
                                        $isVerified
                                            ? '#dc2626'
                                            : ($canVerify ? '#16a34a' : '#9ca3af')
                                    }};
                                "
                                onmouseover="
                                    if (!this.disabled) {
                                        this.style.backgroundColor = '{{ $isVerified ? '#b91c1c' : '#15803d' }}';
                                    }
                                "
                                onmouseout="
                                    this.style.backgroundColor = '{{ $isVerified ? '#dc2626' : ($canVerify ? '#16a34a' : '#9ca3af') }}';
                                "
                                title="{{ (!$isVerified && !$canVerify) ? 'Можно верифицировать только после принятия в работу' : '' }}"
                            >
                                {{ $isVerified ? 'Снять верификацию' : 'Верифицировать' }}
                            </button>
                        </form>
                        
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

    </script>
</x-app-layout>
