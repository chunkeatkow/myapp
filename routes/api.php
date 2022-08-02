<?php

use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
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

Route::post('sign-up', [UserController::class, 'register']);
Route::post('sign-in', [UserController::class, 'authenticate']);

Route::group(['middleware' => ['jwt.verify']], function() {
   Route::get('store/menu', [UserController::class, 'getStoreMenu']);
   Route::post('store/create', [StoreController::class, 'createStore']);
   Route::get('store/list', [StoreController::class, 'getStore']);
});
