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
