<?php

use Illuminate\Support\Facades\File;

function getLocalizationNameVariable($item_id)
{
    $json = File::get(resource_path('lang/items.json'));
    $items = json_decode($json, true);

    foreach ($items as $item) {
        if ($item['UniqueName'] === $item_id) {
            return $item['LocalizationNameVariable'];
        }
    }

    return null;
}

function getItemsWithSameLocalizationNameVariable($localizationNameVariable)
{
    $json = File::get(resource_path('lang/items.json'));
    $items = json_decode($json, true);

    $itemIds = [];
    foreach ($items as $item) {
        if ($item['LocalizationNameVariable'] === $localizationNameVariable) {
            $itemIds[] = $item['UniqueName'];
        }
    }

    return $itemIds;
}
