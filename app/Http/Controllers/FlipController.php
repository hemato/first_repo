<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FlipController extends Controller
{
    public function index()
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
            $flipsData = $apiData['flipperResponse']['flips'];

            // Her item için profit hesaplayalım
            foreach ($flipsData as &$item) {
                // buyOrder, sellOrder ve id'nin mevcut olup olmadığını kontrol ediyoruz
                if (isset($item['id']) && isset($item['buyOrder']) && isset($item['sellOrder'])) {
                    $buyPrice = $item['buyOrder']['unitPriceSilver'];
                    $sellPrice = $item['sellOrder']['unitPriceSilver'];
                    $profit = $buyPrice - $sellPrice;

                    // Eğer upgradeResources varsa maliyetlerini düşelim
                    if (isset($item['upgradeResources'])) {
                        foreach ($item['upgradeResources'] as $resource) {
                            $profit -= $resource['@count'] * $resource['price'];
                        }
                    }

                    // Hesaplanan profiti item'e ekleyelim
                    $item['profit'] = $profit;
                } else {
                    // Eğer buyOrder, sellOrder veya id yoksa, item'ı işaretle
                    $item['profit'] = 'N/A'; // Profit hesaplanamadı
                }
            }

            // Profit 50.000'den büyük olanları filtreleyelim
            $filteredFlips = array_filter($flipsData, function ($item) {
                return is_numeric($item['profit']) && $item['profit'] > 50000;
            });

            // Veriyi view'e gönderelim
            return view('flips.index', ['flipsData' => $filteredFlips]);
        } else {
            // Eğer istek başarısız olursa hata mesajı döndür
            return response()->json(['error' => 'API isteği başarısız oldu'], 500);
        }
    }
}
