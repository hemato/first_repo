<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('api_links', function (Blueprint $table) {
            $table->string('url', 4084)->change(); // Increase the length to 2048 characters
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_links', function (Blueprint $table) {
            $table->string('url', 255)->change(); // Revert to the original length
        });
    }
};
