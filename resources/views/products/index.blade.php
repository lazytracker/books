<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Товары') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @foreach($products as $product)
                        <div class="mb-4 p-4 border rounded">
                            <h3 class="text-lg font-bold">{{ $product->name }}</h3>
                            <p>{{ $product->description }}</p>
                            <p class="font-bold">Цена: {{ $product->price }} руб.</p>
                            
                            <form action="{{ route('cart.add') }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="number" name="quantity" value="1" min="1" class="border rounded px-2 py-1">
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                                    В корзину
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 