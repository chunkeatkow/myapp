<?php

use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('books', [BookController::class, 'index']);
Route::get('books/{id}', [BookController::class, 'show']);
Route::post('books', [BookController::class, 'store']);
Route::put('books', [BookController::class, 'update']);
Route::delete('books/{id}', [BookController::class, 'destroy']);

Route::post('register', [ApiAuthController::class, 'register'])->name('register.api');

Route::post('sign-up', [\App\Http\Controllers\UserController::class, 'register']);
Route::post('sign-in', [\App\Http\Controllers\UserController::class, 'authenticate']);
