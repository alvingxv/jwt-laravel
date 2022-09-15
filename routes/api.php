<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TodoController;


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


Route::group(['prefix' => 'auth'], function ($router) {

    Route::post('login', [UserController::class, 'login'])->name('login');
    Route::post('register', [UserController::class, 'register'])->name('register');

    Route::group([
        'middleware' => 'jwt.verify'
    ], function ($router) {

        Route::post('logout', [UserController::class, 'logout'])->name('logout');
        Route::post('refresh', [UserController::class, 'refresh'])->name('refresh');
        Route::get('me', [UserController::class, 'me'])->name('me');
    });
});

Route::group([
    'prefix' => "todo",
    'middleware' => 'jwt.verify'
], function () {
    
    Route::get('/', [TodoController::class, 'index'])->name('todo.index');
    Route::post('/', [TodoController::class, 'store'])->name('todo.store');
    Route::get('/{id}', [TodoController::class, 'show'])->name('todo.show');
    Route::put('/{id}', [TodoController::class, 'update'])->name('todo.update');
    Route::delete('/{id}', [TodoController::class, 'destroy'])->name('todo.destroy');
});
