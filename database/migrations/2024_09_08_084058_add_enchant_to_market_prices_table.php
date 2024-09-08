<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('market_prices', function (Blueprint $table) {
            $table->integer('enchant')->default(0)->nullable(false); // NULL olmasını engelle ve varsayılan değeri 0 yap
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('market_prices', function (Blueprint $table) {
            $table->dropColumn('enchant');
        });
    }
};
