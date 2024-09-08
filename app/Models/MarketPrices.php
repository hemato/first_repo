<?php

namespace App\Models; // Bu satır, dosyanın bulunduğu klasör yapısını belirtir.

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // Eloquent, Laravel'in veritabanı ile çalışmayı kolaylaştıran bir özelliğidir.

class MarketPrices extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', 'item_name', 'city_id', 'quality_id', 'quantity', 'sell_price_min', 'sell_price_min_date',
        'sell_price_max', 'sell_price_max_date', 'buy_price_min', 'buy_price_min_date', 'buy_price_max',
        'buy_price_max_date','enchant', 'description'
    ];

    // Şehir bilgisi ile ilişki kuruyoruz.
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
        // Bu fonksiyon, market_prices tablosundaki city_id sütununun, cities tablosundaki verilerle ilişkili olduğunu belirtir.
    }

    // Kalite bilgisi ile ilişki kuruyoruz.
    public function quality()
    {
        return $this->belongsTo(Quality::class, 'quality_id');
        // Bu fonksiyon, market_prices tablosundaki quality_id sütununun, qualities tablosundaki verilerle ilişkili olduğunu belirtir.
    }

    // UpgradeResource ile ilişki
    public function upgradeResource()
    {
        return $this->hasMany(UpgradeResource::class, 'item_name', 'item_name');
    }
}


//belongsTo: Bu, Laravel'in bir tabloyu başka bir tabloyla ilişkilendirme şeklidir.
// Örneğin, MarketPrices tablosundaki city_id sütunu, cities tablosundaki bir kaydı temsil eder. Bu sayede bir fiyatın hangi şehre ait olduğunu öğrenebiliriz.
//hasMany: Bir tablo ile birçok satır arasında ilişki olduğunu gösterir.
// Yani bir şehir, birden fazla market_prices kaydına sahip olabilir. Örneğin, Londra'nın (City) birçok farklı eşyanın fiyat bilgilerini (MarketPrices) barındırabileceğini düşünün.
