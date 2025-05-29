<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ClientsController; // Добавляем импорт
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SearchController;

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/cart/updateQuantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');

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
use App\Http\Controllers\TestController;

Route::get('/test', [TestController::class, 'index']);

Route::get('/contacts', function () {
    return view('contacts.index');
})->name('contacts');

// Главная страница теперь показывает список книг
Route::post('/admin/order/{userId}/{orderNum}/verify', [OrderController::class, 'toggleVerification'])
    ->name('admin.order.toggleVerification');

    Route::post('/admin/orders/{userId}/{orderNum}/cancel', [OrderController::class, 'cancel'])
    ->name('admin.order.cancel');
    Route::post('/admin/order/cancel/{userId}/{orderNum}', [CommandController::class, 'cancelOrder'])
    ->name('admin.order.cancel');
Route::post('/admin/order/restore/{userId}/{orderNum}', [CommandController::class, 'restoreOrder'])
    ->name('admin.order.restore');
Route::post('/admin/order/{userId}/{orderNum}/toggle-verification', [CommandController::class, 'toggleVerification'])
    ->name('admin.order.toggleVerification');

    Route::delete('/cart/delete', [CartController::class, 'deleteByOrderNum'])->name('cart.delete');
Route::post('/admin/order/toggle-status/{userId}/{orderNum}', [CommandController::class, 'toggleStatus'])
    ->name('admin.order.toggleStatus');

Route::get('/download-csv', [OrderController::class, 'downloadCsv'])->name('download.csv');

Route::get('/', [BookController::class, 'index'])->name('home');

Route::get('/about', function () {
    return view('about.about'); // папка.about
})->name('about');

// Добавляем маршрут dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Маршруты для книг
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/search', [BookController::class, 'search'])->name('books.search');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// Маршруты для корзины
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/order', [CartController::class, 'order'])->name('cart.order');
    Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
	Route::post('/cart/download', [CartController::class, 'download'])->name('cart.download');
	Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/update-price', [CartController::class, 'updatePrice'])->name('cart.updatePrice');
});

// Маршруты для заказов
Route::middleware('auth')->group(function () {
    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::get('/ordered', [OrderController::class, 'ordered'])->name('order.ordered');
    Route::post('/order/add', [OrderController::class, 'add'])->name('order.add');
    
	Route::post('/order/download', [OrderController::class, 'download'])->name('order.download');
    Route::get('/orderdownld/{userId}/{orderNum}', [OrderController::class, 'downloadOrder'])->name('order.download');
	Route::delete('/order/remove', [OrderController::class, 'remove'])->name('order.remove');
});

// Остальные маршруты для аутентификации
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	
});

// Маршруты для администратора
Route::middleware(['auth', 'checkAdmin'])->group(function () {
    Route::get('/command', [CommandController::class, 'index'])->name('command.index');
    
    // Маршруты для управления клиентами
    Route::get('/clients', [ClientsController::class, 'index'])->name('clients.index');
    Route::post('/clients/{user}/toggle-verification', [ClientsController::class, 'toggleVerification'])->name('clients.toggle-verification');
});

require __DIR__.'/auth.php';