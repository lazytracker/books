<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Категории') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @foreach($categories as $category)
                        <div class="mb-4 p-4 border rounded">
                            <h3 class="text-lg font-bold">
                                <a href="{{ route('categories.show', $category) }}">
                                    {{ $category->name }}
                                </a>
                            </h3>
                            <p>{{ $category->description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 