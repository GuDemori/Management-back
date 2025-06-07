<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStatusHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->enum('old_status', ['Em espera', 'Preparando', 'À caminho', 'Entregue', 'Cancelado']);
            $table->enum('new_status', ['Em espera', 'Preparando', 'À caminho', 'Entregue', 'Cancelado']);
            $table->timestamp('changed_at');
            $table->unsignedBigInteger('changed_by_user_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_status_histories');
    }
}
