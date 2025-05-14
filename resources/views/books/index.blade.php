<x-app-layout>
    <x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Учебники') }}
        </h2>
        <div class="flex items-center space-x-2">
        <form action="{{ route('books.search') }}" method="GET">
            <input type="text" name="query" placeholder="Искать..." class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            <button class="bg-blue-500 text-white rounded-lg p-2 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50" style="margin-left:10px; width:45px;">
                <i class="fas fa-search"></i>
            </button>
        </form>
        </div>
    </div>
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
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th style="min-width:110px;"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Год</th>
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
                                    <td class="px-6 py-4 text-sm text-gray-500" style="min-width: 70px; width: 70px; max-width: 70px;">
                                        @if(!is_null($book->url_id))
                                            <div class="relative">
                                                <a href="https://books.rusneb.ru/book/ru/nbr?book={{ $book->url_id }}" target="_blank">
                                                <!-- Картинка обложки -->
                                                <img src="{{ asset('images/thumbs/thumbs_' . $book->url_id . '.jpg') }}" 
                                                    style="width:70px;" alt="обложка" 
                                                    data-book-id="{{ $book->url_id }}" 
                                                    onmouseover="showPreview({{ $book->url_id }})" onmouseleave="hidePreview({{ $book->url_id }})">

                                                <!-- Контейнер для превью -->
                                                <div id="preview-{{ $book->url_id }}" 
                                                    class="absolute hidden z-10 bg-white shadow-lg p-2 rounded-lg">
                                                    <img src="{{ asset('images/preview/preview_' . $book->url_id . '.jpg') }}" 
                                                        style="max-width: 500px; max-height: 500px; object-fit: contain;" 
                                                        alt="Превью">
                                                </div>
                                            </div>
                                        @else
                                            <img src="{{ asset('images/bookcover.png') }}" class="w-16 h-16 object-cover" alt="обложка">
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $book->year }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <form action="{{ route('cart.add') }}" method="POST" class="inline-flex items-center">
                                            @csrf
                                            
                                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                                            <input type="number" name="quantity" value="1" min="1" 
                                                class="w-16 mr-2 border rounded px-2 py-1">
                                            <button type="submit" 
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                                                <!--В корзину-->
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" style="width:30px; height:30px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 break-words">
                                        {{ $book->caption }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 break-words">
                                        {{ $book->author }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 break-words">
                                        {{ $book->class }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $book->seqNum }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $books->links() }}
                </div>
            @else
                <p>Учебники не найдены</p>
            @endif
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    window.showPreview = function(id) {
        const preview = document.getElementById('preview-' + id);
        const image = document.querySelector('img[data-book-id="' + id + '"]'); // получаем картинку по id
        if (preview && image) {
            preview.classList.remove('hidden');
            preview.style.display = 'block';

            // Добавляем бледно-серую рамку
            preview.style.border = '1px solid #D1D5DB';  // Цвет бледно-серый (цвет из палитры Tailwind)

            document.addEventListener('mousemove', movePreview);
        }

        function movePreview(e) {
            const preview = document.getElementById('preview-' + id);
            const image = document.querySelector('img[data-book-id="' + id + '"]');
            if (!preview || !image) return;

            const imageRect = image.getBoundingClientRect();

            // Позиционируем по горизонтали относительно картинки
            const fixedLeft = imageRect.left + imageRect.width + 10; // 10px отступ справа от картинки
            let y = e.clientY + 20;

            const previewRect = preview.getBoundingClientRect();
            const windowHeight = window.innerHeight;

            // если выходит вниз — поднимаем вверх
            if (y + previewRect.height > windowHeight) {
                y = e.clientY - previewRect.height - 20;
            }

            preview.style.position = 'fixed';
            preview.style.left = fixedLeft + 'px';
            preview.style.top = y + 'px';
        }

        preview._moveHandler = movePreview;
    };

    window.hidePreview = function(id) {
        const preview = document.getElementById('preview-' + id);
        if (preview) {
            preview.classList.add('hidden');
            preview.style.display = 'none';

            if (preview._moveHandler) {
                document.removeEventListener('mousemove', preview._moveHandler);
                preview._moveHandler = null;
            }
        }
    };
});
</script>



</x-app-layout> 
