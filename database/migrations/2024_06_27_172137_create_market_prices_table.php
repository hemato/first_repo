<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketPricesTable extends Migration
{
    public function up()
    {
        Schema::create('market_prices', function (Blueprint $table) {
            $table->id();
            $table->string('item_id');
            $table->string('item_name');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->integer('quality');
            $table->integer('quantity');
            $table->decimal('sell_price_min', 10, 2);
            $table->timestamp('sell_price_min_date');
            $table->decimal('sell_price_max', 10, 2);
            $table->timestamp('sell_price_max_date');
            $table->decimal('buy_price_min', 10, 2);
            $table->timestamp('buy_price_min_date');
            $table->decimal('buy_price_max', 10, 2);
            $table->timestamp('buy_price_max_date');
            $table->text('description');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('market_prices');
    }
}
