<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\OpportunitiesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/prices', [MarketController::class, 'index'])->name('prices');
Route::post('/prices', [MarketController::class, 'store']);
Route::post('/fetch-from-api', [MarketController::class, 'fetchFromApi']);

Route::get('/opportunities', [OpportunitiesController::class, 'index'])->name('opportunities');

Route::get('/price-comparisons', [MarketController::class, 'showItemPriceComparisons'])->name('price-comparisons');
