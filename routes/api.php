<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ForecastingController;
use App\Http\Controllers\TransactionsController;

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
Route::prefix('/forecasting')->name('api.forecasting.')->group(function(){
    Route::get('/do-forecasting', [ForecastingController::class, 'DataForecasting'])->name('get');
});
Route::prefix('/laporan')->name('api.laporan.')->group(function(){
    Route::get('/get-laporan', [TransactionsController::class, 'DataReport'])->name('get-laporan');
    Route::get('/get-chart', [TransactionsController::class, 'ChartData'])->name('get-chart');
});
Route::prefix('/transaction')->name('api.transaction.')->group(function(){
    Route::get('/get-transaction', [TransactionsController::class, 'DataTransaction'])->name('get-transaction');
    Route::get('/get-transaction-split', [TransactionsController::class, 'DataTransactionSplit'])->name('get-transaction-split');
    Route::post('/split-transaction', [TransactionsController::class, 'UpdateTransaction'])->name('post-split-transaction');
    Route::post('/post-transaction', [TransactionsController::class, 'PostTransaction'])->name('post-transaction');
    Route::post('/post-transaction-detail', [TransactionsController::class, 'PostTransaction'])->name('post-transaction-detail');
    Route::post('/delete-transaction', [TransactionsController::class, 'DeleteTransaction'])->name('delete-transaction');
});
Route::prefix('/data-master')->name('api.forecasting.')->group(function(){
    Route::get('/get-provider', [Controller::class, 'DataProvider'])->name('get-provider');
    Route::post('/post-provider', [Controller::class, 'PostProvider'])->name('post-provider');
    Route::post('/delete-provider', [Controller::class, 'DeleteProvider'])->name('delete-provider');
    Route::get('/get-barang', [Controller::class, 'DataBarang'])->name('get-barang');
    Route::post('/post-barang', [Controller::class, 'PostBarang'])->name('post-barang');
    Route::post('/delete-barang', [Controller::class, 'DeleteBarang'])->name('delete-barang');
    Route::get('/get-user', [Controller::class, 'DataUser'])->name('get-user');
});
