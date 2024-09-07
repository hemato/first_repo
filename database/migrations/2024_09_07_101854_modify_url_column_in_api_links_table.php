<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUrlColumnInApiLinksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('api_links', function (Blueprint $table) {
            // 'url' sütununu text olarak değiştir
            $table->text('url')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_links', function (Blueprint $table) {
            // 'url' sütununu orijinal tipine döndür (varchar(255) gibi)
            $table->string('url', 255)->change();
        });
    }
}
