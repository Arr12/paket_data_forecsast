<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
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

Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('actionlogin', [LoginController::class, 'actionlogin'])->name('actionlogin');
Route::get('register', [RegisterController::class, 'register'])->name('register');
Route::post('register/action', [RegisterController::class, 'actionregister'])->name('actionregister');

Route::middleware('auth')->group(function () {
    Route::get('actionlogout', [LoginController::class, 'actionlogout'])->name('actionlogout');
    Route::get('/dashboard', function () {
        return view('admin.pages.home');
    })->name('dashboard');

    Route::get('/master-provider', [Controller::class, 'MasterProviderIndex'])->name('master-provider');
    Route::get('/master-barang', [Controller::class, 'MasterBarangIndex'])->name('master-barang');
    Route::get('/master-user', [Controller::class, 'MasterUserIndex'])->name('master-user');
    Route::get('/master-role', [Controller::class, 'MasterRolesIndex'])->name('master-role');

    Route::get('/transaksi', function () {
        return view('admin.pages.transaksi');
    })->name('transaksi');

    Route::get('/pemesanan', function () {
        return view('admin.pages.pemesanan');
    })->name('pemesanan');

    Route::get('/forecasting', [Controller::class, 'ForecastingIndex'])->name('forecasting');
    Route::get('/laporan', [Controller::class, 'LaporanIndex'])->name('laporan');
});
