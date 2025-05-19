{{-- resources/views/command.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Заказы') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container max-w-full mx-auto px-4 sm:px-6 lg:px-8 customMargin">
            <h1 class="text-2xl font-bold ml-[20%]">Все заказы</h1>

            @if($groupedCartItems->isEmpty())
                <p>No orders found.</p>
            @else
                @foreach($groupedCartItems as $userId => $group)
                    <div class="customCard w-1/2 m-3 p-4 border rounded shadow">
                        <h2 class="font-bold">{{ $group['user']->name }}</h2>

                        @foreach($group['orders'] as $orderNum => $cartItems)
@php
    $firstItem = $cartItems->first();
    $status = $firstItem->status ?? 'нет статуса';
    $isCancelled = mb_strtolower($status) === 'отменён';
    $isVerified = $firstItem->is_verified;
    $canVerify = mb_strtolower($status) === 'принят в работу';
    $createdAt = $firstItem->created_at ? $firstItem->created_at->format('d.m.Y H:i') : 'нет даты';
@endphp
                            <h3 class="font-semibold flex justify-between items-center">
                                <span>Заказ № {{ $orderNum }} от <span class="mx-2">{{ $createdAt }}</span></span>
                                <span class="w-78 text-right whitespace-nowrap">Статус: {{ $status }}</span>
                            </h3>
                            <table class="w-full">
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
                                    <a href="{{ route('order.download', [$userId, $orderNum]) }}"
                                       class="m-3 bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow hover:bg-blue-700">
                                        Скачать
                                    </a>
                                    <a href="{{ url('/download-csv?ordernum=' . $orderNum) }}"
                                       class="m-3 bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow hover:bg-blue-700">
                                        Скачать CSV
                                    </a>

                                    @if ($cartItems->first()->is_verified)
                                        <div class="m-3 px-4 py-2 font-semibold rounded shadow text-green-600 w-[190px] text-center select-none">
                                            Заказ готов
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('admin.order.toggleStatus', [$userId, $orderNum]) }}">
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

                                    <form method="POST" action="{{ route('admin.order.toggleVerification', [$userId, $orderNum]) }}">
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
                                </div>

                                {{-- Кнопка "Отменить заказ" справа --}}
                                <form method="POST" action="{{ route('admin.order.cancel', [$userId, $orderNum]) }}" class="cancel-form">
                                    @csrf
                                    <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow cancel-btn"
                                        onclick="disableOrderActions(event, this)"
                                    >
                                        Отменить заказ
                                    </button>
                                </form>
                            </div>

                        @endforeach
                    </div>
                @endforeach
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
