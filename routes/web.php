<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Главная страница теперь показывает список книг
Route::get('/', [BookController::class, 'index'])->name('home');

// Добавляем маршрут dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Маршруты для книг
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// Маршруты для корзины
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/order', [CartController::class, 'order'])->name('cart.order');
	Route::post('/cart/download', [CartController::class, 'download'])->name('cart.download');
	Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
});

// Маршруты для заказов
Route::middleware('auth')->group(function () {
    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::post('/order/add', [OrderController::class, 'add'])->name('order.add');
    
	Route::post('/order/download', [OrderController::class, 'download'])->name('order.download');
	Route::delete('/order/remove', [OrderController::class, 'remove'])->name('order.remove');
});

// Остальные маршруты для аутентификации
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	
});

/*
Route::middleware(['auth', 'checkAdmin'])->group(function () {
    Route::get('/command', [CommandController::class, 'index']);
});
*/
Route::middleware(['auth', 'checkAdmin'])->group(function () {
    Route::get('/command', [CommandController::class, 'index'])->name('command.index');
});

/*
// Временный маршрут для обновления subj_hex
Route::get('/update-subjects', function() {
    // Фиксированное соответствие предметов и их номеров
    $subjectMapping = [
        'Английский язык' => '00',
        'Астрономия' => '01',
        'Биология' => '02',
        'География' => '03',
        'ИЗО' => '04',
        'Информатика' => '05',
        'История' => '06',
        'Литература' => '07',
        'Математика' => '08',
        'Музыка' => '09',
        'Немецкий язык' => '10',
        'ОБЖ' => '11',
        'Обществознание' => '12',
        'Окружающий мир' => '13',
        'Русский язык' => '14',
        'Технология' => '15',
        'Физика' => '16',
        'Физкультура' => '17',
        'Французский язык' => '18',
        'Химия' => '19'
    ];

    foreach ($subjectMapping as $subject => $id) {
        DB::table('books')
            ->where('subj', $subject)
            ->update(['subj_hex' => $id]);
    }

    return $subjectMapping; // Вернет JSON с соответствиями предмет -> ID
});

// Временный маршрут для обновления subj_hex
Route::get('/update-subjects-hex', function() {
    $subjectMapping = [
        'Английский язык' => '00',
        'Астрономия' => '01',
        'Биология' => '02',
        'География' => '03',
        'ИЗО' => '04',
        'Информатика' => '05',
        'История' => '06',
        'Литература' => '07',
        'Математика' => '08',
        'Музыка' => '09',
        'Немецкий язык' => '10',
        'ОБЖ' => '11',
        'Обществознание' => '12',
        'Окружающий мир' => '13',
        'Русский язык' => '14',
        'Технология' => '15',
        'Физика' => '16',
        'Физкультура' => '17',
        'Французский язык' => '18',
        'Химия' => '19'
    ];

    $updated = [];
    foreach ($subjectMapping as $subject => $id) {
        $count = DB::table('books')
            ->where('subj', $subject)
            ->update(['subj_hex' => $id]);
        
        $updated[$subject] = [
            'id' => $id,
            'count' => $count
        ];
    }

    return response()->json($updated);
});
*/
require __DIR__.'/auth.php';
