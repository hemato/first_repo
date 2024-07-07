<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\OpportunitiesController;
use App\Http\Controllers\ComparisonsController;

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
Route::get('/item/{item_id}', [MarketController::class, 'showItemDetails'])->name('item.details');

Route::get('/opportunities', [OpportunitiesController::class, 'index'])->name('opportunities');

Route::get('/price-comparisons1', [ComparisonsController::class, 'showItemPriceComparisons1'])->name('price-comparisons1');
Route::get('/price-comparisons2', [ComparisonsController::class, 'showItemPriceComparisons2'])->name('price-comparisons2');
Route::get('/price-comparisons3', [ComparisonsController::class, 'showItemPriceComparisons3'])->name('price-comparisons3');
Route::get('/price-comparisons4', [ComparisonsController::class, 'showItemPriceComparisons4'])->name('price-comparisons4');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
