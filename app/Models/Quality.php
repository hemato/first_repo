<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quality extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function marketPrices()
    {
        return $this->hasMany(MarketPrices::class, 'quality_id');
        // Bu fonksiyon, bir kalite seviyesinin birçok market_prices kaydı olabileceğini belirtir.
    }
}

//hasMany: Bir tablo ile birçok satır arasında ilişki olduğunu gösterir.
// Yani bir şehir, birden fazla market_prices kaydına sahip olabilir. Örneğin, Londra'nın (City) birçok farklı eşyanın fiyat bilgilerini (MarketPrices) barındırabileceğini düşünün.
