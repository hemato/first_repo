<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Flip extends Model
{
    protected $fillable = [
        'api_id', 'buy_order_id', 'buy_order_item_type_id', 'buy_order_location',
        'buy_order_quality_level', 'buy_order_enchantment_level', 'buy_order_unit_price_silver', 'buy_order_amount',
        'buy_order_created_at', 'buy_order_expires', 'buy_order_is_consumed',
        'buy_order_server', 'sell_order_id', 'sell_order_item_type_id',
        'sell_order_location', 'sell_order_quality_level', 'sell_order_enchantment_level', 'sell_order_unit_price_silver',
        'sell_order_amount', 'sell_order_created_at', 'sell_order_expires',
        'sell_order_is_consumed', 'sell_order_server', 'flip_created_at', 'created_at', 'server'
    ];

    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'flip_resource')
            ->withPivot('count');
    }
}
