<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SaveAlbionCategoryUrls extends Command
{
    protected $signature = 'save:albion-category-urls';

    protected $description = 'Save Albion Online category URLs to the database';

    public function handle()
    {
        // JSON dosyasını oku
        $jsonPath = resource_path('lang/prosssed_items.json');
        $jsonData = json_decode(file_get_contents($jsonPath), true);

        if (!$jsonData) {
            $this->error('JSON dosyası okunamadı veya bozuk.');
            return;
        }

        // `lang/items.json` dosyasını oku
        $itemsJsonPath = resource_path('lang/items.json');
        $itemsJsonData = json_decode(file_get_contents($itemsJsonPath), true);

        if (!$itemsJsonData) {
            $this->error('items.json dosyası okunamadı veya bozuk.');
            return;
        }

        // Kategorilere göre unique name'leri ayır
        $categories = [
            'melee' => [],
            'magic' => [],
            'ranged' => [],
            'offhand' => [],
            'armor' => [],
            'accessories' => []
        ];

        // JSON içindeki tüm anahtarları kontrol et
        foreach ($jsonData as $key => $items) {
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (is_array($item) && isset($item['@shopcategory']) && isset($categories[$item['@shopcategory']])) {
                        $category = $item['@shopcategory'];
                        $uniquename = $item['@uniquename'];
                        $categories[$category][] = $uniquename;
                    }
                }
            }
        }

        // Her kategori için URL oluştur ve ekrana yazdır
        foreach ($categories as $category => $uniqueNames) {
            if (!empty($uniqueNames)) {
                $allUniqueNames = [];

                // `items.json` içindeki tüm UniqueName'leri kontrol et
                foreach ($itemsJsonData as $item) {
                    if (isset($item['UniqueName'])) {
                        foreach ($uniqueNames as $uniqueName) {
                            if (strpos($item['UniqueName'], $uniqueName) !== false) {
                                $allUniqueNames[] = $item['UniqueName'];
                            }
                        }
                    }
                }

                // UniqueName'leri virgülle ayır ve URL oluştur
                $uniqueNamesString = implode(',', $allUniqueNames);
                $url = "https://europe.albion-online-data.com/api/v2/stats/prices/{$uniqueNamesString}";

                // Veritabanına kaydet
                DB::table('api_links')->insert([
                    'url' => $url,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // URL'yi ekrana yazdır
                $this->info("Generated URL for category '{$category}': {$url}");
            }
        }

        $this->info("Tüm kategoriler için URL'ler başarıyla oluşturuldu.");
    }
}
