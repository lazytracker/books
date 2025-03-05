{{-- resources/views/command.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Command Page') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <h1>All Cart Items</h1>

        @if($groupedCartItems->isEmpty())
            <p>No orders found.</p>
        @else
            @foreach($groupedCartItems as $userId => $cartItems)
                <h2>{{ $cartItems->first()->user->name }}</h2> <!-- Display the user's name -->
                <table>
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Quantity</th>
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
            @endforeach
        @endif
    </div>
</x-app-layout>