<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlipsTable extends Migration
{
    public function up()
    {
        Schema::create('flips', function (Blueprint $table) {
            $table->id();
            $table->string('api_id')->unique(); // API'den gelen ID

            // Buy Order bilgileri
            $table->string('buy_order_id'); // BuyOrder ID
            $table->string('buy_order_item_type_id'); // BuyOrder itemTypeId
            $table->string('buy_order_location');
            $table->string('buy_order_quality_level');
            $table->string('buy_order_enchantment_level');
            $table->integer('buy_order_unit_price_silver');
            $table->integer('buy_order_amount');
            $table->timestamp('buy_order_created_at');
            $table->datetime('buy_order_expires');
            $table->boolean('buy_order_is_consumed');
            $table->string('buy_order_server');

            // Sell Order bilgileri
            $table->string('sell_order_id'); // SellOrder ID
            $table->string('sell_order_item_type_id'); // SellOrder itemTypeId
            $table->string('sell_order_location');
            $table->string('sell_order_quality_level');
            $table->string('sell_order_enchantment_level');
            $table->integer('sell_order_unit_price_silver');
            $table->integer('sell_order_amount');
            $table->timestamp('sell_order_created_at');
            $table->datetime('sell_order_expires');
            $table->boolean('sell_order_is_consumed');
            $table->string('sell_order_server');

            // Diğer bilgiler
            $table->timestamp('flip_created_at');
            $table->string('server');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('flips');
    }
}
