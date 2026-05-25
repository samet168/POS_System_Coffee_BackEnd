<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\dashobardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\IceLevelController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemSizePriceController;
use App\Http\Controllers\ItemStatusController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\PaymentStatusController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SugarLevelController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth.token'])->group(function () {



    Route::post('/logout', [AuthController::class, 'logout']);

    // ADMIN ---
    Route::middleware(['role:admin'])->group(function () {

        Route::get('/dashboard/stats', [dashobardController ::class, 'stats']);

        Route::get('/user', [UserController::class, 'index']);
        Route::get('/user/list', [UserController::class, 'list']);
        Route::post('/user', [UserController::class, 'store']);
        Route::get('/user/{id}', [UserController::class, 'show']);
        Route::post('/user/{id}', [UserController::class, 'update']);
        Route::delete('/user/{id}', [UserController::class, 'destroy']);

        Route::get('/item-categories', [ItemCategoryController::class, 'index']);
        Route::get('/item-categories/list', [ItemCategoryController::class, 'list']);
        Route::post('/item-categories', [ItemCategoryController::class, 'store']);
        Route::get('/item-categories/{id}', [ItemCategoryController::class, 'show']);
        Route::post('/item-categories/{id}', [ItemCategoryController::class, 'update']);
        Route::delete('/item-categories/{id}', [ItemCategoryController::class, 'destroy']);


        Route::get('/payment-types', [PaymentTypeController::class, 'index']);
        Route::get('/payment-types/list', [PaymentTypeController::class, 'list']);
        Route::post('/payment-types', [PaymentTypeController::class, 'store']);
        Route::get('/payment-types/{id}', [PaymentTypeController::class, 'show']);
        Route::post('/payment-types/{id}', [PaymentTypeController::class, 'update']);
        Route::delete('/payment-types/{id}', [PaymentTypeController::class, 'destroy']);

        Route::get('/payment-statuses', [PaymentStatusController::class, 'index']);
        Route::post('/payment-statuses', [PaymentStatusController::class, 'store']);
        Route::get('/payment-statuses/{id}', [PaymentStatusController::class, 'show']);
        Route::post('/payment-statuses/{id}', [PaymentStatusController::class, 'update']);
        Route::delete('/payment-statuses/{id}', [PaymentStatusController::class, 'destroy']);

        Route::get('/items', [ItemController::class, 'index']);
        Route::get('/items/list', [ItemController::class, 'list']);
        Route::post('/items', [ItemController::class, 'store']);
        Route::get('/items/{id}', [ItemController::class, 'show']);
        Route::post('/items/{id}', [ItemController::class, 'update']);
        Route::delete('/items/{id}', [ItemController::class, 'destroy']);

        Route::get('/item-size-prices', [ItemSizePriceController::class, 'index']);
        Route::get('/item-size-prices/list', [ItemSizePriceController::class, 'List']);
        Route::post('/item-size-prices', [ItemSizePriceController::class, 'store']);
        Route::get('/item-size-prices/{id}', [ItemSizePriceController::class, 'show']);
        Route::post('/item-size-prices/{id}', [ItemSizePriceController::class, 'update']);
        Route::delete('/item-size-prices/{id}', [ItemSizePriceController::class, 'destroy']);

        Route::get('/discounts', [DiscountController::class, 'index']);
        Route::get('/discounts/list', [DiscountController::class, 'List']);
        Route::post('/discounts', [DiscountController::class, 'store']);
        Route::get('/discounts/{id}', [DiscountController::class, 'show']);
        Route::post('/discounts/{id}', [DiscountController::class, 'update']);
        Route::delete('/discounts/{id}', [DiscountController::class, 'destroy']);

        Route::get('/order_items', [OrderItemController::class, 'index']);
        Route::get('/order_items/list', [OrderItemController::class, 'list']);
        Route::post('/order_items', [OrderItemController::class, 'store']);
        Route::get('/order_items/{id}', [OrderItemController::class, 'show']);
        Route::post('/order_items/{id}', [OrderItemController::class, 'update']);
        Route::delete('/order_items/{id}', [OrderItemController::class, 'destroy']);

        Route::get('/sizes', [SizeController::class, 'index']);
        Route::post('/sizes', [SizeController::class, 'store']);
        Route::get('/sizes/{id}', [SizeController::class, 'show']);
        Route::post('/sizes/{id}', [SizeController::class, 'update']);
        Route::delete('/sizes/{id}', [SizeController::class, 'destroy']);

    // USER និង ADMIN ---
        Route::middleware(['role:admin,user'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/list', [OrderController::class, 'list']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders/{id}', [OrderController::class, 'update']);
        Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::get('/invoices/list', [InvoiceController::class, 'List']);
        Route::post('/invoices', [InvoiceController::class, 'store']);
        Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
        Route::post('/invoices/{id}', [InvoiceController::class, 'update']);
        Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy']);

        Route::get('/view-items', [ItemController::class, 'index']);
        Route::get('/view-items/list', [ItemController::class, 'list']);

        Route::get('/profile', [UserController::class, 'profile']);
        Route::post('/profile/update', [UserController::class, 'updateProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
    
    });

    });
    
});