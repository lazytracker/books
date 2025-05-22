<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('О сайте') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900">Добро пожаловать!</h3>
                <p class="mt-2 text-gray-600">Это простая страница "О сайте". Здесь вы можете рассказать пользователям, кто вы, чем занимаетесь и т.д.</p>
            </div>
        </div>
    </div>
</x-app-layout>
