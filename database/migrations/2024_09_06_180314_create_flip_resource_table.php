<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlipResourceTable extends Migration
{
    public function up()
    {
        Schema::create('flip_resource', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flip_id');
            $table->unsignedBigInteger('resource_id');
            $table->integer('count');
            $table->foreign('flip_id')->references('id')->on('flips')->onDelete('cascade');
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('flip_resource');
    }
}
