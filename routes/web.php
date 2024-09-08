<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\OpportunitiesController;
use App\Http\Controllers\ComparisonsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FlipController;
use App\Http\Controllers\ProcessedItemsController;

Route::get('/', function () {
    return view('welcome');
});

//prices//
//prices sayfası için route tanımlar ve $url = route('prices'); prices için url tanımı yapar.
Route::get('/prices', [MarketController::class, 'index'])->name('prices');
//prices sayfası her yüklendiğinde itemnameler için istek atar
Route::get('/prices', [MarketController::class, 'showItemNames']);
//prices sayfası için store fonksiyonunu çalıştırır.
Route::post('/prices', [MarketController::class, 'store']);
//prices sayfası için fetch from api popup fonksiyonunu çalıştırır.
Route::post('/fetch-from-api', [MarketController::class, 'fetchFromApi']);

//prices sayfasında yeni sekme ile item detail sayfasını açar $url = route('item.details', ['item_id' => 123]);
Route::get('/item/{item_id}', [MarketController::class, 'showItemDetails'])->name('item.details');

//
Route::get('/opportunities', [OpportunitiesController::class, 'index'])->name('opportunities');

//price-comparisons sayfaları için route tanımlar ve name içindeki veriler için url tanımı yapar.
Route::get('/price-comparisons1', [ComparisonsController::class, 'showItemPriceComparisons1'])->name('price-comparisons1');
Route::get('/price-comparisons2', [ComparisonsController::class, 'showItemPriceComparisons2'])->name('price-comparisons2');
Route::get('/price-comparisons3', [ComparisonsController::class, 'showItemPriceComparisons3'])->name('price-comparisons3');
Route::get('/price-comparisons4', [ComparisonsController::class, 'showItemPriceComparisons4'])->name('price-comparisons4');

//flips//
//apiden freemarketalbiondan flipleri çeker. ama 75 den az olanları çektiği için işe yaramaz.
Route::get('/flips/update', [FlipController::class, 'updateFlips']);
//jsondan freemarketalbiondan flipleri çeker. ama 75 den az olanları çektiği için işe yaramaz.
Route::get('/flips/update-json', [FlipController::class, 'updateFromJsonFile']);
//bu flipleri listeler
Route::get('/flips', [FlipController::class, 'index']);

//api_link tablosu için belirli kategorilere ait linkler oluşturur
Route::get('/process-items', [ProcessedItemsController::class, 'fetchProcessedItems']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
