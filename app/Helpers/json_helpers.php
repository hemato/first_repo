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

if (!function_exists('getItemDetails')) {
    function getItemDetails($uniqueName)
    {
        static $itemsData = null;

        if ($itemsData === null) {
            $jsonFile = File::get(resource_path('lang/items.json'));
            $itemsData = json_decode($jsonFile, true);
        }

        foreach ($itemsData as $item) {
            if ($item['UniqueName'] === $uniqueName) {
                return [
                    'itemName' => $item['LocalizedNames']['EN-US'] ?? '',
                    'itemDescription' => $item['LocalizedDescriptions']['EN-US'] ?? ''
                ];
            }
        }

        return [
            'itemName' => '',
            'itemDescription' => ''
        ];
    }
}
