<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstablishmentTypeController;
use App\Http\Middleware\CheckRole;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/refresh', [UserController::class, 'refresh']);
Route::middleware('auth:api')
        ->post('/logout', [UserController::class, 'logout']);

/*
|-----------------------------------------------------------------------
| Rotas para todos os usuários autenticados (client, coworker, admin)
|-----------------------------------------------------------------------
*/
Route::middleware(['auth:api', CheckRole::class . ':client,coworker,admin'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    // futuramente: criar pedido apenas pra si
});

/*
|-----------------------------------------------------------------------
| Rotas para coworker e admin
|-----------------------------------------------------------------------
*/
Route::middleware(['auth:api', CheckRole::class . ':coworker,admin'])->group(function () {
    Route::get('/stock', [StockController::class, 'index']);
    Route::get('/suppliers', [SupplierController::class, 'index']);
    // futuramente: criar pedido para qualquer cliente
});

/*
|-----------------------------------------------------------------------
| Rotas apenas para admins
|-----------------------------------------------------------------------
*/
Route::middleware(['auth:api', CheckRole::class . ':admin'])->group(function () {
    // USERS
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    // PRODUCTS
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // SUPPLIERS
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show']);
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy']);

    // STOCK
    Route::post('/stock', [StockController::class, 'store']);
    Route::get('/stock/{stock}', [StockController::class, 'show']);
    Route::put('/stock/{stock}', [StockController::class, 'update']);
    Route::delete('/stock/{stock}', [StockController::class, 'destroy']);

    // ESTABLISHMENT TYPES
    Route::post('/establishment-types', [EstablishmentTypeController::class, 'store']);
    Route::get('/establishment-types', [EstablishmentTypeController::class, 'index']);
    Route::get('/establishment-types/{id}', [EstablishmentTypeController::class, 'show']);
    Route::put('/establishment-types/{id}', [EstablishmentTypeController::class, 'update']);
    Route::delete('/establishment-types/{id}', [EstablishmentTypeController::class, 'destroy']);
});

Route::any('/debug-route', function () {
    return response()->json(['message' => 'API está viva'], 200);
});
