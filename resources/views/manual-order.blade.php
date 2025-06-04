<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Загрузка заказов</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Custom scrollbar styles */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8" x-data="manualOrder()">
        
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Загрузка заказов учебников</h1>
            
            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-4 mb-4">
                <!-- Upload Button -->
                <div class="relative">
                    <input type="file" 
                           id="excel_file" 
                           accept=".xlsx,.xls" 
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                           @change="handleFileSelect($event)">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Загрузить файл Excel
                    </button>
                </div>
                
                <!-- Clear Button -->
                <button @click="clearData()" 
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2"
                        :disabled="loading">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Очистить данные
                </button>
            </div>
            
            <!-- File info -->
            <div x-show="selectedFile" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-blue-800">
                    Выбран файл: <span x-text="selectedFile?.name" class="font-medium"></span>
                    (<span x-text="formatFileSize(selectedFile?.size)"></span>)
                </p>
                <button @click="uploadFile()" 
                        class="mt-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-medium"
                        :disabled="loading">
                    <span x-show="!loading">Загрузить</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Загружается...
                    </span>
                </button>
            </div>
            
            <!-- Messages -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Data Table -->
        @if($orders->count() > 0)
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">
                    Загруженные данные ({{ $orders->count() }} записей)
                </h2>
            </div>
            
<!-- Замените существующую таблицу на эту -->
<div class="overflow-auto custom-scrollbar" style="max-height: 70vh;">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 sticky top-0">
            <tr>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 100px;">
                    Артикул
                </th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 120px;">
                    Код ФП
                </th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 200px;">
                    Автор
                </th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 300px;">
                    Наименование
                </th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 80px;">
                    Год
                </th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 80px;">
                    Кол-во
                </th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 100px;">
                    Цена, руб.
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($orders as $order)
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="max-w-[100px] truncate" title="{{ $order->ART }}">
                        {{ $order->ART }}
                    </div>
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="max-w-[120px] truncate" title="{{ $order->seqNum }}">
                        {{ $order->seqNum }}
                    </div>
                </td>
                <td class="px-3 py-4 text-sm text-gray-900">
                    <div class="max-w-[200px] line-clamp-2" title="{{ $order->author }}">
                        {{ $order->author }}
                    </div>
                </td>
                <td class="px-3 py-4 text-sm text-gray-900">
                    <div class="max-w-[300px] line-clamp-3" title="{{ $order->caption }}">
                        {{ $order->caption }}
                    </div>
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $order->year }}
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                    {{ $order->quantity }}
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                    {{ number_format($order->price, 2, ',', ' ') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
        </div>
        @else
        <div class="bg-white shadow-sm rounded-lg p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Нет загруженных данных</h3>
            <p class="text-gray-500">Загрузите Excel файл для отображения данных заказа</p>
        </div>
        @endif
    </div>

    <!-- Hidden forms for AJAX requests -->
    <form id="upload-form" method="POST" action="{{ route('manual-order.upload') }}" enctype="multipart/form-data" style="display: none;">
        @csrf
        <input type="file" name="excel_file" id="hidden-file-input">
    </form>

    <form id="clear-form" method="POST" action="{{ route('manual-order.clear') }}" style="display: none;">
        @csrf
    </form>

    <script>
        function manualOrder() {
            return {
                selectedFile: null,
                loading: false,

                handleFileSelect(event) {
                    this.selectedFile = event.target.files[0];
                },

                formatFileSize(bytes) {
                    if (!bytes) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },

                async uploadFile() {
                    if (!this.selectedFile) return;

                    this.loading = true;
                    
                    const formData = new FormData();
                    formData.append('excel_file', this.selectedFile);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                    try {
                        const response = await fetch('{{ route("manual-order.upload") }}', {
                            method: 'POST',
                            body: formData,
                        });

                        if (response.ok) {
                            window.location.reload();
                        } else {
                            throw new Error('Upload failed');
                        }
                    } catch (error) {
                        alert('Ошибка при загрузке файла');
                        console.error('Upload error:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async clearData() {
                    if (!confirm('Вы уверены, что хотите очистить все данные?')) {
                        return;
                    }

                    this.loading = true;

                    try {
                        const response = await fetch('{{ route("manual-order.clear") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            },
                        });

                        if (response.ok) {
                            window.location.reload();
                        } else {
                            throw new Error('Clear failed');
                        }
                    } catch (error) {
                        alert('Ошибка при очистке данных');
                        console.error('Clear error:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
    <script>
        function manualOrder() {
    return {
        selectedFile: null,
        loading: false,

        handleFileSelect(event) {
            console.log('=== FILE SELECT EVENT ===');
            this.selectedFile = event.target.files[0];
            console.log('Selected file:', this.selectedFile);
            console.log('File name:', this.selectedFile?.name);
            console.log('File size:', this.selectedFile?.size);
            console.log('File type:', this.selectedFile?.type);
        },

        formatFileSize(bytes) {
            if (!bytes) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        async uploadFile() {
            console.log('=== UPLOAD FILE START ===');
            if (!this.selectedFile) {
                console.error('No file selected');
                return;
            }

            console.log('Starting upload for file:', this.selectedFile.name);
            this.loading = true;
            
            const formData = new FormData();
            formData.append('excel_file', this.selectedFile);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            console.log('FormData created');
            console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            console.log('Upload URL:', '{{ route("manual-order.upload") }}');

            try {
                console.log('Sending fetch request...');
                const response = await fetch('{{ route("manual-order.upload") }}', {
                    method: 'POST',
                    body: formData,
                });

                console.log('Response received:', response);
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                console.log('Response headers:', response.headers);

                if (response.ok) {
                    console.log('Upload successful, reloading page...');
                    window.location.reload();
                } else {
                    console.error('Upload failed with status:', response.status);
                    const responseText = await response.text();
                    console.error('Response text:', responseText);
                    throw new Error(`Upload failed with status ${response.status}`);
                }
            } catch (error) {
                console.error('=== UPLOAD ERROR ===');
                console.error('Error details:', error);
                console.error('Error message:', error.message);
                console.error('Error stack:', error.stack);
                alert('Ошибка при загрузке файла: ' + error.message);
            } finally {
                console.log('Upload process finished');
                this.loading = false;
            }
        },

        async clearData() {
            console.log('=== CLEAR DATA START ===');
            if (!confirm('Вы уверены, что хотите очистить все данные?')) {
                console.log('Clear cancelled by user');
                return;
            }

            console.log('Starting clear operation...');
            this.loading = true;

            try {
                console.log('Clear URL:', '{{ route("manual-order.clear") }}');
                const response = await fetch('{{ route("manual-order.clear") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                });

                console.log('Clear response:', response);
                console.log('Clear response status:', response.status);

                if (response.ok) {
                    console.log('Clear successful, reloading page...');
                    window.location.reload();
                } else {
                    console.error('Clear failed with status:', response.status);
                    throw new Error(`Clear failed with status ${response.status}`);
                }
            } catch (error) {
                console.error('=== CLEAR ERROR ===');
                console.error('Clear error details:', error);
                alert('Ошибка при очистке данных: ' + error.message);
            } finally {
                console.log('Clear process finished');
                this.loading = false;
            }
        }
    }
}
    </script>
    
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</body>
</html>