<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PantryController;
use App\Http\Controllers\ShoppingListController;

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

Route::prefix('v1')->group(function () {
    
    // Rutas de autenticación (públicas)
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    // Rutas protegidas (requieren autenticación)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Rutas de autenticación (protegidas)
        Route::prefix('auth')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::put('change-password', [AuthController::class, 'changePassword']);
            Route::put('profile', [AuthController::class, 'updateProfile']);
        });

        // Rutas de productos
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::post('/', [ProductController::class, 'store']);
            Route::get('/search', [ProductController::class, 'search']);
            Route::get('/barcode/{barcode}', [ProductController::class, 'getByBarcode']);
            Route::get('/barcode/{barcode}/nutritional', [ProductController::class, 'nutritionalInfo']);
            Route::get('/barcode/{barcode}/suggestions', [ProductController::class, 'suggestions']);
            Route::get('/{id}', [ProductController::class, 'show']);
            Route::put('/{id}', [ProductController::class, 'update']);
            Route::delete('/{id}', [ProductController::class, 'destroy']);
        });

        // Rutas de despensas
        Route::prefix('pantries')->group(function () {
            Route::get('/', [PantryController::class, 'index']);
            Route::post('/', [PantryController::class, 'store']);
            Route::get('/{id}', [PantryController::class, 'show']);
            Route::put('/{id}', [PantryController::class, 'update']);
            Route::delete('/{id}', [PantryController::class, 'destroy']);
            Route::post('/{id}/items', [PantryController::class, 'addItem']);
            Route::put('/{id}/items/{itemId}', [PantryController::class, 'updateItem']);
            Route::delete('/{id}/items/{itemId}', [PantryController::class, 'deleteItem']);
            Route::get('/{id}/notifications', [PantryController::class, 'notifications']);
            Route::post('/{id}/share', [PantryController::class, 'share']);
            Route::post('/shared/{token}', [PantryController::class, 'joinShared']);
        });

        // Rutas de listas de compra
        Route::prefix('shopping-lists')->group(function () {
            Route::get('/', [ShoppingListController::class, 'index']);
            Route::post('/', [ShoppingListController::class, 'store']);
            Route::get('/{id}', [ShoppingListController::class, 'show']);
            Route::put('/{id}', [ShoppingListController::class, 'update']);
            Route::delete('/{id}', [ShoppingListController::class, 'destroy']);
            Route::post('/{id}/items', [ShoppingListController::class, 'addItem']);
            Route::put('/{id}/items/{itemId}/purchased', [ShoppingListController::class, 'markItemPurchased']);
            Route::put('/{id}/items/{itemId}/unpurchased', [ShoppingListController::class, 'unmarkItemPurchased']);
            Route::delete('/{id}/items/{itemId}', [ShoppingListController::class, 'deleteItem']);
            Route::post('/{id}/complete', [ShoppingListController::class, 'complete']);
            Route::get('/{id}/suggestions', [ShoppingListController::class, 'suggestions']);
            Route::post('/{id}/share', [ShoppingListController::class, 'share']);
            Route::post('/shared/{token}', [ShoppingListController::class, 'joinShared']);
        });
    });

    // Rutas públicas para acceso compartido (sin autenticación requerida)
    Route::get('/pantry/shared/{token}', [PantryController::class, 'showSharedByToken']);
    Route::get('/shopping-list/shared/{token}', [ShoppingListController::class, 'showSharedByToken']);
});

// Ruta de prueba para verificar que la API está funcionando
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Pantry Manager API is working!',
        'version' => '1.0.0',
        'timestamp' => now()->toISOString()
    ]);
});
