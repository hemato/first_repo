<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Flip;
use App\Models\Resource;
use App\Models\FlipResource;
use Illuminate\Support\Facades\File;

class FlipController extends Controller
{
    public function index()
    {
        // Veritabanından verileri çekiyoruz
        $flips = Flip::all(); // `resources` ile birlikte almıyoruz

        // Her flip için profit hesaplıyoruz
        $flips->map(function ($flip) {
            // Buy fiyatından %4 tax düşüyoruz (100% - 4% = 96%)
            $buyPrice = $flip->buy_order_unit_price_silver * 0.96;

            // Sell fiyatı
            $sellPrice = $flip->sell_order_unit_price_silver;

            // Profit hesaplama: (buyPrice - sellPrice)
            $profit = $buyPrice - $sellPrice;

            // Eğer flip_resource varsa maliyetlerini düşelim
            $flipResources = FlipResource::where('flip_id', $flip->id)->get();

            foreach ($flipResources as $flipResource) {
                $resource = $flipResource->resource; // Resource modelini al
                $resourcePrice = $resource->price;
                $count = $flipResource->count;

                $profit -= $count * $resourcePrice;
            }

            // Hesaplanan profiti item'e ekleyelim
            $flip->profit = $profit;

            return $flip;
        });

        // Veriyi view'e gönderiyoruz
        return view('flips.index', ['flipsData' => $flips]);
    }

