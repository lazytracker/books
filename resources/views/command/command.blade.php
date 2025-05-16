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
                <h3 class="font-semibold">Заказ № {{ $orderNum }}</h3>
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
                <div class="mt-4 mb-4 flex items-center gap-4">
                    <a href="{{ route('order.download', [$userId, $orderNum]) }}" class="m-3 bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow hover:bg-blue-700">
                        Скачать
                    </a>

                    <form method="POST" action="{{ route('admin.order.toggleVerification', [$userId, $orderNum]) }}">
                        @csrf
<form method="POST" action="{{ route('admin.order.toggleVerification', [$userId, $orderNum]) }}">
    @csrf
    <button type="submit"
        style="
            margin: 12px;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            color: white;
            background-color: {{ $cartItems->first()->is_verified ? '#dc2626' /* red-600 */ : '#16a34a' /* green-600 */ }};
            border: none;
            cursor: pointer;
        "
        onmouseover="this.style.backgroundColor='{{ $cartItems->first()->is_verified ? '#b91c1c' /* red-700 */ : '#15803d' /* green-700 */ }}'"
        onmouseout="this.style.backgroundColor='{{ $cartItems->first()->is_verified ? '#dc2626' : '#16a34a' }}'">
        {{ $cartItems->first()->is_verified ? 'Снять верификацию' : 'Верифицировать' }}
    </button>
</form>

                    </form>
                </div>
            @endforeach
        </div>
    @endforeach
@endif

        </div>
    </div>
</x-app-layout>