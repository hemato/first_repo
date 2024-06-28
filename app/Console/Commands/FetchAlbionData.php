<?php

namespace App\Console\Commands;

use App\Models\ApiLink;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Item;
use App\Models\City;

class FetchAlbionData extends Command
{
    protected $signature = 'fetch:albion-data';

    protected $description = 'Fetch data from Albion Online API every 5 minutes';

    public function handle()
    {
        // Tüm API linklerini al
        $apiLinks = ApiLink::all();

        foreach ($apiLinks as $apiLink) {
            // API'den veri çek
            $response = Http::get($apiLink->url);

            if ($response->successful()) {
                $data = $response->json();

                if (is_array($data) && !empty($data)) {
                    foreach ($data as $itemData) {
                        $city = City::firstOrCreate(['name' => $itemData['city']], ['is_blackzone' => false]);

                        $existingItem = Item::where('item_id', $itemData['item_id'])
                            ->where('city_id', $city->id)
                            ->where('quality', $itemData['quality'])
                            ->first();

                        if ($existingItem) {
                            $existingItem->update([
                                'item_name' => $itemData['item_id'],
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
                        } else {
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
