<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_Images', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name');
            $table->string('adress');
            $table->integer('shop_image_id');
            $table->timestamps();

            $table->primary(['id', 'name', 'adress']);
        });

        Schema::table('shop_Images', function (Blueprint $table) {
            $table->increments('id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shopImages');
    }
}
