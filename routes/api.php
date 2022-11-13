<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ForecastingController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

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
    Route::get('/transaction-day', [TransactionsController::class, 'DataTransactionDay'])->name('transaction-day');
    Route::get('/transaction-month', [TransactionsController::class, 'DataTransactionMonth'])->name('transaction-month');
    Route::get('/transaction-year', [TransactionsController::class, 'DataTransactionYear'])->name('transaction-year');
    Route::get('/transaction-peryear', [TransactionsController::class, 'DataTransactionPerYear'])->name('transaction-peryear');
    Route::get('/get-transaction-split', [TransactionsController::class, 'DataTransactionSplit'])->name('get-transaction-split');
    Route::post('/split-transaction', [TransactionsController::class, 'UpdateTransaction'])->name('post-split-transaction');
    Route::post('/post-transaction', [TransactionsController::class, 'PostTransaction'])->name('post-transaction');
    Route::post('/post-transaction-detail', [TransactionsController::class, 'PostTransactionDetail'])->name('post-transaction-detail');
    Route::post('/delete-transaction', [TransactionsController::class, 'DeleteTransaction'])->name('delete-transaction');
});
Route::prefix('/pemesanan')->name('api.pemesanan.')->group(function(){
    Route::get('/get-pemesanan', [PemesananController::class, 'show'])->name('get-pemesanan');
    Route::post('/post-pemesanan', [PemesananController::class, 'store'])->name('post-pemesanan');
    Route::post('/update-pemesanan', [PemesananController::class, 'update'])->name('update-pemesanan');
    Route::post('/done-pemesanan', [PemesananController::class, 'done'])->name('done-pemesanan');
    Route::post('/delete-pemesanan', [PemesananController::class, 'destroy'])->name('delete-pemesanan');
});
Route::prefix('/stock')->name('api.stock.')->group(function(){
    Route::get('/get-stock', [StockController::class, 'show'])->name('get-stock');
    Route::post('/post-stock', [StockController::class, 'store'])->name('post-stock');
    Route::post('/delete-stock', [StockController::class, 'destroy'])->name('delete-stock');
});
Route::prefix('/data-master')->name('api.forecasting.')->group(function(){
    Route::get('/get-provider', [Controller::class, 'DataProvider'])->name('get-provider');
    Route::post('/post-provider', [Controller::class, 'PostProvider'])->name('post-provider');
    Route::post('/delete-provider', [Controller::class, 'DeleteProvider'])->name('delete-provider');
    Route::get('/get-barang', [Controller::class, 'DataBarang'])->name('get-barang');
    Route::post('/post-barang', [Controller::class, 'PostBarang'])->name('post-barang');
    Route::post('/delete-barang', [Controller::class, 'DeleteBarang'])->name('delete-barang');
    Route::get('/get-user', [UserController::class, 'show'])->name('get-user');
    Route::post('/post-user', [UserController::class, 'store'])->name('post-user');
    Route::post('/delete-user', [UserController::class, 'destroy'])->name('delete-user');
    Route::post('/update-user', [UserController::class, 'update'])->name('update-user');
    Route::get('/get-roles', [RoleController::class, 'show'])->name('get-roles');
    Route::post('/post-roles', [RoleController::class, 'store'])->name('post-roles');
    Route::post('/delete-roles', [RoleController::class, 'destroy'])->name('delete-roles');
    Route::post('/update-roles', [RoleController::class, 'update'])->name('update-roles');
});
