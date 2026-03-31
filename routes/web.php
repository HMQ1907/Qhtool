<?php

use App\Http\Controllers\CheckFileDownloadExistController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

use App\Http\Controllers\IndexDashboardController;
use App\Http\Controllers\DownloadFileController;
use App\Http\Controllers\IndexHistoryController;
use App\Http\Controllers\NotFoundController;
use App\Http\Controllers\Purchase\ApproveSPOPurchaseController;
use App\Http\Controllers\Purchase\DeleteSPOPurchaseController;
use App\Http\Controllers\Purchase\DeleteSPOPurchaseItemController;
use App\Http\Controllers\ReportErrorController;

use App\Http\Controllers\Purchase\ExportHistoryPurchaseController;
use App\Http\Controllers\Purchase\ExportRecentlyPurchaseController;
use App\Http\Controllers\Purchase\IndexHistoryPurchaseController;
use App\Http\Controllers\Purchase\IndexRecentlyPurchaseController;
use App\Http\Controllers\Purchase\IndexSPOPurchaseController;
use App\Http\Controllers\Purchase\StoreRecentlyPurchaseController;
use App\Http\Controllers\Purchase\DetailSPOPurchaseController;
use App\Http\Controllers\Purchase\DownloadSPOPurchaseController;
use App\Http\Controllers\Purchase\PreviewSPOPurchaseController;
use App\Http\Controllers\PurchaseTemplate\DestroyTemplateController;
use App\Http\Controllers\PurchaseTemplate\EditTemplateController;
use App\Http\Controllers\PurchaseTemplate\IndexTemplateController;
use App\Http\Controllers\PurchaseTemplate\IndexTemplateMappingController;
use App\Http\Controllers\PurchaseTemplate\StoreTemplateController;
use App\Http\Controllers\PurchaseTemplate\UpdateTemplateController;
use App\Http\Controllers\PurchaseTemplate\UpdateTemplateMappingController;

use App\Http\Controllers\Quotation\ExportHistoryQuotationController;
use App\Http\Controllers\Quotation\ExportRecentlyQuotationController;
use App\Http\Controllers\Quotation\IndexHistoryQuotationController;
use App\Http\Controllers\Quotation\IndexRecentlyQuotationController;
use App\Http\Controllers\Quotation\StoreRecentlyQuotationController;

use App\Http\Controllers\StockPurchase\ExportStockPurchaseController;
use App\Http\Controllers\StockPurchase\GetListStockPurchaseController;
use App\Http\Controllers\StockPurchase\ImportStockPurchaseController;
use App\Http\Controllers\StockPurchase\PurchaseStockController;

Route::middleware(['auth'])->group(function () {
    Route::get('check-file-exist', CheckFileDownloadExistController::class)->name('check-file-exist');
    Route::get('download-file', DownloadFileController::class)->name('download-file');
    Route::get('/logs', [LogViewerController::class, 'index'])->name('logs');
    Route::fallback(NotFoundController::class);
    Route::post('report-errors', ReportErrorController::class)->name('report-errors');
    Route::get('/', IndexDashboardController::class)->name('dashboard');
    Route::get('history', IndexHistoryController::class)->name('history.index');

    Route::prefix('quotation')->group(function () {
        Route::get('/recently', IndexRecentlyQuotationController::class)->name('quotation.recently.index');
        Route::post('/recently', StoreRecentlyQuotationController::class)->name('quotation.recently.store');
        Route::get('/export', ExportRecentlyQuotationController::class)->name('quotation.recently.export');
        Route::get('/history', IndexHistoryQuotationController::class)->name('quotation.history.index');
        Route::get('export/history', ExportHistoryQuotationController::class)->name('quotation.history.export');
    });

    Route::prefix('purchase')->group(function () {
        Route::get('/recently', IndexRecentlyPurchaseController::class)->name('purchase.recently.index');
        Route::post('/recently', StoreRecentlyPurchaseController::class)->name('purchase.recently.store');
        Route::get('/export', ExportRecentlyPurchaseController::class)->name('purchase.recently.export');
        Route::get('/spo', IndexSPOPurchaseController::class)->name('purchase.spo.index');
        Route::put('/spo/{id}/approve', ApproveSPOPurchaseController::class)->name('purchase.spo.approve');
        Route::get('/spo/download', DownloadSPOPurchaseController::class)->name('purchase.spo.download');
        Route::get('/spo/{id}', DetailSPOPurchaseController::class)->name('purchase.spo.detail');
        Route::get('/spo/{id}/preview', PreviewSPOPurchaseController::class)->name('purchase.spo.preview');
        Route::delete('/spo', DeleteSPOPurchaseController::class)->name('purchase.spo.destroy');
        Route::delete('/spo/item/{itemId}', DeleteSPOPurchaseItemController::class)->name('purchase.spo.destroy.item');
        Route::get('/history', IndexHistoryPurchaseController::class)->name('purchase.history.index');
        Route::get('export/history', ExportHistoryPurchaseController::class)->name('purchase.history.export');
    });

    Route::prefix('stock-purchase')->group(function () {
        Route::get('/', GetListStockPurchaseController::class)->name('stock-purchase.index');
        Route::get('/export', ExportStockPurchaseController::class)->name('stock-purchase.export');
        Route::post('/purchase/{id}', PurchaseStockController::class)->name('stock-purchase.purchase');
        Route::post('/import', ImportStockPurchaseController::class)->name('stock-purchase.import');
    });

    Route::prefix('purchase-template')->group(function () {
        Route::get('/', IndexTemplateController::class)->name('purchase-template.index');
        Route::put('{id}', EditTemplateController::class)->name('purchase-template.edit');
        Route::post('{id}', UpdateTemplateController::class)->name('purchase-template.update');
        Route::post('/', StoreTemplateController::class)->name('purchase-template.store');
        Route::get('/mapping', IndexTemplateMappingController::class)->name('purchase-template.mapping.index');
        Route::post('/mapping/{code}', UpdateTemplateMappingController::class)->name('purchase-template.mapping.update');
        Route::delete('/', DestroyTemplateController::class)->name('purchase-template.destroy');
    });
});
