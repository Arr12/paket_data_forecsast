<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('admin.pages.home');
})->name('dashboard');

Route::get('/master-provider', [Controller::class, 'MasterProviderIndex'])->name('master-provider');
Route::get('/master-barang', [Controller::class, 'MasterBarangIndex'])->name('master-barang');
Route::get('/master-user', [Controller::class, 'MasterUserIndex'])->name('master-user');

Route::get('/transaksi', function () {
    return view('admin.pages.transaksi');
})->name('transaksi');

Route::get('/forecasting', [Controller::class, 'ForecastingIndex'])->name('forecasting');
Route::get('/laporan', [Controller::class, 'LaporanIndex'])->name('laporan');
