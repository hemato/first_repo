<?php

namespace App\Http\Controllers;

use App\Models\ApiLink;
use Illuminate\Http\Request;
use App\Models\MarketPrices;
use App\Models\City;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ComparisonsController extends Controller
{
    public function showItemPriceComparisons1()
    {
        // Tüm market_prices kayıtlarını alıp farkı 10.000 ve büyük olan buy_price_max değerlerini bulmak için
        $allPrices = DB::table('market_prices as mp1')
            ->select(
                'mp1.item_id',
                'mp1.city_id as city1_id',
                'mp1.quality_id',
                'qualities.name as quality_name',
                'mp1.buy_price_max as city1_buy_price_max',
                'mp1.buy_price_max_date as city1_buy_price_max_date',
                'mp2.city_id as city2_id',
                'mp2.sell_price_min as city2_sell_price_min',
                'mp2.sell_price_min_date as city2_sell_price_min_date'
            )
            ->join('cities as c1', 'mp1.city_id', '=', 'c1.id') // cities tablosuyla mp1.city_id alanında eşleşme yapılır
            ->join('qualities', 'mp1.quality_id', '=', 'qualities.id') // qualities tablosuyla mp1.quality_id alanında eşleşme yapılır
            ->join('market_prices as mp2', function ($join) {
                $join->on('mp1.item_id', '=', 'mp2.item_id') // mp1 ve mp2 tabloları item_id alanında eşleşme yapar
                ->on('mp2.quality_id', '=', 'mp1.quality_id') // mp1 ve mp2 tabloları quality_id alanında eşleşme yapar
                ->whereRaw('mp1.buy_price_max - mp2.sell_price_min > 1') // İki şehir arasındaki buy_price_max farkı 10000'den büyük olmalı
                ->whereRaw('mp1.city_id != mp2.city_id'); // mp1 ve mp2 tablolarının city_id alanları farklı olmalı
            })
            ->where('mp1.buy_price_max', '>', 0) // mp1 tablosundaki buy_price_max değeri 0'dan büyük olmalı
            ->where('mp2.sell_price_min', '>', 0) // mp2 tablosundaki sell_price_min değeri 0'dan büyük olmalı
            ->where('mp2.city_id', '<>', 3) // ikinci(alım) şehrin id'si 3 olmamalı
            ->where('mp1.quality_id', '<>', 5) // quality masterpiece olmamalı
            ->get(); // Sonucu getirir

        // Şehirler arasındaki buy_price_max farklarını tespit etmek için
        $priceComparisons1 = [];
        foreach ($allPrices as $price) {
            $profit = abs($price->city1_buy_price_max - $price->city2_sell_price_min); // Profit hesaplaması
            $priceComparisons1[] = [
                'item_id' => $price->item_id,
                'city1' => City::find($price->city1_id)->name,
                'city1_quality' => $price->quality_name,
                'city1_buy_price_max' => $price->city1_buy_price_max,
                'city1_buy_price_max_date' => $price->city1_buy_price_max_date,
                'city2' => City::find($price->city2_id)->name,
                'city2_sell_price_min' => $price->city2_sell_price_min,
                'city2_sell_price_min_date' => $price->city2_sell_price_min_date,
                'profit' => $profit // Profit değeri eklendi
            ];
        }

        return view('comparisons.price_comparisons1', compact('priceComparisons1'));
    }

    public function showItemPriceComparisons2()
    {
        // Tüm market_prices kayıtlarını alıp farkı 10.000 ve büyük olan buy_price_max değerlerini bulmak için
        $allPrices = DB::table('market_prices as mp1')
            ->select(
                'mp1.item_id',
                'mp1.city_id as city1_id',
                'mp1.quality_id',
                'qualities.name as quality_name',
                'mp1.buy_price_max as city1_buy_price_max',
                'mp1.buy_price_max_date as city1_buy_price_max_date',
                'mp2.city_id as city2_id',
                'mp2.sell_price_min as city2_sell_price_min',
                'mp2.sell_price_min_date as city2_sell_price_min_date'
            )
            ->join('cities as c1', 'mp1.city_id', '=', 'c1.id') // cities tablosuyla mp1.city_id alanında eşleşme yapılır
            ->join('qualities', 'mp1.quality_id', '=', 'qualities.id') // qualities tablosuyla mp1.quality_id alanında eşleşme yapılır
            ->join('market_prices as mp2', function ($join) {
                $join->on('mp1.item_id', '=', 'mp2.item_id') // mp1 ve mp2 tabloları item_id alanında eşleşme yapar
                ->on('mp2.quality_id', '=', 'mp1.quality_id') // mp1 ve mp2 tabloları quality_id alanında eşleşme yapar
                ->whereRaw('mp2.sell_price_min - mp1.buy_price_max > 1') // İki şehir arasındaki buy_price_max farkı 10000'den büyük olmalı
                ->whereRaw('mp1.city_id != mp2.city_id'); // mp1 ve mp2 tablolarının city_id alanları farklı olmalı
            })
            ->where('mp1.buy_price_max', '>', 0) // mp1 tablosundaki buy_price_max değeri 0'dan büyük olmalı
            ->where('mp2.sell_price_min', '>', 0) // mp2 tablosundaki sell_price_min değeri 0'dan büyük olmalı
            ->where('mp1.city_id', '<>', 3) // birinci(alım) şehrin id'si 3 olmamalı
            ->where('mp1.quality_id', '<>', 5) // quality masterpiece olmamalı
            ->get(); // Sonucu getirir

        // Şehirler arasındaki buy_price_max farklarını tespit etmek için
        $priceComparisons2 = [];
        foreach ($allPrices as $price) {
            $profit = abs($price->city2_sell_price_min - $price->city1_buy_price_max); // Profit hesaplaması
            $priceComparisons2[] = [
                'item_id' => $price->item_id,
                'city1' => City::find($price->city1_id)->name,
                'city1_quality' => $price->quality_name,
                'city1_buy_price_max' => $price->city1_buy_price_max,
                'city1_buy_price_max_date' => $price->city1_buy_price_max_date,
                'city2' => City::find($price->city2_id)->name,
                'city2_sell_price_min' => $price->city2_sell_price_min,
                'city2_sell_price_min_date' => $price->city2_sell_price_min_date,
                'profit' => $profit // Profit değeri eklendi
            ];
        }

        return view('comparisons.price_comparisons2', compact('priceComparisons2'));
    }

    public function showItemPriceComparisons3()
    {
        // Tüm market_prices kayıtlarını alıp farkı 10.000 ve büyük olan buy_price_max değerlerini bulmak için
        $allPrices = DB::table('market_prices as mp1')
            ->select(
                'mp1.item_id',
                'mp1.city_id as city1_id',
                'mp1.quality_id',
                'qualities.name as quality_name',
                'mp1.buy_price_max as city1_buy_price_max',
                'mp1.buy_price_max_date as city1_buy_price_max_date',
                'mp2.city_id as city2_id',
                'mp2.buy_price_max as city2_buy_price_max',
                'mp2.buy_price_max_date as city2_buy_price_max_date'
            )
            ->join('cities as c1', 'mp1.city_id', '=', 'c1.id') // cities tablosuyla mp1.city_id alanında eşleşme yapılır
            ->join('qualities', 'mp1.quality_id', '=', 'qualities.id') // qualities tablosuyla mp1.quality_id alanında eşleşme yapılır
            ->join('market_prices as mp2', function ($join) {
                $join->on('mp1.item_id', '=', 'mp2.item_id') // mp1 ve mp2 tabloları item_id alanında eşleşme yapar
                ->on('mp2.quality_id', '=', 'mp1.quality_id') // mp1 ve mp2 tabloları quality_id alanında eşleşme yapar
                ->whereRaw('mp2.buy_price_max - mp1.buy_price_max > 10000') // İki şehir arasındaki buy_price_max farkı 10000'den büyük olmalı
                ->whereRaw('mp1.city_id != mp2.city_id'); // mp1 ve mp2 tablolarının city_id alanları farklı olmalı
            })
            ->where('mp1.buy_price_max', '>', 0) // mp1 tablosundaki buy_price_max değeri 0'dan büyük olmalı
            ->where('mp2.buy_price_max', '>', 0) // mp2 tablosundaki sell_price_min değeri 0'dan büyük olmalı
            ->where('mp1.city_id', '<>', 3) // ikinci(alım) şehrin id'si 3 olmamalı
            ->where('mp1.quality_id', '<>', 5) // quality masterpiece olmamalı
            ->get(); // Sonucu getirir

        // Şehirler arasındaki buy_price_max farklarını tespit etmek için
        $priceComparisons3 = [];
        foreach ($allPrices as $price) {
            $profit = abs($price->city1_buy_price_max - $price->city2_buy_price_max); // Profit hesaplaması
            $priceComparisons3[] = [
                'item_id' => $price->item_id,
                'city1' => City::find($price->city1_id)->name,
                'city1_quality' => $price->quality_name,
                'city1_buy_price_max' => $price->city1_buy_price_max,
                'city1_buy_price_max_date' => $price->city1_buy_price_max_date,
                'city2' => City::find($price->city2_id)->name,
                'city2_buy_price_max' => $price->city2_buy_price_max,
                'city2_buy_price_max_date' => $price->city2_buy_price_max_date,
                'profit' => $profit // Profit değeri eklendi
            ];
        }

        return view('comparisons.price_comparisons3', compact('priceComparisons3'));
    }

}
