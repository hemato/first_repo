<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpgradeResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upgrade_resources', function (Blueprint $table) {
            $table->id();
            $table->string('item_name'); // market_prices tablosundaki item_name ile eşleşecek
            $table->integer('enchantmentlevel'); // enchantment seviyesi
            $table->string('upgraderesource_name'); // Upgrade için gereken resource ismi
            $table->integer('upgraderesource_count'); // Resource sayısı
            $table->timestamps(); // created_at ve updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('upgrade_resources');
    }
}
