<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ItemNameService
{
    public function getItemNameMappings()
    {
        return Cache::remember('item_name_mappings', 60, function () {
            $response = Http::get('https://raw.githubusercontent.com/JPCodeCraft/AlbionFormattedItemsParser/main/us_name_mappings.json');
            return $response->successful() ? $response->json() : [];
        });
    }
}
