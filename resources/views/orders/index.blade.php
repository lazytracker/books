<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Заказы') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($orderItems->count() > 0)
                    <h2>Заказ № {{$orderItems[0]->ordernum}} от {{$orderItems[0]->created_at}}</h2>
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Год</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Автор</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Количество</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($orderItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->book->caption }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->year }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->book->author }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">                                        
                                            {{ $item->quantity }}                                                                                    
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {{ $item->status }}
                                        
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
						 <!-- Кнопка для загрузки текстового файла -->
                    <form action="{{ route('cart.download') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Скачать marc-записи</button>
                    </form>
                    @else
                        <p>У вас пока нет заказов</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 