    public function updateFromJsonFile()
    {
        // Dosyanın yolunu belirleyin
        $filePath = storage_path('app/public/test.json');

        // Dosyayı okuma
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Dosya bulunamadı'], 404);
        }

        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Geçersiz JSON verisi'], 400);
        }

        $updatedCount = 0;
        $createdCount = 0;

        foreach ($data as $item) {
            // Tarihlerin geçerliliğini kontrol et
            $buyDetails = getItemDetails($item['buyOrder']['itemTypeId']);
            $sellDetails = getItemDetails($item['sellOrder']['itemTypeId']);
            $buyOrderCreatedAt = $this->formatDate($item['buyOrder']['createdAt'] ?? null);
            $buyOrderExpires = $this->formatDate($item['buyOrder']['expires'] ?? null);
            $sellOrderCreatedAt = $this->formatDate($item['sellOrder']['createdAt'] ?? null);
            $sellOrderExpires = $this->formatDate($item['sellOrder']['expires'] ?? null);
            $flipCreatedAt = $this->formatDate($item['createdAt'] ?? null);

            // Her item için veri tabanında kayıt veya güncelleme işlemi yapalım
            $flip = Flip::updateOrCreate(
                ['api_id' => $item['id'] ?? ''], // Kayıtları bu ID ile eşleştir
                [
                    'buy_order_id' => $item['buyOrder']['id'] ?? null,
                    'buy_order_item_type_id' => $buyDetails['itemName'],
                    'buy_order_location' => $item['buyOrder']['location'] ?? null,
                    'buy_order_quality_level' => $item['buyOrder']['qualityLevel'] ?? null,
                    'buy_order_enchantment_level' => $item['buyOrder']['enchantmentLevel'] ?? null,
                    'buy_order_unit_price_silver' => $item['buyOrder']['unitPriceSilver'] ?? 0,
                    'buy_order_amount' => $item['buyOrder']['amount'] ?? 0,
                    'buy_order_created_at' => $buyOrderCreatedAt,
                    'buy_order_expires' => $buyOrderExpires,
                    'buy_order_is_consumed' => $item['buyOrder']['isConsumed'] ?? false,
                    'buy_order_server' => $item['buyOrder']['server'] ?? null,

                    'sell_order_id' => $item['sellOrder']['id'] ?? null,
                    'sell_order_item_type_id' => $sellDetails['itemName'],
                    'sell_order_location' => $item['sellOrder']['location'] ?? null,
                    'sell_order_quality_level' => $item['sellOrder']['qualityLevel'] ?? null,
                    'sell_order_enchantment_level' => $item['sellOrder']['enchantmentLevel'] ?? null,
                    'sell_order_unit_price_silver' => $item['sellOrder']['unitPriceSilver'] ?? 0,
                    'sell_order_amount' => $item['sellOrder']['amount'] ?? 0,
                    'sell_order_created_at' => $sellOrderCreatedAt,
                    'sell_order_expires' => $sellOrderExpires,
                    'sell_order_is_consumed' => $item['sellOrder']['isConsumed'] ?? false,
                    'sell_order_server' => $item['sellOrder']['server'] ?? null,

                    'flip_created_at' => $flipCreatedAt,
                    'server' => $item['server'] ?? null,
                ]
            );

            // Güncellenen ve yeni eklenen kayıtları sayalım
            if ($flip->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }

            // `upgradeResources` varsa güncelleyelim
            if (isset($item['upgradeResources'])) {
                foreach ($item['upgradeResources'] as $resource) {
                    // `Resource` modelinde uniqueName ile `price`'ı güncelleyelim
                    $resourceModel = Resource::updateOrCreate(
                        ['unique_name' => $resource['@uniquename'] ?? ''],
                        ['price' => $resource['price'] ?? 0]
                    );

                    // FlipResource modelinde ilişkilendirelim
                    FlipResource::updateOrCreate(
                        ['flip_id' => $flip->id, 'resource_id' => $resourceModel->id],
                        ['count' => $resource['@count'] ?? 0]
                    );
                }
            }
        }

        // İlgili mesajı döndürelim
        return response()->json([
            'message' => 'Veriler güncellendi.',
            'updated_count' => $updatedCount,
            'created_count' => $createdCount,
        ]);
    }

    public function updateFlips(Request $request)
    {
        // API URL'si
        $apiUrl = 'https://api.albionfreemarket.com/flips?server=europe&checkEntitlements=false&historyStatusTopAmount=5';

        // Bearer Token
        $token = env('ALBION_API_TOKEN');

        // API'ye GET isteği atma
        $response = Http::withToken($token)->get($apiUrl);

        // Eğer istek başarılıysa veriyi al
        if ($response->successful()) {
            // API'den dönen veriyi al
            $apiData = $response->json();

            // `flips` dizisini al
            $flipsData = $apiData['flipperResponse']['flips'] ?? [];

            $updatedCount = 0;
            $createdCount = 0;

            foreach ($flipsData as $item) {
                // Tarihlerin geçerliliğini kontrol et
                $buyDetails = getItemDetails($item['buyOrder']['itemTypeId']);
                $sellDetails = getItemDetails($item['sellOrder']['itemTypeId']);
                $buyOrderCreatedAt = $this->formatDate($item['buyOrder']['createdAt'] ?? null);
                $buyOrderExpires = $this->formatDate($item['buyOrder']['expires'] ?? null);
                $sellOrderCreatedAt = $this->formatDate($item['sellOrder']['createdAt'] ?? null);
                $sellOrderExpires = $this->formatDate($item['sellOrder']['expires'] ?? null);
                $flipCreatedAt = $this->formatDate($item['createdAt'] ?? null);

                // Her item için veri tabanında kayıt veya güncelleme işlemi yapalım
                $flip = Flip::updateOrCreate(
                    ['api_id' => $item['id'] ?? ''], // Kayıtları bu ID ile eşleştir
                    [
                        'buy_order_id' => $item['buyOrder']['id'] ?? null,
                        'buy_order_item_type_id' => $buyDetails['itemName'],
                        'buy_order_location' => $item['buyOrder']['location'] ?? null,
                        'buy_order_quality_level' => $item['buyOrder']['qualityLevel'] ?? null,
                        'buy_order_enchantment_level' => $item['buyOrder']['enchantmentLevel'] ?? null,
                        'buy_order_unit_price_silver' => $item['buyOrder']['unitPriceSilver'] ?? 0,
                        'buy_order_amount' => $item['buyOrder']['amount'] ?? 0,
                        'buy_order_created_at' => $buyOrderCreatedAt,
                        'buy_order_expires' => $buyOrderExpires,
                        'buy_order_is_consumed' => $item['buyOrder']['isConsumed'] ?? false,
                        'buy_order_server' => $item['buyOrder']['server'] ?? null,

                        'sell_order_id' => $item['sellOrder']['id'] ?? null,
                        'sell_order_item_type_id' => $sellDetails['itemName'],
                        'sell_order_location' => $item['sellOrder']['location'] ?? null,
                        'sell_order_quality_level' => $item['sellOrder']['qualityLevel'] ?? null,
                        'sell_order_enchantment_level' => $item['sellOrder']['enchantmentLevel'] ?? null,
                        'sell_order_unit_price_silver' => $item['sellOrder']['unitPriceSilver'] ?? 0,
                        'sell_order_amount' => $item['sellOrder']['amount'] ?? 0,
                        'sell_order_created_at' => $sellOrderCreatedAt,
                        'sell_order_expires' => $sellOrderExpires,
                        'sell_order_is_consumed' => $item['sellOrder']['isConsumed'] ?? false,
                        'sell_order_server' => $item['sellOrder']['server'] ?? null,

                        'flip_created_at' => $flipCreatedAt,
                        'server' => $item['server'] ?? null,
                    ]
                );

                // Güncellenen ve yeni eklenen kayıtları sayalım
                if ($flip->wasRecentlyCreated) {
                    $createdCount++;
                } else {
                    $updatedCount++;
                }

                // `upgradeResources` varsa güncelleyelim
                if (isset($item['upgradeResources'])) {
                    foreach ($item['upgradeResources'] as $resource) {
                        // `Resource` modelinde uniqueName ile `price`'ı güncelleyelim
                        $resourceModel = Resource::updateOrCreate(
                            ['unique_name' => $resource['@uniquename'] ?? ''],
                            ['price' => $resource['price'] ?? 0]
                        );

                        // FlipResource modelinde ilişkilendirelim
                        FlipResource::updateOrCreate(
                            ['flip_id' => $flip->id, 'resource_id' => $resourceModel->id],
                            ['count' => $resource['@count'] ?? 0]
                        );
                    }
                }
            }




            // İlgili mesajı döndürelim
            return response()->json([
                'message' => 'Veriler güncellendi.',
                'updated_count' => $updatedCount,
                'created_count' => $createdCount,
            ]);
        } else {
            // Eğer istek başarısız olursa hata mesajı döndür
            return response()->json(['error' => 'API isteği başarısız oldu'], 500);
        }
    }

    // Tarih formatını kontrol eden yardımcı fonksiyon
    private function formatDate($date)
    {
        if (!$date) {
            return '9999-12-31 23:59:59'; // Varsayılan geçerli tarih
        }

        // Tarih formatını kontrol et ve MySQL uyumlu hale getir
        try {
            return (new \DateTime($date))->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            // Tarih geçersizse varsayılan değeri döndür
            return '9999-12-31 23:59:59';
        }
    }
}
