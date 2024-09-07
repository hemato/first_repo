<?php

namespace App\Console\Commands;

use App\Models\ApiLink;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\MarketPrices;
use App\Models\City;

class FetchAlbionData extends Command
{
    protected $signature = 'fetch:albion-data';

    protected $description = 'Fetch data from Albion Online API every 5 minutes';

    public function handle()
    {

        // market_prices tablosundaki mevcut verileri sil
        MarketPrices::truncate();

        // Tüm API linklerini al
        $apiLinks = ApiLink::all();

        foreach ($apiLinks as $apiLink) {
            // API'den veri çek
            $response = Http::get($apiLink->url);

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
        }

        $this->info('Data fetched successfully from Albion Online API.');
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
