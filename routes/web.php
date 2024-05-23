<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\UserController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/users/me', [UserController::class, 'show']);
    Route::get('/users/consommation', [UserController::class, 'getLastSevenConsumptions']);
    Route::post('/users/consommation', [UserController::class, 'addConsommation']);
    Route::get('/users/abonnement', [UserController::class, 'getAbonnement']);
    Route::post('/users/abonnement', [UserController::class, 'addAbonnement']);
    Route::get('/users/notifications', [UserController::class, 'getNotifications']);
    Route::post('/users/notifications', [UserController::class, 'addNotification']);
    Route::apiResource('users', UserController::class)->except(['show']);
});
