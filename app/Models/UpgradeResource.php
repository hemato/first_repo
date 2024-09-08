<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpgradeResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'enchantmentlevel',
        'upgraderesource_name',
        'upgraderesource_count',
    ];

    // MarketPrices ile ilişki
    public function marketPrices()
    {
        return $this->belongsTo(MarketPrices::class, 'item_name', 'item_name');
    }
}
