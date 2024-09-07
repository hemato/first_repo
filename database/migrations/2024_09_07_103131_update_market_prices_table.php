<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMarketPricesTable extends Migration
{
    public function up()
    {
        Schema::table('market_prices', function (Blueprint $table) {
            // Sütunları BIGINT olarak değiştirme
            $table->bigInteger('sell_price_min')->change();
            $table->bigInteger('sell_price_max')->change();
            $table->bigInteger('buy_price_min')->change();
            $table->bigInteger('buy_price_max')->change();
        });
    }

    public function down()
    {
        Schema::table('market_prices', function (Blueprint $table) {
            // Sütunları INT olarak geri döndürme
            $table->integer('sell_price_min')->change();
            $table->integer('sell_price_max')->change();
            $table->integer('buy_price_min')->change();
            $table->integer('buy_price_max')->change();
        });
    }
}
