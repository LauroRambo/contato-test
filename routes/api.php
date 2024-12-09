<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CepController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [LoginController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/password-reset', [LoginController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::delete('/delete-account', [LoginController::class, 'deleteAccount']);
    Route::get('/list-contacts', [ContactController::class, 'getAllContactsByUser']);
    Route::post('/store-contact', [ContactController::class, 'store']);
    Route::put('/edit-contact', [ContactController::class, 'update']);
    Route::delete('/delete-contacts/{contact}', [ContactController::class, 'delete']);

    Route::get('/cep', [CepController::class, 'buscarEndereco']);
});


