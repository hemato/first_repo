<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketPrices extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', 'item_name', 'city_id', 'quality_id', 'quantity', 'sell_price_min', 'sell_price_min_date',
        'sell_price_max', 'sell_price_max_date', 'buy_price_min', 'buy_price_min_date', 'buy_price_max',
        'buy_price_max_date', 'description'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function quality()
    {
        return $this->belongsTo(Quality::class);
    }
}
