<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Регистрация прошла успешно') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p>Вы зашли в систему и теперь можете набрать записей для импорта в БИС ИРБИС из
					<b style="color:blue;" ><a href="/" class="hover:underline">каталога учебников</a></b>
					</p> 
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
