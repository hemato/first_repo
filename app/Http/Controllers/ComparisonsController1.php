<?php

namespace App\Http\Controllers;

use App\Models\MarketPrices;
use App\Models\City;
use App\Services\ItemNameService;
use Illuminate\Support\Collection;
use Carbon\Carbon;
class ComparisonsController1 extends Controller
{
    protected $itemNameService;

    public function __construct(ItemNameService $itemNameService)
    {
        $this->itemNameService = $itemNameService;
    }

    public function showItemPriceComparisons()
    {
        $allPrices = MarketPrices::with(['city', 'quality'])
            ->where('buy_price_max', '>', 0)
            ->get();

        $itemNameMappings = $this->itemNameService->getItemNameMappings();

        $priceComparisons1 = $this->calculateComparisons($allPrices, $itemNameMappings);

        return view('comparisons.price_comparisons1', compact('priceComparisons1'));
    }

    private function calculateComparisons(Collection $allPrices, array $itemNameMappings): array
    {
        $comparisons = [];

        foreach ($allPrices as $price1) { //$allPrices değeri --> $allPrices = MarketPrices::with(['city', 'quality']) ->where('buy_price_max', '>', 0) ->get(); buradan geldi.

            $potentialSales = MarketPrices::where('item_id', $price1->item_id) //$potentialSales : item_id si price1 item_id ile aynı olan kayıtlar
                ->where('quality_id', $price1->quality_id) //$potentialSales : quality_id si price1 quality_id ile aynı olan kayıtlar
                ->where('city_id', '<>', $price1->city_id) //$potentialSales : city_id si price1 city_id ile aynı olmayan kayıtlar
                ->where('city_id', '<>', 3) //$potentialSales : city_id si price1 city_id 3 olmayan kayıtlar satın alım şehri id si 3 olmayacak
                ->where('sell_price_min', '>', 0) //$potentialSales : sell_price_min değeri 0 dan büyük olanlar yani alabileceğimiz itemlar.
                ->where('sell_price_min_date', '>=', Carbon::now()->subMinutes(120))
                ->get();

            //yani price1 yani city1 satış şehri
            //yani price2 yani city2 alış şehri

            foreach ($potentialSales as $price2) {
                $profit = $price1->buy_price_max * 0.96 - $price2->sell_price_min;
                if ($profit > 50000) {
                    $itemName = $itemNameMappings[$price1->item_id] ?? 'Unknown';
                    $comparisons[] = [
                        'item_id' => $price1->item_id,
                        'item_name' => $itemName,
                        'city1' => $price1->city->name,
                        'city1_quality' => $price1->quality->name,
                        'enchant' => $price1->enchant,
                        'city1_buy_price_max' => $price1->buy_price_max,
                        'city1_buy_price_max_date' => $price1->buy_price_max_date,
                        'city2' => $price2->city->name,
                        'city2_sell_price_min' => $price2->sell_price_min,
                        'city2_sell_price_min_date' => $price2->sell_price_min_date,
                        'profit' => $profit
                    ];
                }
            }
        }

        return $comparisons;
    }
}
