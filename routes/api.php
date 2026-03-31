<?php

use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\Purchase\CancelPurchaseApiController;
use App\Http\Controllers\Api\Purchase\CreatePurchaseApiController;
use App\Http\Controllers\Api\Purchase\GetStatusPurchaseApiController;
use App\Http\Controllers\Api\Quotation\CancelQuotationApiController;
use App\Http\Controllers\Api\Quotation\CreateQuotationApiController;
use App\Http\Controllers\Api\Quotation\GetStatusQuotationApiController;
use Illuminate\Support\Facades\Route;

// Route::middleware('custom-api')->group(function () {
Route::group(['prefix' => 'quotation'], function () {
    Route::any('/create', CreateQuotationApiController::class)->name('api.quotation.create');
    Route::any('/status/{identify_code}', GetStatusQuotationApiController::class)->name('api.quotation.getStatus');
    Route::any('/cancel/{identify_code}', CancelQuotationApiController::class)->name('api.quotation.cancel');
});

Route::group(['prefix' => 'purchase'], function () {
    Route::any('/create', CreatePurchaseApiController::class)->name('api.purchase.create');
    Route::any('/status/{identify_code}', GetStatusPurchaseApiController::class)->name('api.purchase.getStatus');
    Route::any('/cancel/{identify_code}', CancelPurchaseApiController::class)->name('api.purchase.cancel');
});

Route::group(
    ['prefix' => 'product'],
    function () {
        Route::get('/update', ProductApiController::class)->name('api.product.update');
    }
);
// });
