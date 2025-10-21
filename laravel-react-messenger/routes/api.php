<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;


Route::post('signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:api')->controller(ContactController::class)->prefix('contact')->group(function () {
    Route::get('index', 'index');
    Route::post('store', 'storeContact');
    Route::post('update', 'updateContact');
    Route::delete('delete', 'deleteContact');
});
