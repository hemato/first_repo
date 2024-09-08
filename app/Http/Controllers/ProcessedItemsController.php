<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProcessedItemsController extends Controller
{
    public function fetchProcessedItems()
    {
        // URLs
        $processedItemsUrl = 'https://raw.githubusercontent.com/JPCodeCraft/AlbionLocalization/main/processed_items.json';
        $itemsUrl = 'https://raw.githubusercontent.com/broderickhyman/ao-bin-dumps/master/formatted/items.json';

        // URL'lerden verileri çek
        $processedItemsResponse = Http::get($processedItemsUrl);
        $itemsResponse = Http::get($itemsUrl);

        // Eğer istekte sorun varsa hata döndür
        if ($processedItemsResponse->failed()) {
            return response()->json(['error' => 'Processed items JSON could not be fetched.'], 500);
        }

        if ($itemsResponse->failed()) {
            return response()->json(['error' => 'Items JSON could not be fetched.'], 500);
        }

        $jsonData = $processedItemsResponse->json();
        $itemsJsonData = $itemsResponse->json();

        // Kategorilere göre unique name'leri ayır
        $categories = [
            'melee' => [],
            'magic' => [],
            'ranged' => [],
            'offhand' => [],
            'armor' => [],
            'accessories' => []
        ];

        // JSON içindeki tüm anahtarları kontrol et ve sadece t4, t5, t6, t7, t8 olanları al
        foreach ($jsonData as $key => $items) {
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (is_array($item) && isset($item['@shopcategory'], $item['@uniquename']) && isset($categories[$item['@shopcategory']])) {
                        $uniqueName = $item['@uniquename'];
                        // UniqueName'in t4 ve üstü olup olmadığını kontrol et
                        if (strpos($uniqueName, 'T4') === 0 ||
                            strpos($uniqueName, 'T5') === 0 ||
                            strpos($uniqueName, 'T6') === 0 ||
                            strpos($uniqueName, 'T7') === 0 ||
                            strpos($uniqueName, 'T8') === 0) {
                            $category = $item['@shopcategory'];
                            $categories[$category][] = $uniqueName;
                        }
                    }
                }
            }
        }

        // `type` değeri `api` olan kayıtları temizle
        DB::table('api_links')->where('type', 'api')->delete();

        // Her kategori için URL oluştur ve ekrana yazdır
        $generatedUrls = [];
        foreach ($categories as $category => $uniqueNames) {
            if (!empty($uniqueNames)) {
                $allUniqueNames = [];

                // items.json içindeki tüm UniqueName'leri kontrol et
                foreach ($itemsJsonData as $item) {
                    if (isset($item['UniqueName'])) {
                        foreach ($uniqueNames as $uniqueName) {
                            if (strpos($item['UniqueName'], $uniqueName) !== false) {
                                $allUniqueNames[] = $item['UniqueName'];
                            }
                        }
                    }
                }

                // UniqueName'leri ikiye böl ve URL oluştur
                $uniqueNamesChunks = array_chunk($allUniqueNames, ceil(count($allUniqueNames) / 2));

                foreach ($uniqueNamesChunks as $index => $chunk) {
                    $uniqueNamesString = implode(',', $chunk);
                    $url = "https://europe.albion-online-data.com/api/v2/stats/prices/{$uniqueNamesString}";

                    // Veritabanına kaydet
                    DB::table('api_links')->insert([
                        'url' => $url,
                        'type' => 'api', // Type'ı belirleyin
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // URL'yi ekrana yazdır
                    $generatedUrls[] = "Generated URL for category '{$category}' (part " . ($index + 1) . "): {$url}";
                }
            }
        }

        return redirect('/prices');
    }
}
