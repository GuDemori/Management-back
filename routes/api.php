<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstablishmentTypeController;
use App\Http\Controllers\ProductCategoryController;
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
Route::middleware(['auth:api', CheckRole::class . ':client,coworker,admin,v1'])->group(function () {

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::get('/product-categories', [ProductCategoryController::class, 'index']);
    Route::get('/product-categories/{product_category}', [ProductCategoryController::class, 'show']);
    Route::get('/establishment-types', [EstablishmentTypeController::class, 'index']);
    Route::get('/establishment-types/{establishment_type}', [EstablishmentTypeController::class, 'show']);

});

/*
|-----------------------------------------------------------------------
| Rotas para coworker e admin
|-----------------------------------------------------------------------
*/
Route::middleware(['auth:api', CheckRole::class . ':coworker,admin'])->group(function () {

    Route::get('/stock', [StockController::class, 'index']);
    Route::get('/suppliers', [SupplierController::class, 'index']);

});

/*
|-----------------------------------------------------------------------
| Rotas apenas para admins
|-----------------------------------------------------------------------
*/
Route::middleware(['auth:api'/*, CheckRole::class . ':admin'*/])->group(function () {

    // USERS
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
    Route::put('/establishment-types/{establishment_type}', [EstablishmentTypeController::class, 'update']);
    Route::delete('/establishment-types/{establishment_type}', [EstablishmentTypeController::class, 'destroy']);


    // PRODUCT CATEGORIES
    Route::post('/product-categories', [ProductCategoryController::class, 'store']);
    Route::put('/product-categories/{product_category}', [ProductCategoryController::class, 'update']);
    Route::delete('/product-categories/{product_category}',[ProductCategoryController::class, 'destroy']);

});