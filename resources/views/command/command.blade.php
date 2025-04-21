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
                        <h2 class="font-bold">{{ $group['user']->name }}</h2> <!-- Display the user's name -->
                        
                        @foreach($group['orders'] as $orderNum => $cartItems)
                            <h3 class="font-semibold">Заказ № {{ $orderNum }}</h3> <!-- Display the order number -->
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
                            <div class="mt-4 mb-4">
                            <a href="{{ route('order.download', [$userId, $orderNum]) }}" class="m-3 bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow hover:bg-blue-700">
                                Скачать
                            </a>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>