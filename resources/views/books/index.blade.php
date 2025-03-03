<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Учебники') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Панель с классами сверху -->
            <div class="mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="font-semibold text-lg mb-4">Классы</h3>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('books.index', array_merge(request()->except('class'), ['page' => 1])) }}" 
                                class="px-4 py-2 rounded @if(!request('class')) bg-blue-600 text-white @else bg-gray-200 text-gray-700 @endif hover:bg-blue-700 hover:text-white">
                                Все классы
                            </a>
                            @foreach($classesWithCount as $classInfo)
                                <a href="{{ route('books.index', array_merge(request()->except('page'), ['class' => $classInfo->class, 'page' => 1])) }}" 
                                    class="px-4 py-2 rounded @if(request('class') == $classInfo->class) bg-blue-600 text-white 
                                    @elseif($classInfo->book_count > 0) bg-gray-200 text-gray-700 
                                    @else bg-gray-100 text-gray-400 cursor-not-allowed @endif 
                                    @if($classInfo->book_count > 0) hover:bg-blue-700 hover:text-white @endif">
                                    {{ $classInfo->class }} класс
                                    <span class="text-sm ml-1">({{ $classInfo->book_count }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex">
                <!-- Боковое меню -->
                <div class="w-64 mr-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="font-semibold text-lg mb-4">Предметы</h3>
                            <ul class="space-y-2">
                                <li>
                                    <a href="{{ route('books.index') }}" 
                                        class="@if(!request('subject_id')) font-bold text-blue-600 @else text-gray-600 @endif hover:text-blue-800">
                                        Все предметы
                                    </a>
                                </li>
                                @foreach($subjects as $subject)
                                    <li>
                                        <a href="{{ route('books.index', ['subject_id' => $subject->subj_hex]) }}" 
                                            class="@if(request('subject_id') === $subject->subj_hex) font-bold text-blue-600 @else text-gray-600 @endif hover:text-blue-800">
                                            {{ $subject->subj }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Таблица с книгами -->
                <div class="flex-1 overflow-x-auto">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            @if($books->count() > 0)
                                <table class="w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Кол-во</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Автор</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Класс</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">№</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($books as $book)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <form action="{{ route('cart.add') }}" method="POST" class="inline-flex items-center">
                                                        @csrf
                                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                                        <input type="number" name="quantity" value="1" min="1" 
                                                            class="w-16 mr-2 border rounded px-2 py-1">
                                                        <button type="submit" 
                                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                                                            В корзину
                                                        </button>
                                                    </form>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $book->caption }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $book->author }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $book->class }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $book->seqNum }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="mt-4">
                                    {{ $books->links() }}
                                </div>
                            @else
                                <p>Учебники не найдены</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 