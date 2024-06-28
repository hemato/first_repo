<?php

namespace App\Http\Controllers;

use App\Models\ApiLink;
use Illuminate\Http\Request;
use App\Models\MarketPrices;
use App\Models\City;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class MarketController extends Controller
{
    public function index()
    {
        $marketPrices = MarketPrices::with('city')->get();
        return view('market_prices.index', compact('marketPrices'));
    }

    public function store(Request $request)
    {
        $city = City::find($request->city_id);

        $existingPrice = MarketPrices::where('item_id', $request->item_id)
            ->where('city_id', $city->id)
            ->where('quality_id', $request->quality)
            ->first();

        if ($existingPrice) {
            $existingPrice->update([
                'item_name' => $request->item_name,
                'quantity' => $request->quantity,
                'sell_price_min' => $request->sell_price_min,
                'sell_price_min_date' => $this->validateDate($request->sell_price_min_date),
                'sell_price_max' => $request->sell_price_max,
                'sell_price_max_date' => $this->validateDate($request->sell_price_max_date),
                'buy_price_min' => $request->buy_price_min,
                'buy_price_min_date' => $this->validateDate($request->buy_price_min_date),
                'buy_price_max' => $request->buy_price_max,
                'buy_price_max_date' => $this->validateDate($request->buy_price_max_date),
                'description' => $request->description,
            ]);
        } else {
            MarketPrices::create([
                'item_id' => $request->item_id,
                'item_name' => $request->item_name,
                'city_id' => $city->id,
                'quality_id' => $request->quality,
                'quantity' => $request->quantity,
                'sell_price_min' => $request->sell_price_min,
                'sell_price_min_date' => $this->validateDate($request->sell_price_min_date),
                'sell_price_max' => $request->sell_price_max,
                'sell_price_max_date' => $this->validateDate($request->sell_price_max_date),
                'buy_price_min' => $request->buy_price_min,
                'buy_price_min_date' => $this->validateDate($request->buy_price_min_date),
                'buy_price_max' => $request->buy_price_max,
                'buy_price_max_date' => $this->validateDate($request->buy_price_max_date),
                'description' => $request->description,
            ]);
        }

        return redirect('/prices');
    }

    public function showItemPriceComparisons()
    {
        // Tüm market_prices kayıtlarını alıp farkı 10.000 ve büyük olanları bulmak için
        $allPrices = DB::table('market_prices as mp1')
            ->select(
                'mp1.item_id',
                'mp1.city_id as cheapest_city_id',
                'mp1.quality_id',
                'qualities.name as quality_name',
                'mp1.buy_price_min',
                'mp1.buy_price_min_date as buy_price_min_date',
                'mp2.city_id as expensive_city_id',
                'mp2.sell_price_max',
                'mp2.sell_price_max_date as sell_price_max_date'
            )
            ->join('cities', 'mp1.city_id', '=', 'cities.id') // cities tablosuyla mp1.city_id alanında eşleşme yapılır
            ->join('qualities', 'mp1.quality_id', '=', 'qualities.id') // qualities tablosuyla mp1.quality_id alanında eşleşme yapılır
            ->join('market_prices as mp2', function ($join) {
                $join->on('mp1.item_id', '=', 'mp2.item_id') // mp1 ve mp2 tabloları item_id alanında eşleşme yapar
                ->on('mp1.quality_id', '=', 'mp2.quality_id') // mp1 ve mp2 tabloları quality_id alanında eşleşme yapar
                ->whereRaw('mp2.sell_price_max - mp1.buy_price_min > 10000') // sell_price_max ile buy_price_min arasındaki fark 10000'den büyük olmalı
                ->whereRaw('mp1.city_id != mp2.city_id'); // mp1 ve mp2 tablolarının city_id alanları farklı olmalı
            })
            ->where('mp1.buy_price_min', '>', 0) // mp1 tablosundaki buy_price_min değeri 0'dan büyük olmalı
            ->where('cities.id', '<>', 3) // en ucuz şehirin id'si 3 olmamalı
            //->whereRaw('mp1.buy_price_min = (SELECT MIN(buy_price_min) FROM market_prices WHERE item_id = mp1.item_id AND quality_id = mp1.quality_id)') // mp1 tablosundaki en düşük buy_price_min değerini alır
            ->get(); // Sonucu getirir

        // En ucuz ve en pahalı şehirleri tespit etmek için
        $priceComparisons = [];
        foreach ($allPrices as $price) {
            $priceComparisons[] = [
                'item_id' => $price->item_id,
                'cheapest_city' =>  City::find($price->cheapest_city_id)->name,
                'cheapest_quality' => $price->quality_name,
                'min_buy_price' => $price->buy_price_min,
                'buy_price_min_date' => $price->buy_price_min_date,
                'expensive_city' => City::find($price->expensive_city_id)->name,
                'max_sell_price' => $price->sell_price_max,
                'sell_price_max_date' => $price->sell_price_max_date,
            ];
        }

        return view('market_prices.price_comparisons', compact('priceComparisons'));
    }
    public function fetchFromApi(Request $request)
    {
        $url = $request->input('api_url');
        $response = Http::get($url);

        // API URL'sini api_urls tablosuna kaydet
        ApiLink::create([
            'url' => $url,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (is_array($data) && !empty($data)) {
                foreach ($data as $priceData) {
                    $city = City::firstOrCreate(['name' => $priceData['city']], ['is_blackzone' => false]);

                    $existingPrice = MarketPrices::where('item_id', $priceData['item_id'])
                        ->where('city_id', $city->id)
                        ->where('quality_id', $priceData['quality'])
                        ->first();

                    if ($existingPrice) {
                        $existingPrice->update([
                            'item_name' => $priceData['item_id'],
                            'quantity' => $priceData['quantity'] ?? 1,
                            'sell_price_min' => $priceData['sell_price_min'] ?? 0,
                            'sell_price_min_date' => $this->validateDate($priceData['sell_price_min_date'] ?? now()),
                            'sell_price_max' => $priceData['sell_price_max'] ?? 0,
                            'sell_price_max_date' => $this->validateDate($priceData['sell_price_max_date'] ?? now()),
                            'buy_price_min' => $priceData['buy_price_min'] ?? 0,
                            'buy_price_min_date' => $this->validateDate($priceData['buy_price_min_date'] ?? now()),
                            'buy_price_max' => $priceData['buy_price_max'] ?? 0,
                            'buy_price_max_date' => $this->validateDate($priceData['buy_price_max_date'] ?? now()),
                            'description' => 'Fetched from API',
                        ]);
                    } else {
                        MarketPrices::create([
                            'item_id' => $priceData['item_id'],
                            'item_name' => $priceData['item_id'],  // item_name eksikse item_id'yi kullan
                            'city_id' => $city->id,
                            'quality_id' => $priceData['quality'],
                            'quantity' => $priceData['quantity'] ?? 1,
                            'sell_price_min' => $priceData['sell_price_min'] ?? 0,
                            'sell_price_min_date' => $this->validateDate($priceData['sell_price_min_date'] ?? now()),
                            'sell_price_max' => $priceData['sell_price_max'] ?? 0,
                            'sell_price_max_date' => $this->validateDate($priceData['sell_price_max_date'] ?? now()),
                            'buy_price_min' => $priceData['buy_price_min'] ?? 0,
                            'buy_price_min_date' => $this->validateDate($priceData['buy_price_min_date'] ?? now()),
                            'buy_price_max' => $priceData['buy_price_max'] ?? 0,
                            'buy_price_max_date' => $this->validateDate($priceData['buy_price_max_date'] ?? now()),
                            'description' => 'Fetched from API',
                        ]);
                    }
                }
            }
        }

        return redirect('/prices');
    }

    private function validateDate($date)
    {
        try {
            if (empty($date)) {
                return '2000-01-01 00:00:00';
            }

            $dateTime = new \DateTime($date);
            if ($dateTime->format('Y') < 1000) {
                return '2000-01-01 00:00:00';
            }

            return $dateTime->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return '2000-01-01 00:00:00';
        }
    }

}
