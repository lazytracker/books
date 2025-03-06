{{-- resources/views/command.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Заказы') }}
        </h2>
    </x-slot>

    <div class="py-12">
	        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 customMargin">
        <h1 class="text-2xl font-bold">Все заказы</h1>

        @if($groupedCartItems->isEmpty())
            <p>No orders found.</p>
        @else
            @foreach($groupedCartItems as $userId => $cartItems)
				<div class="customCard">
                <h2>{{ $cartItems->first()->user->name }}</h2> <!-- Display the user's name -->
                <table>
                    <thead>
                        <tr>
                            <th>Учебник</th>
                            <th>Количество</th>
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
				</div>
            @endforeach
        @endif
    </div>
	</div>
</x-app-layout>