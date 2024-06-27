<?php

namespace App\Http\Controllers;

use App\Models\ApiLink;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\City;
use Illuminate\Support\Facades\Http;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('city')->get();
        return view('items.index', compact('items'));
    }

    public function store(Request $request)
    {
        $city = City::find($request->city_id);

        Item::create([
            'item_id' => $request->item_id,
            'item_name' => $request->item_name,
            'city_id' => $city->id,
            'quality' => $request->quality,
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

        return redirect('/items');
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
                foreach ($data as $itemData) {
                    $city = City::firstOrCreate(['name' => $itemData['city']], ['isblackzone' => false]);

                    Item::create([
                        'item_id' => $itemData['item_id'],
                        'item_name' => $itemData['item_id'],  // item_name eksikse item_id'yi kullan
                        'city_id' => $city->id,
                        'quality' => $itemData['quality'],
                        'quantity' => $itemData['quantity'] ?? 1,
                        'sell_price_min' => $itemData['sell_price_min'] ?? 0,
                        'sell_price_min_date' => $this->validateDate($itemData['sell_price_min_date'] ?? now()),
                        'sell_price_max' => $itemData['sell_price_max'] ?? 0,
                        'sell_price_max_date' => $this->validateDate($itemData['sell_price_max_date'] ?? now()),
                        'buy_price_min' => $itemData['buy_price_min'] ?? 0,
                        'buy_price_min_date' => $this->validateDate($itemData['buy_price_min_date'] ?? now()),
                        'buy_price_max' => $itemData['buy_price_max'] ?? 0,
                        'buy_price_max_date' => $this->validateDate($itemData['buy_price_max_date'] ?? now()),
                        'description' => 'Fetched from API',
                    ]);
                }
            }
        }

        return redirect('/items');
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
