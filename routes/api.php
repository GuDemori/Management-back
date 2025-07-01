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
use App\Http\Controllers\CepController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductNicknameController;
use App\Http\Controllers\ProductStockController;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function() {
    return response('ok', 200);
});
Route::get('/schedule-runner', function (Request $request) {
    if ($request->query('key') !== env('SCHEDULE_KEY')) {
        abort(403, 'Acesso não autorizado');
    }

    Artisan::call('schedule:run');

    return response()->json([
        'status' => 'ok',
        'executed' => now()->toDateTimeString(),
    ]);
});

Route::get('/cep/{cep}', [CepController::class, 'show']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/refresh', [UserController::class, 'refresh']);
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')
        ->post('/logout', [UserController::class, 'logout']);
Route::get('/products/search', [ProductController::class, 'searchByNickname']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::get('/product-categories', [ProductCategoryController::class, 'index']);
Route::get('/product-categories/{product_category}', [ProductCategoryController::class, 'show']);

Route::get('/product-nicknames', [ProductNicknameController::class, 'index']);
Route::get('/product-nicknames/{id}', [ProductNicknameController::class, 'show']);

Route::get('/establishment-types', [EstablishmentTypeController::class, 'index']);
Route::get('/establishment-types/{establishment_type}', [EstablishmentTypeController::class, 'show']);

/*
|-----------------------------------------------------------------------
| Rotas para todos os usuários autenticados (client, coworker, admin)
|-----------------------------------------------------------------------
*/
Route::middleware(['auth:api', CheckRole::class . ':client,coworker,admin'])->group(function () {

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);

});

/*
|-----------------------------------------------------------------------
| Rotas para coworker e admin
|-----------------------------------------------------------------------
*/
Route::middleware(['auth:api', CheckRole::class . ':coworker,admin'])->group(function () {

    Route::get('/users/{user}', [UserController::class, 'show']);

    Route::get('/clients', [UserController::class, 'clients']);
    Route::put('/clients/{id}', [UserController::class, 'update']);

    Route::get('/stock', [StockController::class, 'index']);
    Route::put('/stock/{stock}/deactivate', [StockController::class, 'deactivate']);

    Route::get('/suppliers', [SupplierController::class, 'index']);

    Route::get('/product-stock', [ProductStockController::class, 'index']);
    Route::get('/product-stock/{product}/{stock}', [ProductStockController::class, 'show']);
    Route::post('/product-stock',               [ProductStockController::class, 'store']);
    Route::put('/product-stock/{product}/{stock}', [ProductStockController::class, 'update']);
    Route::delete('/product-stock/{product}/{stock}', [ProductStockController::class, 'destroy']);

    Route::get('/products/{product}/nicknames', [ProductNicknameController::class, 'getByProduct']);
    Route::put('/products/{product}/nicknames', [ProductController::class, 'updateNicknames']);
    Route::put('/product-nicknames/{id}', [ProductNicknameController::class, 'update']);
    Route::delete('/product-nicknames/{id}', [ProductNicknameController::class, 'destroy']);
    Route::post('/product-nicknames', [ProductNicknameController::class, 'store']);


});

/*
|-----------------------------------------------------------------------
| Rotas apenas para admins
|-----------------------------------------------------------------------
*/
Route::middleware(['auth:api', CheckRole::class . ':admin'])->group(function () {

    // USERS
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    // PRODUCTS
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::put('/products/{product}/deactivate', [ProductController::class, 'deactivate']);
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

    // PRODUCT STOCK
    Route::post('/product-stock', [ProductStockController::class, 'store']);
    Route::put('/product-stock/{product}/{stock}', [ProductStockController::class, 'update']);
    Route::delete('/product-stock/{product}/{stock}', [ProductStockController::class, 'destroy']);

});