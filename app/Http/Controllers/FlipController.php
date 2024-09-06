<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class FlipController extends Controller
{
    public function index()
    {
        // JSON dosyasını okuyoruz
        $json = File::get(resource_path('lang/flips.json'));
        $items = json_decode($json, true);

        // Her bir item için profit hesaplaması yapıyoruz
        foreach ($items as &$item) {
            $buyPrice = $item['buyOrder']['unitPriceSilver'];
            $sellPrice = $item['sellOrder']['unitPriceSilver'];
            $profit = $buyPrice - $sellPrice;

            // upgradeResources varsa, her bir resource'un count * price'ını hesaplıyoruz
            if (isset($item['upgradeResources'])) {
                foreach ($item['upgradeResources'] as $resource) {
                    $profit -= $resource['@count'] * $resource['price'];
                }
            }

            // Profit değerini item'a ekliyoruz
            $item['profit'] = $profit;
        }

        // Verileri blade'e gönderiyoruz
        return view('flips.index', ['items' => $items]);
    }
}
