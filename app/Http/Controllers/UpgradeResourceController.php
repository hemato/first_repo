<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\UpgradeResource;
use App\Models\MarketPrices;
use Illuminate\Support\Facades\Log;

class UpgradeResourceController extends Controller
{
    public function fetchUpgradeRequirementsForAllItems()
    {
        // API URL'sini belirleyin
        $apiUrl = "https://raw.githubusercontent.com/JPCodeCraft/AlbionLocalization/main/processed_items.json";

        // API'ye istek gönder
        $response = Http::get($apiUrl);

        // Yanıtı kontrol edin
        if ($response->successful()) {
            $data = $response->json();
            Log::info('API Response:', $data); // API yanıtını loglayın

            // Gelen response'dan enchantments bilgilerini al
            $items = MarketPrices::distinct()->pluck('item_name');
            Log::info('Item Names:', $items->toArray()); // item_name'leri loglayın

            // Yanıtın iç yapısına göre doğru dizinlere erişin
            $categories = ['weapon', 'equipmentitem'];
            foreach ($categories as $category) {
                $categoryData = $data[$category] ?? [];
                foreach ($categoryData as $item) {
                    $item_name = $item['@uniquename'] ?? null;
                    if ($item_name && $items->contains($item_name)) {
                        $enchantments = $item['enchantments']['enchantment'] ?? [];

                        foreach ($enchantments as $enchantment) {
                            $enchantmentLevel = $enchantment['@enchantmentlevel'];
                            $upgradeResource = $enchantment['upgraderequirements']['upgraderesource'] ?? null;

                            // Upgrade resource bilgilerini kaydet
                            if ($upgradeResource) {
                                Log::info('Saving Upgrade Resource:', [
                                    'item_name' => $item_name,
                                    'enchantmentlevel' => $enchantmentLevel,
                                    'upgraderesource_name' => $upgradeResource['@uniquename'],
                                    'upgraderesource_count' => $upgradeResource['@count']
                                ]);

                                UpgradeResource::updateOrCreate(
                                    [
                                        'item_name' => $item_name,
                                        'enchantmentlevel' => $enchantmentLevel,
                                    ],
                                    [
                                        'upgraderesource_name' => $upgradeResource['@uniquename'],
                                        'upgraderesource_count' => $upgradeResource['@count']
                                    ]
                                );
                            }
                        }
                    }
                }
            }

            return response()->json(['message' => 'Upgrade requirements for all items saved successfully.']);
        } else {
            Log::error('API Request Failed:', ['url' => $apiUrl, 'status' => $response->status()]); // Hata durumunda loglama
            return response()->json(['error' => 'API request failed'], 500);
        }
    }
}
