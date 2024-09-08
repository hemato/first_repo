<?php

//web.php > Tüm routelar burada oluşturulur


//Db ilişkileri:

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
        // Bu fonksiyon, market_prices tablosundaki city_id sütununun, cities tablosundaki verilerle ilişkili olduğunu belirtir.
    },
    public function marketPrices()
    {
        return $this->hasMany(MarketPrices::class, 'quality_id');
        // Bu fonksiyon, bir kalite seviyesinin birçok market_prices kaydı olabileceğini belirtir.
    }


    //belongsTo: Bu, Laravel'in bir tabloyu başka bir tabloyla ilişkilendirme şeklidir.
    // Örneğin, MarketPrices tablosundaki city_id sütunu, cities tablosundaki bir kaydı temsil eder. Bu sayede bir fiyatın hangi şehre ait olduğunu öğrenebiliriz.
    //hasMany: Bir tablo ile birçok satır arasında ilişki olduğunu gösterir.
    // Yani bir şehir, birden fazla market_prices kaydına sahip olabilir. Örneğin, Londra'nın (City) birçok farklı eşyanın fiyat bilgilerini (MarketPrices) barındırabileceğini düşünün.

