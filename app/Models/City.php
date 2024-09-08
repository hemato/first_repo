<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_blackzone'];

    public function marketPrices()
    {
        return $this->hasMany(MarketPrices::class, 'city_id');
        // Bu fonksiyon, bir şehrin birçok market_prices kaydı olabileceğini belirtir.
    }
}

//hasMany: Bir tablo ile birçok satır arasında ilişki olduğunu gösterir.
// Yani bir şehir, birden fazla market_prices kaydına sahip olabilir. Örneğin, Londra'nın (City) birçok farklı eşyanın fiyat bilgilerini (MarketPrices) barındırabileceğini düşünün.
