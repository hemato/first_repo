<?php

namespace App\Http\Controllers;

use App\Models\ApiLink;
use Illuminate\Http\Request;
use App\Models\MarketPrices;
use App\Models\City;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use App\Services\ItemNameService;
use App\Models\UpgradeResource;

class MarketController extends Controller
{
    public function index()
    {

        // MarketPrices'ı yükle, city ilişkisini de dahil et
        $marketPrices = MarketPrices::with('city')->get();

        return view('market_prices.index', compact('marketPrices'));
    }

    public function resources()
    {
        // Benzersiz item_name değerlerini al
        $uniqueItemNames = MarketPrices::select('item_name')
            ->distinct()
            ->pluck('item_name');

        // Her item_name için gerekli upgrade kaynaklarını al
        $listResources = [];
        foreach ($uniqueItemNames as $itemName) {
            $listResources[$itemName] = UpgradeResource::where('item_name', $itemName)
                ->orderBy('enchantmentlevel')
                ->get(['enchantmentlevel', 'upgraderesource_name', 'upgraderesource_count']);
        }

        return view('market_prices.resources', compact('listResources'));
    }

    public function store(Request $request)
    {
        $city = City::find($request->city_id);
        $enchant = $this->calculateEnchant($request->item_id);

        $existingPrice = MarketPrices::where('item_id', $request->item_id)
            ->where('city_id', $city->id)
            ->where('quality_id', $request->quality)
            ->first();

        if ($existingPrice) {
            $existingPrice->update([
                'item_name' => $this->extractItemName($request->item_id),
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
                'enchant' => $enchant ?? 0, // Enchant değeri
            ]);
        } else {
            MarketPrices::create([
                'item_id' => $request->item_id,
                'item_name' => $this->extractItemName($request->item_id),
                'city_id' => $city->id,
                'quality_id' => $request->quality,
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
                'enchant' => $enchant ?? 0, // Enchant değeri
            ]);
        }

        return redirect('/prices');
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
                foreach ($data as $priceData) {
                    $city = City::firstOrCreate(['name' => $priceData['city']], ['is_blackzone' => false]);
                    $enchant = $this->calculateEnchant($priceData['item_id']);

                    $existingPrice = MarketPrices::where('item_id', $priceData['item_id'])
                        ->where('city_id', $city->id)
                        ->where('quality_id', $priceData['quality'])
                        ->first();

                    if ($existingPrice) {
                        $existingPrice->update([
                            'item_name' => $this->extractItemName($priceData['item_id']),
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
                            'enchant' => $enchant ?? 0, // Enchant değeri
                        ]);
                    } else {
                        MarketPrices::create([
                            'item_id' => $priceData['item_id'],
                            'item_name' => $this->extractItemName($priceData['item_id']),
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
                            'enchant' => $enchant ?? 0, // Enchant değeri
                        ]);
                    }
                }
            }
        }

        return redirect('/prices');
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

    public function showItemDetails($item_id)
    {
        $localizationNameVariable = getLocalizationNameVariable($item_id);

        if (!$localizationNameVariable) {
            return redirect()->back()->with('error', 'Item not found.');
        }

        $itemIds = getItemsWithSameLocalizationNameVariable($localizationNameVariable);

        $itemDetails = DB::table('market_prices')
            ->select(
                'market_prices.*',
                'cities.name as city_name',
                'qualities.name as quality_name'
            )
            ->join('cities', 'market_prices.city_id', '=', 'cities.id')
            ->join('qualities', 'market_prices.quality_id', '=', 'qualities.id')
            ->whereIn('market_prices.item_id', $itemIds)
            ->get();

        $groupedDetails = $itemDetails->groupBy('city_name');

        return view('market_prices.item_details', compact('groupedDetails', 'item_id'));
    }

    private function extractItemName($item_id)
    {
        // Check if item_id contains '@' and return the part before '@', otherwise return the original item_id
        return strpos($item_id, '@') !== false ? substr($item_id, 0, strpos($item_id, '@')) : $item_id;
    }

    private function calculateEnchant($item_id)
    {
        if (strpos($item_id, '@') !== false) {
            return (int) substr($item_id, -1); // Enchant değerini al
        }
        return 0; // Enchant yoksa sıfır döner
    }

    public function showItemNames(ItemNameService $itemNameService)
    {
        // Service sınıfını kullanarak JSON verisini al
        $jsonData = $itemNameService->getItemNameMappings();

        // Market prices tablosundaki tüm item'ları alalım
        $marketPrices = MarketPrices::all();

        // JSON verileri ile item_id'leri eşleyelim
        foreach ($marketPrices as $marketPrice) {
            $marketPrice->item_name = $jsonData[$marketPrice->item_id] ?? 'Unknown';
        }

        return view('market_prices.index', compact('marketPrices'));
    }
}
