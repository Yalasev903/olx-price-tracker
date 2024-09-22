<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;

// Главная страница
Route::get('/', function () {
    return view('welcome');
});

// Маршрут для обработки POST-запроса подписки
Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);

// Маршрут для подтверждения подписки по ссылке в письме
Route::get('/subscription/verify/{id}', [SubscriptionController::class, 'verify'])->name('subscription.verify');

// Маршрут для отображения формы подписки
Route::get('/subscribe-form', function() {
    return view('subscribe');
});
