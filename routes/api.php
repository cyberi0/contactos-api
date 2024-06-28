<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ContactoController;
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

Route::post('/contactos/', [ContactoController::class, 'register']);
Route::put('/contactos/{id}', [ContactoController::class, 'update']);
Route::delete('/contactos/{id}', [ContactoController::class, 'delete']);
Route::get('/contactos/', [ContactoController::class, 'getAll']);
Route::get('/contactos/{id}', [ContactoController::class, 'getById']);
Route::options('/contactos/filter/', [ContactoController::class, 'filterContacts']);
