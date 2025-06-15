<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('order_items_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_item_id');
            $table->unsignedBigInteger('old_product_id');
            $table->string('old_product_name');
            $table->integer('old_quantity');
            $table->decimal('old_price_unit', 10, 2);
            $table->decimal('old_subtotal', 10, 2);
            $table->unsignedBigInteger('new_product_id');
            $table->string('new_product_name');
            $table->integer('new_quantity');
            $table->decimal('new_price_unit', 10, 2);
            $table->decimal('new_subtotal', 10, 2);
            $table->timestamp('changed_at');
            $table->unsignedBigInteger('changed_by_user_id');
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items_histories');
    }
}
