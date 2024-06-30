<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class ItemHelper
{
    public static function getItemLocalizationData($itemId)
    {
        $json = File::get(resource_path('lang/items.json'));
        $data = json_decode($json, true);

        foreach ($data as $item) {
            if ($item['UniqueName'] == $itemId) {
                return $item;
            }
        }

        return null;
    }
}
